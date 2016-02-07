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
use Magento\Framework\Exception\State\InputMismatchException;

interface AccountManagementInterface
{

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @api
     * @param CustomerInterface $customer
     * @param string $password
     * @param string $redirectUrl
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function createAccount(
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    );

    /**
     * Create customer account using provided hashed password. Should not be exposed as a webapi.
     *
     * @api
     * @param CustomerInterface $customer
     * @param string $hash Password hash that we can save directly
     * @param string $redirectUrl URL fed to welcome email templates. Can be used by templates to, for example, direct
     *                            the customer to a product they were looking at after pressing confirmation link.
     * @return CustomerInterface
     * @throws InputException If bad input is provided
     * @throws InputMismatchException If the provided email is already used
     * @throws LocalizedException
     */
    public function createAccountWithPasswordHash(
        CustomerInterface $customer,
        $hash,
        $redirectUrl = ''
    );


}