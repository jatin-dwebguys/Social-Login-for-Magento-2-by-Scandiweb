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

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Checks whether the provider is enabled
     *
     * @param $provider
     * @return boolean
     */
    public function isProviderEnabled($provider)
    {
        return $this->scopeConfig->isSetFlag(
                'social_login/' . $provider . '/enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            && $this->getProviderApiKey($provider)
            && $this->getProviderApiSecret($provider);
    }

    /**
     * Get provider api key
     *
     * @param $provider
     * @return string
     */
    public function getProviderApiKey($provider)
    {
        return $this->scopeConfig->getValue(
            'social_login/' . $provider . '/api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get provider api secret
     *
     * @param $provider
     * @return string
     */
    public function getProviderApiSecret($provider)
    {
        return $this->scopeConfig->getValue(
            'social_login/' . $provider . '/api_secret',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get provider sort order
     *
     * @param $provider
     * @return mixed
     */
    public function getProviderOrder($provider)
    {
        return $this->scopeConfig->getValue(
            'social_login/' . $provider . '/sort_order',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' . $_SERVER['SERVER_NAME'] : 'http://' . $_SERVER['SERVER_NAME'];

        return $baseUrl . '/sociallogin/endpoint';
    }

}