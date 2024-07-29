<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Shiprestriction
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */


namespace Mavenbird\Shiprestriction\Model\ResourceModel;

use Mavenbird\Shiprestriction\Model\ConstantsInterface;

class Rule extends \Mavenbird\Shiprestriction\Model\ResourceModel\AbstractRule
{
    public const TABLE_NAME = 'mavenbird_shiprestriction_rule';
    public const ATTRIBUTE_TABLE_NAME = 'mavenbird_shiprestriction_attribute';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'rule_id');
    }

    /**
     * Beforesave
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            // convert data from array to string
            $value = $object->getData($field);
            $object->setData($field, '');

            if (is_array($value)) {
                if ($field == 'methods') {
                    $carriers = [];

                    foreach ($value as $key => $shipMethod) {
                        if (strpos($shipMethod, '_') === false) {
                            $carriers[] = $shipMethod;
                            unset($value[$key]);
                        }
                    }
                    $object->setCarriers(implode(',', $carriers));
                }

                $object->setData($field, implode(',', $value));
            }
        }

        return parent::_beforeSave($object);
    }
}
