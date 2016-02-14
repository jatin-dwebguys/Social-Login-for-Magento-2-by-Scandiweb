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

use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface;

interface CustomerProviderRepositoryInterface
{

    /**
     * Save customer provider
     *
     * @param CustomerProviderInterface $customerProvider
     * @return CustomerProviderInterface
     */
    public function save(CustomerProviderInterface $customerProvider);

}