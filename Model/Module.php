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

class Module
{
    public const SHIPPING_RULES_MODULE_NAMESPACE = 'Mavenbird_Shiprules';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Module constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->request->getModuleName();
    }

    /**
     * Get module alias
     *
     * @param string $moduleNamespace
     *
     * @return string
     */
    public function getModuleAlias($moduleNamespace)
    {
        return strtolower($moduleNamespace);
    }

    /**
     * Is shipping Rules Medthod
     *
     * @return bool
     */
    public function isShippingRulesMethod()
    {
        return $this->getModuleName()
            == $this->getModuleAlias(self::SHIPPING_RULES_MODULE_NAMESPACE);
    }
}
