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

class RuleData extends \Magento\Rule\Model\AbstractModel
{
    public const ALL_ORDERS = 0;
    public const BACKORDERS_ONLY = 1;
    public const NON_BACKORDERS = 2;

    public const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = \Magento\SalesRule\Model\Rule\Condition\Product::class;

    /**
     * Stores manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Rule\Condition\Combine
     */
    protected $conditionCombine;

    /**
     * @var Rule\Condition\Product\Combine
     */
    protected $conditionProductCombine;

    /**
     * @var \Mavenbird\Shiprestriction\Model\Modifiers\Subtotal
     */
    protected $subtotalModifier;

    /**
     * @var Validator\Backorder
     */
    protected $backorderValidator;

    /**
     * Rule constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mavenbird\Shiprestriction\Model\Rule\Condition\Combine $conditionCombine
     * @param \Mavenbird\Shiprestriction\Model\Rule\Condition\Product\Combine $conditionProductCombine
     * @param \Mavenbird\Shiprestriction\Model\Modifiers\Subtotal $subtotalModifier
     * @param \Mavenbird\Shiprestriction\Model\Validator\Backorder $backorderValidator
     * @param bool $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mavenbird\Shiprestriction\Model\Rule\Condition\Combine $conditionCombine,
        \Mavenbird\Shiprestriction\Model\Rule\Condition\Product\Combine $conditionProductCombine,
        \Mavenbird\Shiprestriction\Model\Modifiers\Subtotal $subtotalModifier,
        \Mavenbird\Shiprestriction\Model\Validator\Backorder $backorderValidator,
        $resource = null,
        array $data = []
    ) {
        $this->conditionCombine = $conditionCombine;
        $this->conditionProductCombine = $conditionProductCombine;
        $this->storeManager = $storeManager;
        $this->subtotalModifier = $subtotalModifier;
        $this->backorderValidator = $backorderValidator;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            null,
            $data
        );
    }

    /**
     * Get conditions instance.
     *
     * @return Rule\Condition\Combine
     */
    public function getConditionsInstance(): Rule\Condition\Combine
    {
        return $this->conditionCombine;
    }

    /**
     * Get actions instance.
     *
     * @return Rule\Condition\Product\Combine
     */
    public function getActionsInstance(): Rule\Condition\Product\Combine
    {
        return $this->conditionProductCombine;
    }

    /**
     * Match
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     *
     * @return bool
     */
    public function match($rate): bool
    {
        $selectedCarriers = explode(',', $this->getCarriers());

        if (in_array($rate->getCarrier(), $selectedCarriers)) {
            return true;
        }
        $methods = $this->getMethods();

        if (!$methods) {
            return false;
        }
        $methods = array_unique(explode(',', $methods));
        $rateCode = $rate->getCarrier();
        if (strpos($rate->getCarrier(), '_') === false) {
            $rateCode = $rate->getCarrier() . '_' . $rate->getMethod();
        }

        /** @var string $methodName */
        foreach ($methods as $methodName) {
            if ($rateCode == $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Websites Ids
     *
     * @return $this
     */
    protected function _setWebsiteIds(): self
    {
        $websites = [];

        foreach ($this->storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();

                foreach ($stores as $store) {
                    $websites[$store->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', array_unique($websites));

        return $this;
    }

    /**
     * Before save
     *
     * @return $this
     */
    public function beforeSave(): self
    {
        $this->_setWebsiteIds();

        return parent::beforeSave();
    }

    /**
     * Before delete
     *
     * @return $this
     */
    public function beforeDelete(): self
    {
        $this->_setWebsiteIds();

        return parent::beforeDelete();
    }

    /**
     * Validate object
     *
     * @param \Magento\Framework\DataObject $object
     * @param bool $items
     *
     * @return bool
     */
    public function validate(\Magento\Framework\DataObject $object, $items = null): bool
    {
        if ($items && !$this->backorderValidator->validate($this, $items)) {
            return false;
        }

        if ($object instanceof \Magento\Quote\Model\Quote\Address) {
            $object = $this->subtotalModifier->modify($object);
        }

        return parent::validate($object);
    }
}
