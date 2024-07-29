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

namespace Mavenbird\Shiprestriction\Model\Rule\Condition\Product;

use Mavenbird\Shiprestriction\Model\Rule\Condition\ConditionBuilder as Conditions;

class Subselect extends \Magento\SalesRule\Model\Rule\Condition\Product\Subselect
{
    /**
     * @var Conditions
     */
    private $conditionBuilder;

    /**
     * Subselect constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Mavenbird\Shiprestriction\Model\Rule\Condition\Product $ruleConditionProduct
     * @param \Mavenbird\Shiprestriction\Model\Rule\Condition\ConditionBuilder $conditionBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Mavenbird\Shiprestriction\Model\Rule\Condition\Product $ruleConditionProduct,
        \Mavenbird\Shiprestriction\Model\Rule\Condition\ConditionBuilder $conditionBuilder,
        array $data = []
    ) {
        $this->conditionBuilder = $conditionBuilder;
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType(Conditions::MAVENBIRD_COMMON_RULES_PATH_TO_CONDITIONS . 'Product\Subselect')->setValue(null);
    }

    /**
     * Select options by child
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();

        return $this->conditionBuilder->getChangedNewChildSelectOptions($conditions);
    }

    /**
     * Load attribute Options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'qty'                       => __('total quantity'),
            'base_row_total'            => __('total amount excl. tax'),
            'base_row_total_incl_tax'   => __('total amount incl. tax'),
            'row_weight'                => __('total weight'),
        ]);

        return $this;
    }

    /**
     * Validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->validateNotModel($object);
    }

    /**
     * Validate not model
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function validateNotModel($object)
    {
        $attr = $this->getAttribute();
        $total = 0;
        $items = $object->getAllItems() ? $object->getAllItems() : $object->getItemsToValidateRestrictions();
        if ($items) {
            $validIds = [];
            foreach ($items as $item) {
                if ($item->getProduct()->getTypeId() == 'configurable') {
                    $item->getProduct()->setTypeId('skip');

                    foreach ($item->getChildren() as $child) {
                        $dataChild[$child->getId()] = [
                           'base_row_total' => $child->getBaseRowTotal(),
                           'price' => $child->getPrice(),
                        ];

                        $child->setBaseRowTotal(
                            $item->getBaseRowTotal()
                        )->setPrice(
                            $item->getPrice()
                        );
                    }
                }

                //can't use parent here
                if (\Magento\SalesRule\Model\Rule\Condition\Product\Combine::validate($item)) {
                    if (!($itemParentId = $item->getParentItemId())) {
                        $validIds[] = $item->getItemId();
                    } else {
                        if (in_array($itemParentId, $validIds)) {
                            continue;
                        } else {
                            $validIds[] = $itemParentId;
                        }
                    }

                    $total += $item->getData($attr);
                }

                if ($item->getProduct()->getTypeId() === 'skip') {
                    $item->getProduct()->setTypeId('configurable');
                }

                if (isset($dataChild[$item->getId()])) {
                    $item->setBaseRowTotal(
                        $dataChild[$item->getId()]['base_row_total']
                    )->setPrice(
                        $dataChild[$item->getId()]['price']
                    );
                }
            }
        }

        return $this->validateAttribute($total);
    }
}
