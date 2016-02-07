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
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface;
use Scandiweb\SocialLogin\HybridAuth\HybridAuth;

abstract class AbstractProviderAction extends Action
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
     * Facebook constructor
     *
     * @param Context                     $context
     * @param HybridAuth                  $hybridAuth
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession             $customerSession
     * @param AccountManagementInterface  $accountManagement
     * @param CustomerInterface           $customer
     */
    public function __construct(
        Context $context,
        HybridAuth $hybridAuth,
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerInterface $customer
    ) {
        $this->hybridAuth = $hybridAuth;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->customer = $customer;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        try {
            $adapter = $this->hybridAuth->authenticate($this->provider);
            /** @var $user Hybrid_User_Profile */
            $user = $adapter->getUserProfile();

            $customer = $this->customerRepository->getByProviderIdAndName(
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
                    $this->customer = $this->customerRepository->get($user->email);
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
                    }

                    $this->login($customer->getId());
                }
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit;
        }

        $this->_redirect($this->_redirect->getRefererUrl());
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
            $this->customer->setEmail($facebookUser->email);
            $this->customer->setFirstname($facebookUser->firstName);
            $this->customer->setLastname($facebookUser->lastName);
        }
        $this->customer->setCustomAttribute('scandi_provider_user_id', $facebookUser->identifier);
        $this->customer->setCustomAttribute('scandi_provider_name', $this->provider);

        if ($this->customer->getId()) {
            $customer = $this->customerRepository->save($this->customer);
        } else {
            $customer = $this->accountManagement->createAccount($this->customer);
        }

        return $customer;
    }
}