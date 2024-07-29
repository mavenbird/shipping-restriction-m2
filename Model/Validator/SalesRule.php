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


namespace Mavenbird\Shiprestriction\Model\Validator;

class SalesRule implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate($rule, $items)
    {
        $providedCouponCodes = $this->getCouponCodes($items);

        $providedRuleIds = $this->getRuleIds($items);

        return $this->isApply($rule, $providedCouponCodes, $providedRuleIds) ?
            !$this->isApply($rule, $providedCouponCodes, $providedRuleIds, false) : false;
    }

    /**
     * Apply rule providedcoupencode provideruleids if disable is true.
     *
     * @param \Magento\Rule\Model\AbstractModel $rule
     * @param array $providedCouponCodes
     * @param array $providedRuleIds
     * @param bool $isDisable
     *
     * @return bool
     */
    private function isApply($rule, $providedCouponCodes, $providedRuleIds, $isDisable = true)
    {
        if ($isDisable) {
            $coupons = $rule->getCouponDisable();
            $discountIds = $rule->getDiscountIdDisable();
        } else {
            $coupons = $rule->getCoupon();
            $discountIds = $rule->getDiscountId();
        }

        if (!$coupons && !$discountIds) {
            return $isDisable;
        }

        $activeCoupons = $coupons ? array_intersect(explode(',', strtolower($coupons)), $providedCouponCodes) : [];
        $activeRules = $discountIds ? array_intersect(explode(',', $discountIds), $providedRuleIds) : [];

        return !($activeCoupons || $activeRules);
    }

    /**
     * Get rules ids for items.
     *
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return array
     */
    private function getRuleIds($items)
    {
        if (empty($items)) {
            return [];
        }

        /** @var \Magento\Quote\Model\Quote\Item $firstItem */
        $firstItem = current($items);
        $rules = trim($firstItem->getQuote()->getAppliedRuleIds());

        if (!$rules) {
            return [];
        }

        return explode(',', $rules);
    }

    /**
     * Get coupen codes for items.
     *
     * @param \Magento\Quote\Model\Quote\Item[] $items
     *
     * @return array
     */
    private function getCouponCodes($items)
    {
        if (!count($items)) {
            return [];
        }

        /** @var \Magento\Quote\Model\Quote\Item $firstItem */
        $firstItem = current($items);
        $codes = trim(strtolower($firstItem->getQuote()->getCouponCode()));

        if (!$codes) {
            return [];
        }

        return array_map('trim', explode(",", $codes));
    }
}
