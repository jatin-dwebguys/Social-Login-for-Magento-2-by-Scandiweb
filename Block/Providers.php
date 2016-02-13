<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Scandiweb\SocialLogin\HybridAuth\HybridAuth;

class Providers extends Template
{

    /**
     * @var HybridAuth
     */
    protected $hybridAuth;

    /**
     * Providers constructor.
     *
     * @param Context    $context
     * @param array      $data
     * @param HybridAuth $hybridAuth
     */
    public function __construct(Context $context, array $data, HybridAuth $hybridAuth)
    {
        $this->hybridAuth = $hybridAuth;

        parent::__construct($context, $data);
    }

    /**
     * Get all enabled providers
     *
     * @return array
     */
    public function getProviders()
    {
        $providers = [];
        $hybridProviders = $this->hybridAuth->getProviders();

        foreach ($hybridProviders as $key => $provider) {
            $providers[strtolower($key)]['url'] = $this->getUrl('sociallogin/login', ['provider' => strtolower($key)]);
        }

        return $providers;
    }

}