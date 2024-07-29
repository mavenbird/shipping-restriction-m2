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

class MethodConverter
{
    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * @var array
     */
    private $methods = [];

    /**
     * @param \Magento\Shipping\Model\Config $shippingConfig
     *
     */
    public function __construct(
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Convert comma-separated string of shipping methods codes to string with labels of those methods.
     *
     * @param string|null $methodsStr Comma-separated string of method codes
     *
     * @return string
     */
    public function convert($methodsStr)
    {
        if (!$methodsStr) {
            return ''; // Return empty string if $methodsStr is null or empty
        }

        $methods = $this->getCarrierMethods();
        $result = [];
        $currentMethods = explode(",", $methodsStr);

        foreach ($currentMethods as $currentMethod) {
            $currentMethod = trim($currentMethod);
            if (!empty($currentMethod) && array_key_exists($currentMethod, $methods)) {
                $result[] = $methods[$currentMethod];
            }
        }

        return implode("<br>", $result); // @codingStandardsIgnoreLine
    }

    /**
     * Return array of shipping method codes, which label contains $likeValue.
     *
     * @param string $likeValue
     *
     * @return array|string
     */
    public function getCodes($likeValue)
    {
        $likeValue = trim(str_replace('%', '', $likeValue));

        if (stripos('Any', $likeValue) !== false) {
            return '';
        }

        $methods = $this->getCarrierMethods();

        return array_keys(array_filter($methods, function ($var) use ($likeValue) {
            return stripos($var, $likeValue) !== false;
        }));
    }

    /**
     * Return all shipping methods as array.
     *
     * Format like: method_code => [carrier_code] + method_label
     *
     * @return array
     */
    public function getCarrierMethods()
    {
        if (!$this->methods) {
            $methods = [];
            $carriers = $this->shippingConfig->getAllCarriers();

            /** @var \Magento\Shipping\Model\Carrier\CarrierInterface $carrierModel */
            foreach ($carriers as $carrierCode => $carrierModel) {
                $carrierMethods = $carrierModel->getAllowedMethods();

                if (!$carrierMethods) {
                    continue;
                }

                foreach ($carrierMethods as $methodCode => $methodTitle) {
                    if (strpos($carrierCode, '_') === false) {
                        $methods[$carrierCode . '_' . $methodCode] = '[' . $carrierCode . '] ' . $methodTitle;
                    } else {
                        $methods[$carrierCode] = '[' . $carrierCode . '] ' . $methodTitle;
                    }
                }
            }

            $this->methods = $methods;
        }

        return $this->methods;
    }
}
