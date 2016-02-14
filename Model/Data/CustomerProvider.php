<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Model\Data;

use Scandiweb\SocialLogin\Api\Data\CustomerProviderInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class CustomerProvider extends AbstractExtensibleObject implements CustomerProviderInterface
{

    /**
     * Get row id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set customer id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setEntityId($id)
    {
        $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get user id from social network
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * Set user id from social network
     *
     * @param int $id
     *
     * @return $this
     */
    public function setUserId($id)
    {
        return $this->setData(self::USER_ID, $id);
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->_get(self::PROVIDER);
    }

    /**
     * Set provider
     *
     * @param string $provider
     *
     * @return $this
     */
    public function setProvider($provider)
    {
        return $this->setData(self::PROVIDER, $provider);
    }

}