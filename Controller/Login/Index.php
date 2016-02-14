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
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface;
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
     * @var Logger
     */
    protected $logger;

    /**
     * Facebook constructor
     *
     * @param Context                            $context
     * @param HybridAuth                         $hybridAuth
     * @param CustomerRepositoryInterface        $customerRepository
     * @param MagentoCustomerRepositoryInterface $magentoCustomerRepository
     * @param CustomerSession                    $customerSession
     * @param AccountManagementInterface         $accountManagement
     * @param CustomerInterface                  $customer
     * @param Logger                             $logger
     */
    public function __construct(
        Context $context,
        HybridAuth $hybridAuth,
        CustomerRepositoryInterface $customerRepository,
        MagentoCustomerRepositoryInterface $magentoCustomerRepository,
        CustomerSession $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerInterface $customer,
        Logger $logger
    ) {
        $this->hybridAuth = $hybridAuth;
        $this->customerRepository = $customerRepository;
        $this->magentoCustomerRepository = $magentoCustomerRepository;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->customer = $customer;
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
                    $this->customer = $this->magentoCustomerRepository->get('viktor.vipolzov@gmail.com');
                } finally {
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
                                'Not all data were obtained from the social network. Please correct your personal data on <a href="%1">account information</a> page.',
                                $this->_url->getUrl('customer/account/edit')
                            ));
                        }
                    }

                    $this->login($customer->getId());
                }
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

        $this->_redirect($this->_redirect->getRefererUrl());
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
     * @param Hybrid_User_Profile $facebookUser
     * @return CustomerInterface
     */
    private function create(Hybrid_User_Profile $facebookUser)
    {
        if (!$this->customer->getId()) {
            if ($facebookUser->email) {
                $this->customer->setEmail($facebookUser->email);
            } else {
                $fakeEmail = $facebookUser->identifier . '@' . $this->provider . '.com';
                $this->customer->setEmail($fakeEmail);
            }

            if ($facebookUser->firstName) {
                $this->customer->setFirstname($facebookUser->firstName);
            } else {
                $this->customer->setFirstname($this->provider . 'Firstname');
            }

            if ($facebookUser->lastName) {
                $this->customer->setLastname($facebookUser->lastName);
            } else {
                $this->customer->setLastname($this->provider . 'Lastname');
            }
        }

        if (!$this->customer->getId()) {
            return $this->accountManagement->createAccount($this->customer);
        }

        return $this->magentoCustomerRepository->save($this->customer);
    }
}