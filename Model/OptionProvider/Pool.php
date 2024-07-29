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


namespace Mavenbird\Shiprestriction\Model\OptionProvider;

class Pool
{
    /**
     * @var \Magento\Framework\Data\OptionSourceInterface[]
     */
    protected $optionProviders;

    /**
     * PoolOptionProvider constructor.
     * @param array $optionProviders
     */
    public function __construct(array $optionProviders)
    {
        $this->optionProviders = $optionProviders;
    }

    /**
     * List of registered option providers
     *
     * @return \Magento\Framework\Data\OptionSourceInterface[]
     */
    public function getOptionProviders()
    {
        return $this->optionProviders;
    }

    /**
     * Get optionds by provide code
     *
     * @param array $providerCode
     * @return array|null
     */
    public function getOptionsByProviderCode($providerCode)
    {
        return isset($this->optionProviders[$providerCode])
            ? $this->optionProviders[$providerCode]->toOptionArray() : null;
    }
}
