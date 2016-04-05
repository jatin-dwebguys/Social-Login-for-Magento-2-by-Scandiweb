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
     * Process OpenID realm reques
     *
     * @return void
     */
    protected function processOpenidRealm()
    {
        // TODO: rewrite this method
        $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' . $_SERVER['SERVER_NAME'] : 'http://' . $_SERVER['SERVER_NAME'];
        header('Location: ' . $baseUrl);
    }

}