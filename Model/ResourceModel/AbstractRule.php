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

use Mavenbird\Shiprestriction\Model\Rule;

/**
 * Abstract class representing a rule skeleton for database operations.
 */
abstract class AbstractRule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name for attributes.
     */
    public const ATTRIBUTE_TABLE_NAME = '';

    /**
     * @var \Mavenbird\Core\Model\Serializer
     */
    protected $serializer;

    /**
     * AbstractRule constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context     $context        Database context.
     * @param \Mavenbird\Core\Model\Serializer                      $serializer     Serializer instance.
     * @param string|null                                           $connectionName Optional connection name.
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mavenbird\Core\Model\Serializer $serializer,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->serializer = $serializer;
    }

    /**
     * Perform actions after saving a model.
     *
     * @param \Magento\Framework\Model\AbstractModel $object Saved model instance.
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($object->getConditionsSerialized()),
            $this->_getUsedAttributes($object->getActionsSerialized())
        );

        if (count($ruleProductAttributes)) {
            $this->saveAttributes($object->getId(), $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve all product attributes used in serialized action or condition.
     *
     * @param string $serializedString Serialized string representing conditions or actions.
     * @return array Used attributes.
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $serializedString = $this->serializer->unserialize($serializedString);

        if (is_array($serializedString) && array_key_exists('conditions', $serializedString)) {
            $result = $this->recursiveFindAttributes($serializedString);
        }

        return $result;
    }

    /**
     * Recursively find attributes in a serialized array.
     *
     * @param array $loop Serialized array to search.
     * @return array Found attributes.
     */
    protected function recursiveFindAttributes($loop)
    {
        $arrayIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($loop)
        );

        $result = [];
        $nextAttribute = false;
        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == Rule::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $nextAttribute = true;
            }

            if ($key == 'attribute' && $nextAttribute) {
                $result[] = $value;
                $nextAttribute = false;
            }
        }

        return $result;
    }

    /**
     * Retrieve codes of all product attributes currently used in promo rules.
     *
     * @return array Attribute codes.
     */
    public function getAttributes()
    {
        $dbConnection = $this->getConnection();
        $select = $dbConnection->select()
            ->from(
                $this->getTable(static::ATTRIBUTE_TABLE_NAME),
                new \Zend_Db_Expr('DISTINCT code')
            );

        return $dbConnection->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule.
     *
     * @param int   $ruleId     Rule ID.
     * @param array $attributes List of attribute codes.
     * @return $this
     */
    public function saveAttributes($ruleId, $attributes)
    {
        $dbConnection = $this->getConnection();

        $dbConnection->delete(
            $this->getTable(static::ATTRIBUTE_TABLE_NAME),
            [
                'rule_id = ?' => $ruleId
            ]
        );

        $data = [];
        foreach ($attributes as $code) {
            $data[] = [
                'rule_id' => $ruleId,
                'code' => $code,
            ];
        }
        $dbConnection->insertMultiple($this->getTable(static::ATTRIBUTE_TABLE_NAME), $data);

        return $this;
    }

    /**
     * Retrieve linked stores for a given rule model.
     *
     * @param \Mavenbird\Shiprestriction\Model\Rule $model Rule model instance.
     * @return array Linked store IDs.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStores($model)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable(),
            'stores'
        )->where(
            'rule_id = :rule_id'
        );

        if (!($result = $connection->fetchCol($select, ['rule_id' => $model->getId()]))) {
            $result = [];
        }

        if (is_string($result)) {
            $result = explode(',', $result);
        }

        return $result;
    }
}
