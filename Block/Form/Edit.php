<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Block\Form;

use Magento\Customer\Block\Form\Edit as MagentoEdit;

class Edit extends MagentoEdit
{

    /**
     * Get customer password
     *
     * @return string
     */
    public function getPassword()
    {
        $customer = $this->customerSession->getCustomer();
        $password = $customer->getPasswordHash();

        return $password;
    }

    /**
     * Get new password link
     *
     * @return string
     */
    public function getNewPasswordLink()
    {
        $customer = $this->customerSession->getCustomer();

        return $this->getUrl('customer/account/createPassword', [
            'id' => $customer->getId(),
            'token' => $customer->getData('rp_token')
        ]);
    }

}