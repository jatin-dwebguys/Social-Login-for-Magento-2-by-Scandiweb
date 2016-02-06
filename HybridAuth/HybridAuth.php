<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\HybridAuth;

use Hybrid_Auth;
use Magento\Framework\UrlInterface;

class HybridAuth extends Hybrid_Auth
{

    /**
     * HybridAuth constructor
     *
     * @param Config       $config
     * @param UrlInterface $url
     */
    public function __construct(Config $config, UrlInterface $url)
    {
        parent::__construct([
            'base_url' => $url->getBaseUrl() . '/sociallogin/endpoint',
            'providers' => [
                'Facebook' => [
                    'enabled' => $config->isProviderEnabled('facebook'),
                    'keys'    => [
                        'id'     => $config->getProviderApiKey('facebook'),
                        'secret' => $config->getProviderApiSecret('facebook')
                    ],
                    'scope'   => 'email'
                ]
            ]
        ]);
    }

}