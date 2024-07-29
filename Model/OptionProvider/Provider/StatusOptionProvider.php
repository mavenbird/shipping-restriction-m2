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
class StatusOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const ACTIVE  = 1;
    public const INACTIVE = 0;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * TO option array
     *
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => self::ACTIVE, 'label' => __('Active')],
                ['value' => self::INACTIVE, 'label' => __('Inactive')],
            ];
        }

        return $this->options;
    }
}
