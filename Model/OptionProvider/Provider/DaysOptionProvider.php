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

/**
 * OptionProvider
 */
class DaysOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array|null
     */
    protected $options;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => '7',
                    'label' => __('Sunday')
                ],
                [
                    'value' => '1',
                    'label' => __('Monday')
                ],
                [
                    'value' => '2',
                    'label' => __('Tuesday')
                ],
                [
                    'value' => '3',
                    'label' => __('Wednesday')
                ],
                [
                    'value' => '4',
                    'label' => __('Thursday')
                ],
                [
                    'value' => '5',
                    'label' => __('Friday')
                ],
                [
                    'value' => '6',
                    'label' => __('Saturday')
                ],
            ];
        }

        return $this->options;
    }
}
