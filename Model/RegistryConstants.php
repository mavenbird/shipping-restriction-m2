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

/**
 * Declarations of core registry keys used by the Shiprestriction module
 *
 */
class RegistryConstants
{
    /**
     * Rule table names for modules
     */
    public const SHIPPING_RULES_RULE_TABLE_NAME = 'mavenbird_shiprules_rule';
    public const SHIPPING_RESTRICTIONS_RULE_TABLE_NAME = 'mavenbird_shiprestriction_rule';
    public const PAYMENT_RESTRICTIONS_RULE_TABLE_NAME = 'payrestriction_rule';

    public const MAVENBIRD_SPECIAL_PROMOTIONS_PRO_MODULE_NAME = 'Mavenbird_RulesPro';
}
