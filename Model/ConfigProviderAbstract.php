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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @since 1.4.4
 */
abstract class ConfigProviderAbstract
{
    /**
     * Xpath prefix of module (section)
     * @var string '{section}/'
     */
    protected $pathPrefix = '/';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Stored values by scopes
     *
     * @var array
     */
    protected $data = [];

    /**
     * ConfigProviderAbstract constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws \LogicException
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        if ($this->pathPrefix === '/') {
            throw new \LogicException('$pathPrefix should be declared');
        }
    }

    /**
     * Clear local storage
     *
     * @return void
     */
    public function clean()
    {
        $this->data = [];
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $path '{group}/{field}'
     * @param int|ScopeInterface|null $storeId Scope code
     * @param string $scope
     *
     * @return mixed
     */
    protected function getValue(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        if ($storeId instanceof \Magento\Framework\App\ScopeInterface) {
            $storeId = $storeId->getId();
        }
        $scopeKey = $storeId;
        if ($scopeKey === null) {
            $scopeKey = 'current_';
        }
        $scopeKey .= $scope;
        if (empty($this->data[$path][$scopeKey])) {
            $this->data[$path][$scopeKey] = $this->scopeConfig->getValue($this->pathPrefix . $path, $scope, $storeId);
        }

        return $this->data[$path][$scopeKey];
    }

    /**
     * An alias for scope config with scope type Default
     *
     * @param string $path '{group}/{field}'
     *
     * @return mixed
     */
    protected function getGlobalValue($path)
    {
        return $this->getValue($path, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * Is set Flag
     *
     * @param string $path '{group}/{field}'
     * @param int|ScopeInterface|null $storeId
     * @param string $scope
     *
     * @return bool
     */
    protected function isSetFlag(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        return (bool)$this->getValue($path, $storeId, $scope);
    }

    /**
     * Is set global flag
     *
     * @param string $path '{group}/{field}'
     *
     * @return bool
     */
    protected function isSetGlobalFlag($path)
    {
        return $this->isSetFlag($path, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
