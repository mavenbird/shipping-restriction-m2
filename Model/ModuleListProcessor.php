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

use Mavenbird\Shiprestriction\Model\Feed\ExtensionsProvider;
use Magento\Framework\Module\ModuleListInterface;

class ModuleListProcessor
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var array
     */
    private $modules;

    /**
     * @var Feed\ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param ModuleListInterface $moduleList
     * @param ExtensionsProvider $extensionsProvider
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ExtensionsProvider $extensionsProvider,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->moduleList = $moduleList;
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * Get module List
     *
     * @return array
     */
    public function getModuleList()
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        $this->modules = [
            'lastVersion' => [],
            'hasUpdate' => []
        ];

        $modules = $this->moduleList->getNames();
        sort($modules);

        foreach ($modules as $moduleName) {
            if ($moduleName === 'Mavenbird_Shiprestriction'
                || strpos($moduleName, 'Mavenbird_') === false
                || in_array($moduleName, $this->moduleInfoProvider->getRestrictedModules(), true)
            ) {
                continue;
            }

            try {
                if (!is_array($module = $this->getModuleInfo($moduleName))) {
                    continue;
                }
            } catch (\Exception $e) {
                continue;
            }

            if (empty($module['hasUpdate'])) {
                $this->modules['lastVersion'][] = $module;
            } else {
                $this->modules['hasUpdate'][] = $module;
            }
        }

        return $this->modules;
    }

    /**
     * Get module Info
     *
     * @param string $moduleCode
     *
     * @return array|mixed|string
     */
    protected function getModuleInfo($moduleCode)
    {
        $module = $this->moduleInfoProvider->getModuleInfo($moduleCode);

        if (!is_array($module)
            || !isset($module['version'])
            || !isset($module['description'])
        ) {
            return '';
        }

        $currentVer = $module['version'];
        $module['description'] = $this->replaceMavenbirdText($module['description']);

        $allExtensions = $this->extensionsProvider->getAllFeedExtensions();
        if ($allExtensions && isset($allExtensions[$moduleCode])) {
            $ext = end($allExtensions[$moduleCode]);

            $lastVer = $ext['version'];
            $module['lastVersion'] = $lastVer;
            $module['hasUpdate'] = version_compare($currentVer, $lastVer, '<');
            $module['description'] = $this->replaceMavenbirdText($ext['name']);
            $module['url'] = !empty($ext['url']) ? $ext['url'] : '';

            return $module;
        }

        return '';
    }

    /**
     * Replace text
     *
     * @param string $moduleName
     *
     * @return string
     */
    protected function replaceMavenbirdText($moduleName)
    {
        return str_replace(['for Magento 2', 'by Mavenbird'], '', $moduleName);
    }
}
