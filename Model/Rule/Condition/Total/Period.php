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

namespace Mavenbird\Shiprestriction\Model\Rule\Condition\Total;

class Period extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'period' => __('Period after order was placed'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '>=' => __('equals or less than'),
                '<=' => __('equals or greater than'),
                '>'  => __('less than'),
                '<'  => __('greater than'),
                '='  => __('is'),
            ]
        );

        return $this;
    }

    /**
     * Attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
     * Value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Value select options
     *
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        $options = [];

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }

        return $this->getData($key);
    }

    /**
     * Validate model
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return array
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $v = min(16000, $this->getValue());

        $date   = date("Y-m-d H:i:s", time() - $v * 24 * 3600);
        $result = ['date' => $this->getOperatorForValidate() . "'" . $date . "'"];

        return $result;
    }
}
