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

use Hybrid_Endpoint;

class Endpoint extends Hybrid_Endpoint
{

    /**
     * Process OpenID realm request
     *
     * @return void
     */
    protected function processOpenidRealm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Store\Model\Store $store */
        $store = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore();
        $url = $store->getUrl(null, ['_secure' => $store->isCurrentlySecure()]);

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $objectManager->get(\Magento\Framework\App\Response\Http::class);
        $response->setRedirect($url);
    }

}