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

namespace Mavenbird\Shiprestriction\Model\Rule\Condition;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Rule\Model\Condition as Condition;

class Orders extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * Orders constructor.
     * @param Condition\Context $context
     * @param AppResource $resource
     * @param array $data
     */
    public function __construct(
        Condition\Context $context,
        AppResource $resource,
        array $data = []
    ) {
        $this->resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * Condition label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getConditionLabel()
    {
        return __('Purchases history');
    }

    /**
     * Attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'order_num'    => __('Number of Completed Orders'),
            'sales_amount' => __('Total Sales Amount'),
        ];

        $this->setAttributeOption($attributes);

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
     * Value Element Type
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
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $quote = $model;

        if (!$quote instanceof \Magento\Quote\Model\Quote) {
            $quote = $model->getQuote();
        }

        $num = 0;
        if ($quote->getCustomerId()) {
            $db = $this->resource->getConnection('default');

            $select = $db->select()
                ->from(['o' => $this->resource->getTableName('sales_order')], [])
                ->where('o.customer_id = ?', $quote->getCustomerId())
                ->where('o.status = ?', 'complete');

            if ('order_num' == $this->getAttribute()) {
                $select->from(null, [new \Zend_Db_Expr('COUNT(*)')]);
            } elseif ('sales_amount' == $this->getAttribute()) {
                $select->from(null, [new \Zend_Db_Expr('SUM(o.base_grand_total)')]);
            }

            $num = $db->fetchOne($select);
        }

        return $this->validateAttribute($num);
    }
}
