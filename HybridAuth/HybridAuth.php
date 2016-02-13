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
use Exception;

class HybridAuth extends Hybrid_Auth
{

    /**
     * Providers
     */
    const FACEBOOK  = 'facebook';
    const TWITTER   = 'twitter';
    const GOOGLE    = 'google';
    const INSTAGRAM = 'instagram';
    const YAHOO     = 'yahoo';

    /**
     * @var Config
     */
    protected static $hybridConfig;

    /**
     * HybridAuth constructor
     *
     * @param Config       $config
     * @param UrlInterface $url
     */
    public function __construct(Config $config, UrlInterface $url)
    {
       HybridAuth::$hybridConfig = $config;

        $vendorPath = require BP . '/app/etc/vendor_path.php';
        $vendorPath = BP . "/{$vendorPath}/";

        try {
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
                    ],
                    ucfirst(self::GOOGLE) => [
                        'enabled' => $config->isProviderEnabled(self::GOOGLE),
                        'keys'    => [
                            'id'     => $config->getProviderApiKey(self::GOOGLE),
                            'secret' => $config->getProviderApiSecret(self::GOOGLE)
                        ]
                    ],
                    ucfirst(self::INSTAGRAM) => [
                        'enabled' => $config->isProviderEnabled(self::INSTAGRAM),
                        'keys'    => [
                            'id'     => $config->getProviderApiKey(self::INSTAGRAM),
                            'secret' => $config->getProviderApiSecret(self::INSTAGRAM)
                        ],
                        'wrapper' => [
                            'path'  => $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-instagram/Providers/Instagram.php',
                            'class' => 'Hybrid_Providers_Instagram'
                        ]
                    ],
                    ucfirst(self::YAHOO) => [
                        'enabled' => $config->isProviderEnabled(self::YAHOO),
                        'keys'    => [
                            'key'    => $config->getProviderApiKey(self::YAHOO),
                            'secret' => $config->getProviderApiSecret(self::YAHOO)
                        ]
                    ]
                ]
            ]);
        } catch (Exception $e) {
            // Kludge, but corrects error - Undefined index: oauth_token in OAuth1Client.php
            // Throws a exception: "Notice: Undefined index: providers"
            // TODO: Rewrite this using a more better solution
            parent::__construct([]);
        }
    }

    /**
     * Return array listing all enabled providers
     *
     * @return array
     */
    public static function getProviders()
    {
        $providers = parent::getProviders();

        foreach ($providers as $key => $provider) {
            $providers[$key]['order'] = HybridAuth::$hybridConfig->getProviderOrder(strtolower($key));
        }

        return $providers;
    }

}