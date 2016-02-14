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
class CustomerRepository extends MagentoCustomerRepository implements CustomerRepositoryInterface
{

    /**
     * Retrieve customer by provider
     *
     * @param int    $id
     * @param string $provider
     *
     * @return CustomerInterface | null
     */
    public function getByProvider($id, $provider)
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

}