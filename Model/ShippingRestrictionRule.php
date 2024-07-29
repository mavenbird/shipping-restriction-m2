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

class ShippingRestrictionRule
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Rule[]
     */
    private $allRules;

    /**
     * @var \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\Collection
     */
    private $rulesCollection;

    /**
     * @var \Mavenbird\Shiprestriction\Model\ProductRegistry
     */
    private $productRegistry;

    /**
     * @var Message\MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var \Mavenbird\Shiprestriction\Model\Validator\SalesRule
     */
    private $salesRuleValidator;

    /**
     * @param \Magento\Framework\App\State                                             $appState
     * @param \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\Collection           $rulesCollection
     * @param \Mavenbird\Shiprestriction\Model\ProductRegistry                         $productRegistry
     * @param \Mavenbird\Shiprestriction\Model\Message\MessageBuilder                  $messageBuilder
     * @param \Mavenbird\Shiprestriction\Model\Validator\SalesRule                         $salesRuleValidator
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\Collection $rulesCollection,
        \Mavenbird\Shiprestriction\Model\ProductRegistry $productRegistry,
        \Mavenbird\Shiprestriction\Model\Message\MessageBuilder $messageBuilder,
        \Mavenbird\Shiprestriction\Model\Validator\SalesRule $salesRuleValidator
    ) {
        $this->appState = $appState;
        $this->rulesCollection = $rulesCollection;
        $this->productRegistry = $productRegistry;
        $this->messageBuilder = $messageBuilder;
        $this->salesRuleValidator = $salesRuleValidator;
    }

    /**
     * Get restriction
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRestrictionRules($request)
    {
        /** @var \Magento\Quote\Model\Quote\Item[] $allItems */
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return [];
        }

        $firstItem = current($allItems);
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $firstItem->getAddress();
        $address->setItemsToValidateRestrictions($allItems);

        //multishipping optimization
        $this->prepareAllRules($address);

        /**
         * Fix for admin checkout
         *
         * UPD: Return missing address data (discount, grandtotal, etc)
         */
        if ($this->isAdmin() && $address->hasOrigData()) {
            $address->addData($address->getOrigData());
        }

        // remember old
        $subtotal = $address->getSubtotal();
        $baseSubtotal = $address->getBaseSubtotal();
        $validRules = $this->getValidRules($address, $allItems);
        // restore
        $address->setSubtotal($subtotal);
        $address->setBaseSubtotal($baseSubtotal);

        return $validRules;
    }

    /**
     * Address
     *
     * @param string $address
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareAllRules($address)
    {
        if (!$this->allRules) {
            $this->allRules = $this->rulesCollection->addAddressFilter($address);

            if ($this->isAdmin()) {
                $this->allRules->addFieldToFilter('for_admin', 1);
            }

            $this->allRules = $this->rulesCollection->getItems();

            /** @var \Mavenbird\Shiprestriction\Model\Rule $rule */
            foreach ($this->allRules as $rule) {
                $rule->afterLoad();
            }
        }
    }

    /**
     * Get valid rules
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote\Item[] $allItems
     *
     * @return \Mavenbird\Shiprestriction\Model\Rule[]
     */
    protected function getValidRules($address, $allItems)
    {
        $validRules = [];
        /** @var \Mavenbird\Shiprestriction\Model\Rule $rule */
        foreach ($this->allRules as $rule) {
            $this->productRegistry->clearProducts();

            if ($rule->validate($address, $allItems)
                && $this->salesRuleValidator->validate($rule, $allItems)
            ) {
                // remember used products
                $newMessage = $this->messageBuilder->parseMessage(
                    $rule->getMessage(),
                    $this->productRegistry->getProducts()
                );

                $rule->setMessage($newMessage);
                $validRules[] = $rule;
            }
        }

        return $validRules;
    }

    /**
     * Is admin
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isAdmin()
    {
        return $this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }
}
