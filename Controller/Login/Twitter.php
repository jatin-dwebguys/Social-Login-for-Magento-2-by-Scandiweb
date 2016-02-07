<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Controller\Login;

use Magento\Framework\App\ResponseInterface;

class Twitter extends AbstractProviderAction
{

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->provider = 'twitter';

        parent::execute();
    }

}