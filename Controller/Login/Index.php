<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Controller\Login;

use Hybrid_User_Profile;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as MagentoCustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Scandiweb\SocialLogin\Api\CustomerProviderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface;
use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface;
use Scandiweb\SocialLogin\HybridAuth\HybridAuth;
use Exception;
use Scandiweb\SocialLogin\Logger\Logger;

class Index extends Action
{

    /**
     * @var string
     */
    protected $provider;

    /**
     * @var HybridAuth
     */
    protected $hybridAuth;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerProviderRepositoryInterface
     */
    protected $customerProviderRepository;

    /**
     * @var MagentoCustomerRepositoryInterface
     */
    protected $magentoCustomerRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var CustomerProviderInterface
     */
    protected $customerProvider;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Facebook constructor
     *
     * @param Context                             $context
     * @param HybridAuth                          $hybridAuth
     * @param CustomerRepositoryInterface         $customerRepository
     * @param CustomerProviderRepositoryInterface $customerProviderRepository
     * @param MagentoCustomerRepositoryInterface  $magentoCustomerRepository
     * @param CustomerSession                     $customerSession
     * @param AccountManagementInterface          $accountManagement
     * @param CustomerInterface                   $customer
     * @param CustomerProviderInterface           $customerProvider
     * @param Logger                              $logger
     */
    public function __construct(
        Context $context,
        HybridAuth $hybridAuth,
        CustomerRepositoryInterface $customerRepository,
        CustomerProviderRepositoryInterface $customerProviderRepository,
        MagentoCustomerRepositoryInterface $magentoCustomerRepository,
        CustomerSession $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider,
        Logger $logger
    ) {
        $this->hybridAuth = $hybridAuth;
        $this->customerRepository = $customerRepository;
        $this->customerProviderRepository = $customerProviderRepository;
        $this->magentoCustomerRepository = $magentoCustomerRepository;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->customer = $customer;
        $this->customerProvider = $customerProvider;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->provider = $this->getRequest()->getParam('provider');
        $redirect = $this->_redirect->getRefererUrl();

        try {
            $adapter = $this->hybridAuth->authenticate($this->provider);
            /** @var $user Hybrid_User_Profile */
            $user = $adapter->getUserProfile();

            $customer = $this->customerRepository->getByProvider(
                $user->identifier,
                $this->provider
            );

            if (!is_null($customer)) {
                $this->login($customer->getId());

                $this->messageManager->addSuccess(__(
                    "You have successfully logged in using your %1 account.", ucfirst($this->provider)
                ));
            } else {
                try {
                    $this->customer = $this->magentoCustomerRepository->get($user->email);
                } catch (NoSuchEntityException $e) {}

                $customer = $this->create($user);

                if ($this->customer->getId() == $customer->getId()) {
                    $this->messageManager->addSuccess(__(
                        "We have discovered you already have an account at our store."
                        . " Your %1 account is now connected to your store account.", ucfirst($this->provider)
                    ));
                } else {
                    $this->messageManager->addSuccess(__(
                        "Your %1 account is now connected to your new user account at our store.", ucfirst($this->provider)
                    ));

                    if (!$user->email || !$user->firstName || !$user->lastName) {
                        $this->messageManager->addWarning(__(
                            'Not all data were obtained from the social network. Please correct your personal data.',
                            $this->_url->getUrl('customer/account/edit')
                        ));

                        $redirect = 'customer/account/edit';
                    }
                }

                $this->login($customer->getId());
            }
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (InputException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());

            $this->messageManager->addError(__(
                "Oops. Something went wrong! Please try again later."
            ));
        }

        $this->_redirect($redirect);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return $this|ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        return parent::dispatch($request);
    }

    /**
     * Authorization customer by id
     *
     * @param int $customerId
     */
    private function login($customerId)
    {
        $this->customerSession->loginById($customerId);
        $this->customerSession->regenerateId();
    }

    /**
     * Create user by using data from provider
     *
     * @param Hybrid_User_Profile $user
     * @return CustomerInterface
     */
    private function create(Hybrid_User_Profile $user)
    {
        if (!$this->customer->getId()) {
            if ($user->email) {
                $this->customer->setEmail($user->email);
            } else {
                $fakeEmail = $user->identifier . '@' . $this->provider . '.com';
                $this->customer->setEmail($fakeEmail);
            }

            if ($user->firstName) {
                $this->customer->setFirstname($user->firstName);
            } else {
                $this->customer->setFirstname('Firstname');
            }

            if ($user->lastName) {
                $this->customer->setLastname($user->lastName);
            } else {
                $this->customer->setLastname('Lastname');
            }
        }

        if (!$this->customer->getId()) {
            $customer = $this->accountManagement->createAccount($this->customer);
        } else {
            $customer = $this->magentoCustomerRepository->save($this->customer);
        }

        $this->customerProvider->setEntityId($customer->getId());
        $this->customerProvider->setUserId($user->identifier);
        $this->customerProvider->setProvider($this->provider);

        $this->customerProviderRepository->save($this->customerProvider);

        return $customer;
    }
}