<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 */
namespace Scandiweb\SocialLogin\HybridAuth\AdditionalProviders\Draugiem;

$vendorPath = require BP . '/app/etc/vendor_path.php';
$vendorPath = BP . "/{$vendorPath}/";
require_once $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-draugiem/thirdparty/Draugiem/DraugiemApi.php';

class DraugiemApi extends \Draugiem_Api
{
}