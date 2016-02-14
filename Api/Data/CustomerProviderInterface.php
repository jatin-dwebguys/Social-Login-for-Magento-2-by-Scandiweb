<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Api\Data;

interface CustomerProviderInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID        = 'id';
    const ENTITY_ID = 'entity_id';
    const USER_ID   = 'user_id';
    const PROVIDER  = 'provider';

    /**
     * Get row id
     *
     * @return int
     */
    public function getId();

    /**
     * Get customer id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set customer id
     *
     * @param int $id
     * @return $this
     */
    public function setEntityId($id);

    /**
     * Get user id from social network
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set user id from social network
     *
     * @param int $id
     * @return $this
     */
    public function setUserId($id);

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider();

    /**
     * Set provider
     *
     * @param string $provider
     * @return $this
     */
    public function setProvider($provider);

}