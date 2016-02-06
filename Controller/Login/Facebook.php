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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Scandiweb\SocialLogin\HybridAuth\HybridAuth;

class Facebook extends Action
{

    /**
     * @var HybridAuth
     */
    protected $hybridAuth;

    /**
     * Facebook constructor
     *
     * @param Context    $context
     * @param HybridAuth $hybridAuth
     */
    public function __construct(Context $context, HybridAuth $hybridAuth)
    {
        $this->hybridAuth = $hybridAuth;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        try {
            $adapter = $this->hybridAuth->authenticate('facebook');
            var_dump($adapter->getUserProfile());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}