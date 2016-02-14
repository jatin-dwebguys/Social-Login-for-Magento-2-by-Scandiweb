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

interface CustomerRepositoryInterface
{

    /**
     * Retrieve customer by provider
     *
     * @param int $id
     * @param string $provider
     * @return CustomerInterface | null
     */
    public function getByProvider($id, $provider);

}