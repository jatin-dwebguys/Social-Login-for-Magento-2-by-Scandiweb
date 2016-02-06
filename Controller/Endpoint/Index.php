<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 16.6.2
 * Time: 15:54
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