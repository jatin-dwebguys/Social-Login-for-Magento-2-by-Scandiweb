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

use Scandiweb\SocialLogin\api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository as MagentoCustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepository extends MagentoCustomerRepository implements CustomerRepositoryInterface
{

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
        // TODO: Rewrite this class without using MagentoCustomerRepository
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
}