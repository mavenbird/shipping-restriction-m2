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

namespace Mavenbird\Shiprestriction\Model\OptionProvider\Provider;

class CalculationOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const CALC_REPLACE = 0;
    public const CALC_ADD     = 1;
    public const CALC_DEDUCT  = 2;
    public const CALC_REPLACE_PRODUCT = 3;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * To option array
     *
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'label' => __('Replace'),
                    'value' => self::CALC_REPLACE
                ],
                [
                    'label' => __('Surcharge'),
                    'value' => self::CALC_ADD
                ],
                [
                    'label' => __('Discount'),
                    'value' => self::CALC_DEDUCT
                ],
                [
                    'label' => __('Partial Replace'),
                    'value' => self::CALC_REPLACE_PRODUCT
                ]
            ];
        }

        return $this->options;
    }
}
