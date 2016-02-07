<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Model\ResourceModel;

use Magento\Customer\Model\ResourceModel\Customer as MagentoCustomer;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Eav\Model\Entity\AbstractEntity;

class Customer extends MagentoCustomer
{

    /**
     * @param DataObject $customer
     *
     * @return $this
     * @throws ValidatorException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function _beforeSave(DataObject $customer)
    {
        /** @var \Scandiweb\SocialLogin\Model\Customer $customer */
        if ($customer->getStoreId() === null) {
            $customer->setStoreId($this->storeManager->getStore()->getId());
        }
        $customer->getGroupId();

        AbstractEntity::_beforeSave($customer);

        if (!$customer->getEmail()) {
            throw new ValidatorException(__('Please enter a customer email.'));
        }

        $connection = $this->getConnection();
        $bind = ['email' => $customer->getEmail()];

        $select = $connection->select()->from(
            $this->getEntityTable(),
            [$this->getEntityIdField()]
        )->where(
            'email = :email'
        );
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $bind['website_id'] = (int)$customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }
        if ($customer->getId()) {
            $bind['entity_id'] = (int)$customer->getId();
            $select->where('entity_id != :entity_id');
        }

        $result = $connection->fetchOne($select, $bind);
        if ($result) {
            throw new AlreadyExistsException(
                __('A customer with the same email already exists in an associated website.')
            );
        }

        // set confirmation key logic
        if ($customer->getForceConfirmed() || $customer->getPasswordHash() == '') {
            $customer->setConfirmation(null);
        } elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
            $customer->setConfirmation($customer->getRandomConfirmationKey());
        }
        // remove customer confirmation key from database, if empty
        if (!$customer->getConfirmation()) {
            $customer->setConfirmation(null);
        }

        $this->_validate($customer);

        return $this;
    }

    /**
     * Validate customer entity
     *
     * @param \Scandiweb\SocialLogin\Model\Customer $customer
     * @return void
     * @throws \Magento\Framework\Validator\Exception
     */
    protected function _validate($customer)
    {
        $validator = $this->_validatorFactory->createValidator('sociallogin_customer', 'save');
        if (!$validator->isValid($customer)) {
            throw new ValidatorException(
                null,
                null,
                $validator->getMessages()
            );
        }
    }

}