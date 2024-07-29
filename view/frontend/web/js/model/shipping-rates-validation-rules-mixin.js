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
define([
        'jquery',
        'mage/utils/wrapper',
        'uiRegistry'
],function ($, wrapper, registry) {
    "use strict";

    return function (shippingRatesValidationRules) {
        shippingRatesValidationRules.getObservableFields = wrapper.wrap(
            shippingRatesValidationRules.getObservableFields,
            function (originalAction) {
                var fields = originalAction();
                fields.push('street');
                fields.push('city');
                return fields;
            }
        );

        return shippingRatesValidationRules;
    };
});
