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

namespace Mavenbird\Shiprestriction\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AbstractData extends AbstractHelper
{
    public const CONFIG_MODULE_PATH = 'mavenbird';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * AbstractData constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param JsonHelper $jsonHelper
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        JsonHelper $jsonHelper,
        Json $jsonSerializer
    ) {
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->jsonSerializer = $jsonSerializer;

        parent::__construct($context);
    }

    /**
     * Is enabled
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return (bool) $this->getConfigGeneral('enabled', $storeId);
    }

    /**
     * Get config general
     *
     * @param string $code
     * @param int|null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral(string $code = '', ?int $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * Get module config
     *
     * @param string $field
     * @param int|null $storeId
     *
     * @return mixed
     */
    public function getModuleConfig(string $field = '', ?int $storeId = null)
    {
        $field = ($field !== '') ? '/' . $field : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . $field, $storeId);
    }

    /**
     * Get config value
     *
     * @param string $field
     * @param int|null $scopeValue
     * @param string $scopeType
     *
     * @return mixed
     */
    public function getConfigValue(string $field, ?int $scopeValue = null, string $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if (!$this->isArea() && ($scopeValue===null)) {
            return $this->scopeConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * Get data
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getData(string $name)
    {
        return $this->_data[$name] ?? null;
    }

    /**
     * Set data
     *
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setData(string $name, $value): self
    {
        $this->_data[$name] = $value;

        return $this;
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * Version compare
     *
     * @param string $ver
     *
     * @return bool
     */
    public function versionCompare(string $ver): bool
    {
        $productMetadata = $this->_productMetadata;
        $version = $productMetadata->getVersion(); //will return the magento version

        return version_compare($version, $ver, '>=');
    }

    /**
     * Serialize
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data): string
    {
        if ($this->versionCompare('2.2.0')) {
            return $this->jsonEncode($data);
        }

        return $this->phpSerialize->serialize($data);
    }

    /**
     * Unserialize
     *
     * @param string $string
     *
     * @return mixed
     */
    public function unserialize(string $string)
    {
        if ($this->versionCompare('2.2.0')) {
            return $this->jsonDecode($string);
        }

        return $this->phpSerialize->unserialize($string);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public function jsonEncode($valueToEncode): string
    {
        try {
            return $this->jsonHelper->jsonEncode($valueToEncode);
        } catch (\Exception $e) {
            return '{}';
        }
    }

    /**
     * Decode the given $encodedValue string which is encoded in the JSON format
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public function jsonDecode(string $encodedValue)
    {
        try {
            return $this->jsonHelper->jsonDecode($encodedValue);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Is Admin Store
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isArea(Area::AREA_ADMINHTML);
    }

    /**
     * Is area
     *
     * @param string $area
     *
     * @return bool
     */
    public function isArea(string $area = Area::AREA_FRONTEND): bool
    {
        try {
            $state = $this->_state;
            return $state->getAreaCode() == $area;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create object
     *
     * @param string $path
     * @param array $arguments
     *
     * @return mixed
     */
    public function createObject(string $path, array $arguments = [])
    {
        return $this->_objectManager->create($path, $arguments);
    }

    /**
     * Get object
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getObject(string $path)
    {
        return $this->_objectManager->get($path);
    }
}
