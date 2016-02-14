<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Controller\Account;

use Magento\Customer\Controller\Account\Logout as MagentoLogout;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Scandiweb\SocialLogin\HybridAuth\HybridAuth;

class Logout extends MagentoLogout
{

    /**
     * @var
     */
    protected $hybridAuth;

    /**
     * Logout constructor
     *
     * @param Context    $context
     * @param Session    $customerSession
     * @param HybridAuth $hybridAuth
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        HybridAuth $hybridAuth
    ) {
        $this->hybridAuth = $hybridAuth;

        parent::__construct($context, $customerSession);
    }

    /**
     * Customer logout action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->hybridAuth->logoutAllProviders();
        return parent::execute();
    }

}