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

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Scandiweb\SocialLogin\Api\CustomerProviderRepositoryInterface;
use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface;
use Scandiweb\SocialLogin\Model\CustomerProviderFactory;

class CustomerProviderRepository implements CustomerProviderRepositoryInterface
{

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var CustomerProviderFactory
     */
    protected $customerProviderFactory;

    public function __construct(ExtensibleDataObjectConverter $extensibleDataObjectConverter, CustomerProviderFactory $customerProviderFactory)
    {
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerProviderFactory = $customerProviderFactory;
    }

    /**
     * Save customer provider
     *
     * @param CustomerProviderInterface $customerProvider
     * @return CustomerProviderInterface
     */
    public function save(CustomerProviderInterface $customerProvider)
    {
        $customerProviderData = $this->extensibleDataObjectConverter->toNestedArray(
            $customerProvider,
            [],
            '\Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface'
        );

        /** @var \Scandiweb\SocialLogin\Model\CustomerProvider $customerModel */
        $customerModel = $this->customerProviderFactory->create();
        $customerModel->setData($customerProviderData);
        $customerModel->save();

        return $customerModel->getDataModel();
    }
}