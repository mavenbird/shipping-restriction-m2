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

/**
 * ConfigProvider for Mavenbird_Shiprestriction module.
 */
class Config
{
    public const CONFIG_PATH_GENERAL_TAX_INCLUDE = '/general/tax';
    public const CONFIG_PATH_GENERAL_USE_SUBTOTAL = '/general/discount';
    public const CONFIG_PATH_GENERAL_ERROR_MESSAGE = '/general/error_message';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig Scope configuration interface.
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get tax include configuration.
     *
     * @param string $sectionName Section name.
     * @return bool
     */
    public function getTaxIncludeConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_TAX_INCLUDE);
    }

    /**
     * Get subtotal configuration.
     *
     * @param string $sectionName Section name.
     * @return bool
     */
    public function getUseSubtotalConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_USE_SUBTOTAL);
    }

    /**
     * Get error message configuration.
     *
     * @param string $sectionName Section name.
     * @return bool
     */
    public function getErrorMessageConfig($sectionName)
    {
        return $this->getConfigValueByPath($sectionName . self::CONFIG_PATH_GENERAL_ERROR_MESSAGE);
    }

    /**
     * Get configuration value by path.
     *
     * @param string            $path       Configuration path.
     * @param int|null          $storeId    Store ID.
     * @param string            $scope      Scope.
     * @return mixed
     */
    public function getConfigValueByPath(
        $path,
        $storeId = null,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * Get carriers configuration.
     *
     * @return array
     */
    public function getCarriersConfig()
    {
        return $this->scopeConfig->getValue('carriers');
    }
}
