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

class ConditionBuilder
{
    public const MAVENBIRD_COMMON_RULES_PATH_TO_CONDITIONS = 'Mavenbird\Shiprestriction\Model\Rule\Condition\\';
    public const MAVENBIRD_SHIP_RESTRICTION_PATH_TO_CONDITIONS = 'Mavenbird\Shiprestriction\Model\Rule\Condition\\';
    public const MAVENBIRD_SHIP_RULES_PATH_TO_CONDITIONS = 'Mavenbird\Shiprules\Model\Rule\Condition\\';
    public const MAGENTO_SALES_RULE_PATH_TO_CONDITIONS = 'Magento\SalesRule\Model\Rule\Condition\\';

    /**
     * Change new child select options
     *
     * @param array $conditions
     *
     * @return array
     */
    public function getChangedNewChildSelectOptions($conditions)
    {
        foreach ($conditions as $key => $value) {
            if (isset($value['value'])
                && $value['value'] == self::MAGENTO_SALES_RULE_PATH_TO_CONDITIONS . 'Product\Combine'
            ) {
                $conditions[$key]['value'] = self::MAVENBIRD_COMMON_RULES_PATH_TO_CONDITIONS . 'Product\Combine';
            }
        }

        return $conditions;
    }
}
