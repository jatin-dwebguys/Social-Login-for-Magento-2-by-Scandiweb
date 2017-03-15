<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2017 Scandiweb, Ltd (http://scandiweb.com)
 */
namespace Scandiweb\SocialLogin\HybridAuth\AdditionalProviders\Strava;

$vendorPath = require BP . '/app/etc/vendor_path.php';
$vendorPath = BP . "/{$vendorPath}/";
require_once $vendorPath . 'hybridauth/hybridauth/additional-providers/hybridauth-strava/Providers/strava.php';

class Strava extends \Hybrid_Providers_Strava
{

    /**
     * Load the user profile from the IDp api client
     *
     * @return \Hybrid_User_Profile
     * @throws \Exception
     */
    function getUserProfile()
    {
        $this->user->profile = parent::getUserProfile();

        $data = $this->api->get("athlete");
        if (!isset($data->id)){
            throw new \Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }

        $this->user->profile->firstName = $data->firstname;
        $this->user->profile->lastName = $data->lastname;

        return $this->user->profile;
    }

}