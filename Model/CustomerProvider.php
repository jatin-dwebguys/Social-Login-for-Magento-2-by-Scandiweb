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

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface;
use Scandiweb\SocialLogin\Model\ResourceModel\CustomerProvider as Resource;
use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterfaceFactory;

class CustomerProvider extends AbstractModel
{

    /**
     * @var CustomerProviderInterfaceFactory
     */
    protected $customerProviderDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * CustomerProvider constructor
     *
     * @param Context                          $context
     * @param Registry                         $registry
     * @param Resource                         $resource
     * @param AbstractDb                       $resourceCollection
     * @param array                            $data
     * @param CustomerProviderInterfaceFactory $customerProviderDataFactory
     * @param DataObjectHelper                 $dataObjectHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerProviderInterfaceFactory $customerProviderDataFactory,
        DataObjectHelper $dataObjectHelper,
        Resource $resource,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerProviderDataFactory = $customerProviderDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Scandiweb\SocialLogin\Model\ResourceModel\CustomerProvider');
    }

    /**
     * Retrieve customer provider model
     *
     * @return CustomerProviderInterface
     */
    public function getDataModel()
    {
        $customerProviderData = $this->getData();
        $customerProviderDataObject = $this->customerProviderDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $customerProviderDataObject,
            $customerProviderData,
            '\Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface'
        );

        return $customerProviderDataObject;
    }

}