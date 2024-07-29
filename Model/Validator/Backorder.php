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

use Mavenbird\Shiprestriction\Model\Rule;

/**
 * Backorder Validation.
 */
class Backorder implements ValidatorInterface
{
    /**
     * @inheritdoc
     */

    public function validate($rule, $items)
    {
        switch ($rule->getOutOfStock()) {
            case Rule::BACKORDERS_ONLY:
                return $this->checkItemsBackorder($items, true);
            case Rule::NON_BACKORDERS:
                return $this->checkItemsBackorder($items, false);
        }

        return true;
    }

    /**
     * Check if items match the backorder flag.
     *
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @param bool $backorderFlag true - only backorder, false - only without backorder
     * @return bool
     */

    protected function checkItemsBackorder($items, $backorderFlag)
    {
        foreach ($items as $item) {
            if ((bool)$item->getBackorders() !== $backorderFlag) {
                return false;
            }
        }

        return true;
    }
}
