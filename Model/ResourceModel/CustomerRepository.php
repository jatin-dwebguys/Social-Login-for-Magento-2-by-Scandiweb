<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Model\ResourceModel;

use Magento\Backend\Model\Menu\Builder\Command\Add;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecureFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository as MagentoCustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterface as MagentoCustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageProcessorInterface;
use Scandiweb\SocialLogin\Model\CustomerFactory as CustomCustomerFactory;
use Zend\EventManager\EventManager;

// TODO: Rewrite this class without using MagentoCustomerRepository
class CustomerRepository implements CustomerRepositoryInterface
{

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageProcessorInterface
     */
    private $imageProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var CustomerMetadataInterface
     */
    private $customerMetadata;

    /**
     * @var CustomCustomerFactory
     */
    private $customCustomerFactory;

    /**
     * CustomerRepository constructor.
     *
     * @param CustomerFactory                       $customerFactory
     * @param CustomerSecureFactory                 $customerSecureFactory
     * @param CustomerRegistry                      $customerRegistry
     * @param AddressRepository                     $addressRepository
     * @param Customer                              $customerResourceModel
     * @param CustomerMetadataInterface             $customerMetadata
     * @param CustomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param ManagerInterface                      $eventManager
     * @param StoreManagerInterface                 $storeManager
     * @param ExtensibleDataObjectConverter         $extensibleDataObjectConverter
     * @param DataObjectHelper                      $dataObjectHelper
     * @param ImageProcessorInterface               $imageProcessor
     * @param JoinProcessorInterface                $extensionAttributesJoinProcessor
     * @param CustomCustomerFactory                 $customCustomerFactory
     */
    public function __construct(
        CustomerFactory $customerFactory,
        CustomerSecureFactory $customerSecureFactory,
        CustomerRegistry $customerRegistry,
        AddressRepository $addressRepository,
        Customer $customerResourceModel,
        CustomerMetadataInterface $customerMetadata,
        CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        ImageProcessorInterface $imageProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CustomCustomerFactory $customCustomerFactory
    ) {
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->imageProcessor = $imageProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerRegistry = $customerRegistry;
        $this->addressRepository = $addressRepository;
        $this->eventManager = $eventManager;
        $this->customerMetadata = $customerMetadata;
        $this->customCustomerFactory = $customCustomerFactory;
    }

    /**
     * Retrieve customer by user provider id and provider name
     *
     * @param int    $id
     * @param string $provider
     *
     * @return CustomerInterface | null
     */
    public function getByProviderIdAndName($id, $provider)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerFactory->create()->getCollection();

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $collection
            ->addFieldToSelect('*')
            ->addAttributeToSelect('scandi_provider_user_id')
            ->addAttributeToSelect('scandi_provider_name')
            ->addFieldToFilter('website_id', $this->storeManager->getWebsite()->getId())
            ->addAttributeToFilter('scandi_provider_user_id', $id)
            ->addAttributeToFilter('scandi_provider_name', $provider)
            ->load()
            ->getFirstItem();

        if ($customer->getId()) {
            return $customer->getDataModel();
        }

        return null;
    }

    /**
     * @param MagentoCustomerInterface $customer
     * @param string|null              $passwordHash
     *
     * @return MagentoCustomerInterface
     */
    public function save(CustomerInterface $customer, $passwordHash = null)
    {
        $this->validate($customer);

        $prevCustomerData = null;
        if ($customer->getId()) {
            $prevCustomerData = $this->getById($customer->getId());
        }
        $customer = $this->imageProcessor->save(
            $customer,
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            $prevCustomerData
        );

        $origAddresses = $customer->getAddresses();
        $customer->setAddresses([]);
        $customerData = $this->extensibleDataObjectConverter->toNestedArray(
            $customer,
            [],
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        $customer->setAddresses($origAddresses);
        $customerModel = $this->customCustomerFactory->create(['data' => $customerData]);

        $storeId = $customerModel->getStoreId();
        if ($storeId === null) {
            $customerModel->setStoreId($this->storeManager->getStore()->getId());
        }
        $customerModel->setId($customer->getId());

        // Need to use attribute set or future updates can cause data loss
        if (!$customerModel->getAttributeSetId()) {
            $customerModel->setAttributeSetId(
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
            );
        }
        // Populate model with secure data
        if ($customer->getId()) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
            $customerModel->setRpToken($customerSecure->getRpToken());
            $customerModel->setRpTokenCreatedAt($customerSecure->getRpTokenCreatedAt());
            $customerModel->setPasswordHash($customerSecure->getPasswordHash());
        } else {
            if ($passwordHash) {
                $customerModel->setPasswordHash($passwordHash);
            }
        }

        // If customer email was changed, reset RpToken info
        if ($prevCustomerData
            && $prevCustomerData->getEmail() !== $customerModel->getEmail()
        ) {
            $customerModel->setRpToken(null);
            $customerModel->setRpTokenCreatedAt(null);
        }
        $customerModel->save();
        $this->customerRegistry->push($customerModel);
        $customerId = $customerModel->getId();

        if ($customer->getAddresses() !== null) {
            if ($customer->getId()) {
                $existingAddresses = $this->getById($customer->getId())->getAddresses();
                $getIdFunc = function ($address) {
                    return $address->getId();
                };
                $existingAddressIds = array_map($getIdFunc, $existingAddresses);
            } else {
                $existingAddressIds = [];
            }

            $savedAddressIds = [];
            foreach ($customer->getAddresses() as $address) {
                $address->setCustomerId($customerId)
                    ->setRegion($address->getRegion());
                $this->addressRepository->save($address);
                if ($address->getId()) {
                    $savedAddressIds[] = $address->getId();
                }
            }

            $addressIdsToDelete = array_diff($existingAddressIds, $savedAddressIds);
            foreach ($addressIdsToDelete as $addressId) {
                $this->addressRepository->deleteById($addressId);
            }
        }

        $savedCustomer = $this->get($customer->getEmail(), $customer->getWebsiteId());
        $this->eventManager->dispatch(
            'customer_save_after_data_object',
            ['customer_data_object' => $savedCustomer, 'orig_customer_data_object' => $customer]
        );

        return $savedCustomer;
    }

    /**
     * Retrieve customer
     *
     * @api
     * @param string $email
     * @param int|null $websiteId
     * @return CustomerInterface
     * @throws NoSuchEntityException If customer with the specified email does not exist.
     * @throws LocalizedException
     */
    public function get($email, $websiteId = null)
    {
        $customerModel = $this->customerRegistry->retrieveByEmail($email, $websiteId);
        return $customerModel->getDataModel();
    }

    /**
     * Validate customer attribute values
     *
     * @param MagentoCustomerInterface $customer
     *
     * @throws InputException
     * @return void
     */
    private function validate(MagentoCustomerInterface $customer) {
        $exception = new InputException();

        $isEmailAddress = \Zend_Validate::is(
            $customer->getEmail(),
            'EmailAddress'
        );

        if (!$isEmailAddress) {
            $exception->addError(__(
                InputException::INVALID_FIELD_VALUE,
                ['fieldName' => 'email', 'value' => $customer->getEmail()]
            ));
        }

        $dob = $this->getAttributeMetadata('dob');
        if ($dob !== null && $dob->isRequired() && '' == trim($customer->getDob())) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'dob']));
        }

        $taxvat = $this->getAttributeMetadata('taxvat');
        if ($taxvat !== null && $taxvat->isRequired() && '' == trim($customer->getTaxvat())) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'taxvat']));
        }

        $gender = $this->getAttributeMetadata('gender');
        if ($gender !== null && $gender->isRequired() && '' == trim($customer->getGender())) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'gender']));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Get attribute metadata.
     *
     * @param string $attributeCode
     *
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface|null
     */
    private function getAttributeMetadata($attributeCode)
    {
        try {
            return $this->customerMetadata->getAttributeMetadata($attributeCode);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}