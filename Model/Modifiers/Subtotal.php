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


namespace Mavenbird\Shiprestriction\Model\Modifiers;

/**
 * Subtotal Modifier
 */
class Subtotal implements ModifierInterface
{
    /**
     * @var string
     */
    protected $sectionConfig = '';

    /**
     * @var \Mavenbird\Shiprestriction\Model\Config
     */
    private $config;

    /**
     * Subtotal constructor.
     * @param \Mavenbird\Shiprestriction\Model\Config $config
     */
    public function __construct(\Mavenbird\Shiprestriction\Model\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Modify
     *
     * @param \Magento\Quote\Model\Quote\Address $object
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function modify($object)
    {
        /** @var \Magento\Quote\Model\Quote\Address $tempObject */
        $tempObject = clone $object;

        $subtotal = $tempObject->getSubtotal();
        $baseSubtotal = $tempObject->getBaseSubtotal();

        if ($this->config->getTaxIncludeConfig($this->getSectionConfig())) {
            $subtotal += $tempObject->getTaxAmount();
            $baseSubtotal += $tempObject->getBaseTaxAmount();
        }

        if ($this->config->getUseSubtotalConfig($this->getSectionConfig())) {
            $subtotal += $tempObject->getDiscountAmount();
            $baseSubtotal += $tempObject->getBaseDiscountAmount();
        }

        $tempObject->setSubtotal($subtotal);
        $tempObject->setBaseSubtotal($baseSubtotal);
        $tempObject->setPackageValueWithDiscount($baseSubtotal);

        return $tempObject;
    }

    /**
     * Set section config
     *
     * @param string $sectionConfig
     *
     * @return $this
     */
    public function setSectionConfig($sectionConfig)
    {
        $this->sectionConfig = $sectionConfig;

        return $this;
    }

    /**
     * Get section config
     *
     * @return string
     */
    public function getSectionConfig()
    {
        return $this->sectionConfig;
    }
}
