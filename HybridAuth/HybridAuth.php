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
    const FACEBOOK      = 'facebook';
    const TWITTER       = 'twitter';
    const GOOGLE        = 'google';
    const INSTAGRAM     = 'instagram';
    const YAHOO         = 'yahoo';
    const LINKEDIN      = 'linkedin';
    const WINDOWS_LIVE  = 'live';
    const VKONTAKTE     = 'vkontakte';
    const DRAUGIEM      = 'draugiem';
    const STRAVA        = 'strava';

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
                'base_url' => $config->getBaseUrl(),
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
                    ],
                    'LinkedIn' => [
                        'enabled' => $config->isProviderEnabled(self::LINKEDIN),
                        'keys'    => [
                            'key'    => $config->getProviderApiKey(self::LINKEDIN),
                            'secret' => $config->getProviderApiSecret(self::LINKEDIN)
                        ]
                    ],
                    ucfirst(self::WINDOWS_LIVE) => [
                        'enabled' => $config->isProviderEnabled(self::WINDOWS_LIVE),
                        'keys'    => [
                            'id'     => $config->getProviderApiKey(self::WINDOWS_LIVE),
                            'secret' => $config->getProviderApiSecret(self::WINDOWS_LIVE)
                        ]
                    ],
                    ucfirst(self::VKONTAKTE) => [
                        'enabled' => $config->isProviderEnabled(self::VKONTAKTE),
                        'keys'    => [
                            'id'     => $config->getProviderApiKey(self::VKONTAKTE),
                            'secret' => $config->getProviderApiSecret(self::VKONTAKTE)
                        ],
                        'wrapper' => [
                            'path'  => $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-vkontakte/Providers/Vkontakte.php',
                            'class' => 'Hybrid_Providers_Vkontakte'
                        ]
                    ],
                    ucfirst(self::DRAUGIEM) => [
                        'enabled' => $config->isProviderEnabled(self::DRAUGIEM),
                        'keys'    => [
                            'key'     => $config->getProviderApiKey(self::DRAUGIEM),
                            'secret'  => $config->getProviderApiSecret(self::DRAUGIEM)
                        ],
                        'wrapper' => [
                            'path'  => __DIR__ . '/AdditionalProviders/Draugiem/Draugiem.php',
                            'class' => \Scandiweb\SocialLogin\HybridAuth\AdditionalProviders\Draugiem\Draugiem::class
                        ]
                    ],
                    ucfirst(self::STRAVA) => [
                        'enabled' => $config->isProviderEnabled(self::STRAVA),
                        'keys'    => [
                            'key'     => $config->getProviderApiKey(self::STRAVA),
                            'secret'  => $config->getProviderApiSecret(self::STRAVA)
                        ],
                        'wrapper' => [
                            'path'  => $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-strava/Providers/strava.php',
                            'class' => 'Hybrid_Providers_Strava'
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