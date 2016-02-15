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

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerProvider extends AbstractDb
{

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_provider', 'id');
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel $object
     * @throws ValidatorException
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        if (!$object->getEntityId() || !$object->getUserId() || !$object->getProvider()) {
            throw new ValidatorException(__('Not received all the required fields'));
        }

        parent::_beforeSave($object);

        return $this;
    }

}