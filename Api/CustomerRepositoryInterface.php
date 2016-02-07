<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;

interface CustomerRepositoryInterface
{

    /**
     * Retrieve customer by user provider id and provider name
     *
     * @param int $id
     * @param string $provider
     * @return CustomerInterface | null
     */
    public function getByProviderIdAndName($id, $provider);

    /**
     * Create customer.
     *
     * @api
     * @param CustomerInterface $customer
     * @param string $passwordHash
     * @return CustomerInterface
     * @throws InputException If bad input is provided
     * @throws InputMismatchException If the provided email is already used
     * @throws LocalizedException
     */
    public function save(CustomerInterface $customer, $passwordHash = null);

    /**
     * Retrieve customer
     *
     * @api
     * @param string $email
     * @param int|null $websiteId
     * @return CustomerInterface
     * @throws NoSuchEntityException If customer with the specified email does not exist.
     * @throws LocalizedException
     */
    public function get($email, $websiteId = null);

}