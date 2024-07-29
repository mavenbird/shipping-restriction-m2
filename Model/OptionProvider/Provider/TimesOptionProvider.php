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
class TimesOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
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
            $timeArray = [
                ['value' => 0, 'label' => __('Please select...')]
            ];

            for ($i = 0; $i < 24; $i++) {
                for ($j = 0; $j < 60; $j = $j + 15) {
                    $timeStamp = $i . ':' . $j;
                    $timeFormat = date('H:i', strtotime($timeStamp));
                    $timeArray[] = ['value' => $i * 100 + $j + 1, 'label' => $timeFormat];
                }
            }

            $this->options = $timeArray;
        }

        return $this->options;
    }
}
