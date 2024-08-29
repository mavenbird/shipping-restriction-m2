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

use Mavenbird\Shiprestriction\Model\Source\NotificationType;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * ConfigProvider for Mavenbird_Shiprestriction module.
 */
class Config
{
        /**
     * xpath prefix of module (section)
     *
     * @var string
     */
    protected $pathPrefix = 'mavenbird_shiprestriction/';

    /**#@+
     * xpath group parts
     */
    public const NOTIFICATIONS_BLOCK = 'notifications/';

    public const SYSTEM_VALUE_BLOCK = 'system_value/';

    /**#@-*/

    /**#@+
     * xpath field parts
     */
    public const LAST_UPDATE = 'last_update';

    public const FREQUENCY = 'frequency';

    public const FIRST_MODULE_RUN = 'first_module_run';

    public const REMOVE_DATE = 'remove_date';

    public const ADS_ENABLE = 'ads_enable';

    public const NOTIFICATIONS_TYPE = 'type';

    /**#@-*/

    public const HOUR_MIN_SEC_VALUE = 60 * 60 * 24;

    public const REMOVE_EXPIRED_FREQUENCY = 60 * 60 * 6;//4 times per day

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        // parent::__construct($scopeConfig);
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
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

     /**
     * Get frequency in second
     *
     * @return int
     */
    public function getFrequencyInSec()
    {
        return $this->getCurrentFrequencyValue() * self::HOUR_MIN_SEC_VALUE;
    }

    /**
     * Get last update
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->getConfigValueByPath($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::LAST_UPDATE);
    }

    /**
     * Get last removement
     *
     * @return int
     */
    public function getLastRemovement()
    {
        return $this->getConfigValueByPath($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::REMOVE_DATE);
    }

    /**
     * Save Last Update
     */
    public function setLastUpdate()
    {
        $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::LAST_UPDATE, time());
        $this->reinitableConfig->reinit();
        $this->clean();
    }

    /**
     * Get first module run
     *
     * @return int
     */
    public function getFirstModuleRun()
    {
        $result = $this->getConfigValueByPath($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::FIRST_MODULE_RUN);
        if (!$result) {
            $result = time();
            $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::FIRST_MODULE_RUN, $result);
            $this->reinitableConfig->reinit();
            $this->clean();
        }

        return $result;
    }

    /**
     * Save Last Removement
     */
    public function setLastRemovement()
    {
        $this->configWriter->save($this->pathPrefix . self::SYSTEM_VALUE_BLOCK . self::REMOVE_DATE, time());
        $this->reinitableConfig->reinit();
        $this->clean();
    }

    /**
     * Get current Frequency value
     *
     * @return int
     */
    public function getCurrentFrequencyValue()
    {
        return $this->getConfigValueByPath($this->pathPrefix . self::NOTIFICATIONS_BLOCK . self::FREQUENCY);
    }

    /**
     * Change frequency
     *
     * @param int $value
     */
    public function changeFrequency($value)
    {
        $this->configWriter->save($this->pathPrefix . self::NOTIFICATIONS_BLOCK . self::FREQUENCY, $value);
        $this->reinitableConfig->reinit();
        $this->clean();
    }

    /**
     * Is ads Enabled
     *
     * @return bool
     */
    public function isAdsEnabled()
    {
        return (bool)$this->getConfigValueByPath($this->pathPrefix . self::NOTIFICATIONS_BLOCK . self::ADS_ENABLE);
    }

    /**
     * Get enable notification types
     *
     * @return array
     */
    public function getEnabledNotificationTypes()
    {
        $value = $this->getConfigValueByPath($this->pathPrefix . self::NOTIFICATIONS_BLOCK . self::NOTIFICATIONS_TYPE);

        return empty($value) ? [] : explode(',', $value);
    }
}
