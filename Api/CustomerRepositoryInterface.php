<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as MagentoCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends MagentoCustomerRepositoryInterface
{

    /**
     * Retrieve customer by user provider id and provider name
     *
     * @param int $id
     * @param string $provider
     * @return CustomerInterface | null
     */
    public function getByProviderIdAndName($id, $provider);

}