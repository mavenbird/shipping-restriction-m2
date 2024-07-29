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

namespace Mavenbird\Shiprestriction\Model;

use Mavenbird\Shiprestriction\Model\RuleData;

class Rule extends RuleData
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
        parent::_construct();
        $this->subtotalModifier->setSectionConfig(ConstantsInterface::SECTION_KEY);
    }

    /**
     * @inheritDoc
     */
    public function prepareForEdit()
    {
        foreach (ConstantsInterface::FIELDS as $field) {
            $value = $this->getData($field);

            if (!is_array($value) && $value !== null) {
                $this->setData($field, explode(',', $value));
            }
        }

        $value = $this->getCarriers();

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (is_array($value)) {
            $existingMethods = $this->getMethods();
            if (!is_array($existingMethods)) {
                $existingMethods = [];
            }
            $this->setMethods(array_merge($value, $existingMethods));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStores()
    {
        $stores = $this->_getData('stores');
        if (is_string($stores)) {
            $stores = explode(',', $stores);
        } elseif ($stores === null) {
            $stores = [];
        }

        return $stores;
    }

    /**
     * @inheritDoc
     */
    public function setStores($stores)
    {
        if (is_array($stores)) {
            $stores = implode(',', $stores);
        } elseif ($stores === null) {
            $stores = '';
        }

        return $this->setData('stores', $stores);
    }
}
