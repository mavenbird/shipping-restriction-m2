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
class PaymentMethodOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\Config\Initial
     */
    private $initialConfig;

    /**
     * PaymentMethodOptionProvider constructor.
     *
     * @param \Magento\Framework\App\Config\Initial $initialConfig
     */
    public function __construct(\Magento\Framework\App\Config\Initial $initialConfig)
    {
        $this->initialConfig = $initialConfig;
    }

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
            $hash = [];
            foreach ($this->initialConfig->getData('default')['payment'] as $code => $config) {
                if (!empty($config['title']) || !empty($config['group'])) {
                    $hash[$code] = $this->getLabel($config);
                }
            }
            asort($hash);

            $methods = [];
            foreach ($hash as $code => $label) {
                $methods[] = [
                    'value' => $code,
                    'label' => $label
                ];
            }

            $this->options = $methods;
        }

        return $this->options;
    }

    /**
     * Get label
     *
     * @param array $config
     *
     * @return \Magento\Framework\Phrase
     */
    private function getLabel($config)
    {
        $label = '';

        if (!empty($config['group'])) {
            $label = ucfirst($config['group']);
        }

        if (!empty($config['title'])) {
            $label .= $label ? ' - ' . $config['title'] : $config['title'];
        }

        if (!empty($config['allowspecific']) && !empty($config['specificcountry'])) {
            $label .= ' in ' . $config['specificcountry'];
        }

        return __($label);
    }
}
