<?php
/**
 * Scandiweb_SocialLogin
 *
 * @category    Scandiweb
 * @package     Scandiweb_SocialLogin
 * @author      Viktors Vipolzovs <info@scandiweb.com>
 * @copyright   Copyright (c) 2016 Scandiweb, Ltd (http://scandiweb.com)
 */

namespace Scandiweb\SocialLogin\Model\Validator\Attribute;

use Magento\Eav\Model\Validator\Attribute\Data as MagentoData;
use Magento\Framework\DataObject;
use Magento\Eav\Model\Attribute;

class Data extends MagentoData
{

    /**
     * Validate EAV model attributes with data models
     *
     * @param \Magento\Framework\Model\AbstractModel $entity
     * @return bool
     */
    public function isValid($entity)
    {
        /** @var $attributes Attribute[] */
        $attributes = $this->_getAttributes($entity);
        $isNotRequiredAttributes = ['firstname', 'lastname'];

        $data = [];
        if ($this->_data) {
            $data = $this->_data;
        } elseif ($entity instanceof DataObject) {
            $data = $entity->getData();
        }

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, $isNotRequiredAttributes)) {
                $attribute->setIsRequired(false);
            }

            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }

            $dataModel = $this->_attrDataFactory->create($attribute, $entity);
            $dataModel->setExtractedData($data);

            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }

            $result = $dataModel->validateValue($data[$attributeCode]);
            if (true !== $result) {
                $this->_addErrorMessages($attributeCode, (array)$result);
            }
        }

        return count($this->_messages) == 0;
    }

}