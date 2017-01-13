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
require_once $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-draugiem/Providers/Draugiem.php';

use \Scandiweb\SocialLogin\HybridAuth\AdditionalProviders\Draugiem\DraugiemApi as Api;

class Draugiem extends \Hybrid_Providers_Draugiem
{ 

	/**
	 * IDp wrappers initializer 
	 */
	function initialize() 
	{
		if (!$this->config['keys']['key'] || !$this->config['keys']['secret']) {
			throw new \Exception('Your application key and secret are required in order to connect to ' . $this->providerId . '.', 4);
		}

		//Create Draugiem.lv API object
		$this->api = new Api(
			$this->config['keys']['key'],
			$this->config['keys']['secret']
		);
		
		//Try to authenticate user
		$session = $this->api->getSession();

		//Authentication successful
		if($session){
			//Get user info
			$user = $this->api->getUserData();
			$this->user_id = $user['uid'];
		}
	}

}
