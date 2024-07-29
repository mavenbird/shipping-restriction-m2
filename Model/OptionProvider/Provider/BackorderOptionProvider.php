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
class BackorderOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const ALL_ORDERS = 0;
    public const BACKORDERS_ONLY = 1;
    public const NON_BACKORDERS = 2;

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
                ['value' => self::ALL_ORDERS, 'label' => __('All orders')],
                ['value' => self::BACKORDERS_ONLY, 'label' => __('Backorders only')],
                ['value' => self::NON_BACKORDERS, 'label' => __('Non backorders')]
            ];
        }

        return $this->options;
    }
}
