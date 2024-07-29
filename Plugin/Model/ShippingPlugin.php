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

namespace Mavenbird\Shiprestriction\Plugin\Model;

use Mavenbird\Shiprestriction\Model\ConstantsInterface;

/**
 * Entry point.
 */
class ShippingPlugin
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory
     */
    private $rateErrorFactory;

    /**
     * @var \Mavenbird\Shiprestriction\Model\ShippingRestrictionRule
     */
    private $shippingRestrictionRule;

    /**
     * @var \Mavenbird\Shiprestriction\Model\Config
     */
    private $shiprestrictionConfig;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest|null
     */
    private $request = null;

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Mavenbird\Shiprestriction\Model\ShippingRestrictionRule $shipRestrictionRule
     * @param \Mavenbird\Shiprestriction\Model\Config $shiprestrictionConfig
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Mavenbird\Shiprestriction\Model\ShippingRestrictionRule $shipRestrictionRule,
        \Mavenbird\Shiprestriction\Model\Config $shiprestrictionConfig
    ) {
        $this->rateErrorFactory = $rateErrorFactory;
        $this->shippingRestrictionRule = $shipRestrictionRule;
        $this->shiprestrictionConfig = $shiprestrictionConfig;
    }

    /**
     * Before collect rates
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     */
    public function beforeCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $this->request = $request;
    }

    /**
     * After collect rates
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param string $result
     */
    public function afterCollectRates(\Magento\Shipping\Model\Shipping $subject, $result)
    {
        $result = $subject->getResult();

        if (!($rates = $result->getAllRates())
            || !($rules = $this->shippingRestrictionRule->getRestrictionRules($this->request))
        ) {
            return $subject;
        }

        $result->reset();
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        foreach ($rates as $rate) {
            $restrict = false;
            /** @var \Mavenbird\Shiprestriction\Model\Rule $rule */
            foreach ($rules as $rule) {
                if ($rule->match($rate)) {
                    $restrict = true;
                    $this->setError($result, $rate, $rule->getMessage());
                    break;
                }
            }
            if (!$restrict) {
                $result->append($rate);
            }

        }

        return $subject;
    }

    /**
     * Set error
     *
     * @param \Magento\Shipping\Model\Rate\Result $result
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $lastRate
     * @param string $errorMessage
     *
     * @return bool
     */
    private function setError($result, $lastRate, $errorMessage)
    {
        $errorMessage = $errorMessage
            ?: __('Sorry, no shipping quotes are available for the selected products and destination');

        $isShowMessage = $this->shiprestrictionConfig->getErrorMessageConfig(
            ConstantsInterface::SECTION_KEY
        );

        if ($lastRate !== null && $isShowMessage && $errorMessage) {
            $error = $this->rateErrorFactory->create();
            $error->setCarrier($lastRate->getCarrier());
            $error->setCarrierTitle($lastRate->getCarrierTitle());
            $error->setErrorMessage($errorMessage);

            $result->append($error);

            return true;
        }

        return false;
    }
}
