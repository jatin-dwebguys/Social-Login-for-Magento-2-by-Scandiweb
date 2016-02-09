<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/sociallogin.log';
}