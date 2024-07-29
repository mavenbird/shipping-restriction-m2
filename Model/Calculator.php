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

class Calculator
{
    public const CONNECTION_TYPE_DEFAULT = 'default';

    /**
     * @var Resource
     */
    private $resource;

    /**
     * Calculator constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Get single total field
     *
     * @param mixed $customerId
     * @param mixed $fieldName
     * @param mixed $conditions
     * @param mixed $conditionType
     *
     * @return mixed
     */
    public function getSingleTotalField($customerId, $fieldName, $conditions, $conditionType)
    {
        $result = $this->_getTotals($customerId, $conditions, $conditionType);

        return $result[$fieldName];
    }

    /**
     * Calculates aggregated order values for given customer
     *
     * @param int $customerId
     * @param array $conditions e.g. array( 0=> array('date'=>'>2013-12-04'),  1=>array('status'=>'>2013-12-04'))
     * @param string $conditionType "all"  or "any"
     *
     * @return array
     */
    protected function _getTotals($customerId, $conditions = [], $conditionType = 'all')
    {
        return $this->getTotals($customerId, $conditions, $conditionType);
    }
    /**
     * Get totals
     *
     * @param int $customerId
     * @param array $conditions
     * @param string $conditionType
     */
    public function getTotals($customerId, $conditions, $conditionType)
    {
        $db = $this->resource->getConnection(self::CONNECTION_TYPE_DEFAULT);
        $select = $db->select()
            ->from(['o' => $this->resource->getTableName('sales_order')], [])
            ->where('o.customer_id = ?', $customerId);

        $map = [
            'date' => 'o.created_at',
            'status' => 'o.status',
        ];

        foreach ($conditions as $element) {
            $value = current($element);
            $field = $map[key($element)];
            $w = $field . ' ' . $value;

            if ($conditionType == 'all') {
                $select->where($w);
            } else {
                $select->orWhere($w);
            }
        }

        $select->from(
            null,
            [
                'count' => new \Zend_Db_Expr('COUNT(*)'),
                'amount' => new \Zend_Db_Expr('SUM(o.base_grand_total)')
            ]
        );
        $row = $db->fetchRow($select);

        return [
            'average_order_value' => $row['count'] ? $row['amount'] / $row['count'] : 0,
            'total_orders_amount' => $row['amount'],
            'of_placed_orders' => $row['count'],
        ];
    }
}
