<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\AccountManagement as MagentoAccountManagement;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Scandiweb\SocialLogin\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface as CustomCustomerRepositoryInterface;

class AccountManagement extends MagentoAccountManagement implements AccountManagementInterface
{

    /**
    * @var CustomerRepositoryInterface
    */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var CustomCustomerRepositoryInterface
     */
    private $customCustomerRepository;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * AccountManagement constructor
     *
     * @param CustomerFactory                   $customerFactory
     * @param ManagerInterface                  $eventManager
     * @param StoreManagerInterface             $storeManager
     * @param Random                            $mathRandom
     * @param Validator                         $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface        $addressRepository
     * @param CustomerMetadataInterface         $customerMetadataService
     * @param CustomerRegistry                  $customerRegistry
     * @param PsrLogger                         $logger
     * @param Encryptor                         $encryptor
     * @param ConfigShare                       $configShare
     * @param StringHelper                      $stringHelper
     * @param CustomerRepositoryInterface       $customerRepository
     * @param ScopeConfigInterface              $scopeConfig
     * @param TransportBuilder                  $transportBuilder
     * @param DataObjectProcessor               $dataProcessor
     * @param Registry                          $registry
     * @param CustomerViewHelper                $customerViewHelper
     * @param DateTime                          $dateTime
     * @param Customer                          $customerModel
     * @param ObjectFactory                     $objectFactory
     * @param ExtensibleDataObjectConverter     $extensibleDataObjectConverter
     * @param CustomCustomerRepositoryInterface $customCustomerRepository
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        Customer $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CustomCustomerRepositoryInterface $customCustomerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->mathRandom = $mathRandom;
        $this->customCustomerRepository = $customCustomerRepository;
        $this->customerRegistry = $customerRegistry;

        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter
        );
    }

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @api
     * @param CustomerInterface $customer
     * @param string $password
     * @param string $redirectUrl
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function createAccount(CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
        if ($password !== null) {
            $this->checkPasswordStrength($password);
            $hash = $this->createPasswordHash($password);
        } else {
            $hash = null;
        }

        return $this->createAccountWithPasswordHash($customer, $hash, $redirectUrl);
    }

    /**
     * Create customer account using provided hashed password. Should not be exposed as a webapi.
     *
     * @api
     * @param CustomerInterface $customer
     * @param string $hash Password hash that we can save directly
     * @param string $redirectUrl URL fed to welcome email templates. Can be used by templates to, for example, direct
     *                            the customer to a product they were looking at after pressing confirmation link.
     * @return CustomerInterface
     * @throws InputException If bad input is provided
     * @throws InputMismatchException If the provided email is already used
     * @throws LocalizedException
     */
    public function createAccountWithPasswordHash(CustomerInterface $customer, $hash, $redirectUrl = '')
    {
        // This logic allows an existing customer to be added to a different store.  No new account is created.
        // The plan is to move this logic into a new method called something like 'registerAccountWithStore'
        if ($customer->getId()) {
            $customer = $this->customCustomerRepository->get($customer->getEmail());
            $websiteId = $customer->getWebsiteId();

            if ($this->isCustomerInStore($websiteId, $customer->getStoreId())) {
                throw new InputException(__('This customer already exists in this store.'));
            }
            // Existing password hash will be used from secured customer data registry when saving customer
        }
        // Make sure we have a storeId to associate this customer with.
        if (!$customer->getStoreId()) {
            if ($customer->getWebsiteId()) {
                $storeId = $this->storeManager->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            } else {
                $storeId = $this->storeManager->getStore()->getId();
            }

            $customer->setStoreId($storeId);
        }

        // Update 'created_in' value with actual store name
        if ($customer->getId() === null) {
            $storeName = $this->storeManager->getStore($customer->getStoreId())
                ->getName();
            $customer->setCreatedIn($storeName);
        }

        $customerAddresses = $customer->getAddresses() ?: [];
        $customer->setAddresses(null);
        try {
            // If customer exists existing hash will be used by Repository
            $customer = $this->customCustomerRepository->save($customer, $hash);
        } catch (AlreadyExistsException $e) {
            throw new InputMismatchException(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (LocalizedException $e) {
            throw $e;
        }
        try {
            foreach ($customerAddresses as $address) {
                $address->setCustomerId($customer->getId());
                $this->addressRepository->save($address);
            }
        } catch (InputException $e) {
            $this->customerRepository->delete($customer);
            throw $e;
        }
        $customer = $this->customerRepository->getById($customer->getId());
        $newLinkToken = $this->mathRandom->getUniqueHash();
        $this->changeResetPasswordLinkToken($customer, $newLinkToken);
        $this->sendEmailConfirmation($customer, $redirectUrl);

        return $customer;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param CustomerInterface $customer
     * @param string $passwordLinkToken
     * @return bool
     * @throws InputException
     */
    public function changeResetPasswordLinkToken($customer, $passwordLinkToken)
    {
        if (!is_string($passwordLinkToken) || empty($passwordLinkToken)) {
            throw new InputException(
                __(
                    InputException::INVALID_FIELD_VALUE,
                    ['value' => $passwordLinkToken, 'fieldName' => 'password reset token']
                )
            );
        }

        if (is_string($passwordLinkToken) && !empty($passwordLinkToken)) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerSecure->setRpToken($passwordLinkToken);
            $customerSecure->setRpTokenCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
            $this->customCustomerRepository->save($customer);
        }

        return true;
    }

}