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
     * Providers
     */
    const FACEBOOK = 'facebook';
    const TWITTER = 'twitter';

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
                ucfirst(self::FACEBOOK) => [
                    'enabled' => $config->isProviderEnabled(self::FACEBOOK),
                    'keys'    => [
                        'id' => $config->getProviderApiKey(self::FACEBOOK),
                        'secret' => $config->getProviderApiSecret(
                            self::FACEBOOK
                        )
                    ],
                    'scope' => 'email'
                ],
                ucfirst(self::TWITTER) => [
                    'enabled' => $config->isProviderEnabled(self::TWITTER),
                    'keys'    => [
                        'key'    => $config->getProviderApiKey(self::TWITTER),
                        'secret' => $config->getProviderApiSecret(self::TWITTER)
                    ],
                    'includeEmail' => true
                ]
            ]
        ]);
    }

}