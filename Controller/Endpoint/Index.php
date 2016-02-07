<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Controller\Endpoint;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Scandiweb\SocialLogin\HybridAuth\Endpoint;

class Index extends Action
{

    /**
     * @var Endpoint
     */
    private $endpoint;

    public function __construct(Context $context, Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return void
     */
    public function execute()
    {
        $this->endpoint->process();
    }
}