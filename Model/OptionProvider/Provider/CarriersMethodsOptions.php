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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Return array of carriers with methods optgroup
 */
class CarriersMethodsOptions implements ArrayInterface
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Return array of carriers with methods optgroup.
     *
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     *
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $methods = [];
        $carriers = $this->shippingConfig->getAllCarriers();
        /** @var \Magento\Shipping\Model\Carrier\CarrierInterface $carrierModel */
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if (empty($carrierTitle)) {
                $carrierTitle = $carrierCode;
            }
            $methods[$carrierCode] = ['label' => $carrierTitle, 'optgroup' => [], 'value' => $carrierCode];
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $value = $label = $carrierCode . '_' . $methodCode;
                if (!empty($methodTitle)) {
                    $label = '[' . $carrierCode . '] ' . $methodTitle;
                }
                $methods[$carrierCode]['optgroup'][] = [
                    'value' => $value,
                    'label' => $label,
                ];
            }
        }

        return $methods;
    }
}
