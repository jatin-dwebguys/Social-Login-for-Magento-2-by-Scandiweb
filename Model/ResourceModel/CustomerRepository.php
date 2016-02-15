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

use Magento\Customer\Api\CustomerRepositoryInterface as MagentoCustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Scandiweb\SocialLogin\Api\CustomerRepositoryInterface;
use Scandiweb\SocialLogin\Model\CustomerProviderFactory;

class CustomerRepository implements CustomerRepositoryInterface
{

    /**
     * @var CustomerProviderFactory
     */
    protected $customerProviderFactory;

    /**
     * @var MagentoCustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CustomerRepository constructor
     *
     * @param CustomerProviderFactory            $customerProviderFactory
     * @param MagentoCustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface              $storeManager
     */
    public function __construct(
        CustomerProviderFactory $customerProviderFactory,
        MagentoCustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->customerProviderFactory = $customerProviderFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve customer by provider
     *
     * @param int    $id
     * @param string $provider
     *
     * @return CustomerInterface | null
     */
    public function getByProvider($id, $provider, $websiteId = null)
    {
        if (!$websiteId) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        /** @var \Scandiweb\SocialLogin\Model\ResourceModel\CustomerProvider\Collection $collection */
        $collection = $this->customerProviderFactory->create()->getCollection();

        /** @var \Scandiweb\SocialLogin\Model\CustomerProvider $customerProvider */
        $providers = $collection
            ->addFieldToSelect('*')
            ->addFieldToFilter('user_id', $id)
            ->addFieldToFilter('provider', $provider)
            ->load();

        foreach ($providers as $provider) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->getById($provider->getEntityId());

            if ($customer->getId() && $customer->getWebsiteId() == $websiteId) {
                return $customer;
            }
        }
        
        return null;
    }

}