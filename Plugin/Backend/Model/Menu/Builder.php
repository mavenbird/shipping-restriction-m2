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


namespace Mavenbird\Shiprestriction\Plugin\Backend\Model\Menu;

use Mavenbird\Shiprestriction\Model\Feed\ExtensionsProvider;
use Mavenbird\Shiprestriction\Model\ModuleInfoProvider;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Config;
use Magento\Backend\Model\Menu\Filter\IteratorFactory;
use Magento\Backend\Model\Menu\ItemFactory;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Builder
{
    public const BASE_MENU = 'Mavenbird_Shiprestriction::menu';

    public const SETTING_ENABLE = 'mavenbird_shiprestriction/menu/enable';

    /**
     * @var Config
     */
    private $menuConfig;

    /**
     * @var array|null
     */
    private $mavenbirdItems = null;

    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param Config $menuConfig
     * @param IteratorFactory $iteratorFactory
     * @param ItemFactory $itemFactory
     * @param ModuleListInterface $moduleList
     * @param Structure $configStructure
     * @param ProductMetadataInterface $metadata
     * @param ObjectFactory $objectFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ExtensionsProvider $extensionsProvider
     * @param LoggerInterface $logger
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        Config $menuConfig,
        IteratorFactory $iteratorFactory,
        ItemFactory $itemFactory,
        ModuleListInterface $moduleList,
        Structure $configStructure,
        ProductMetadataInterface $metadata,
        ObjectFactory $objectFactory,
        ScopeConfigInterface $scopeConfig,
        ExtensionsProvider $extensionsProvider,
        LoggerInterface $logger,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->menuConfig = $menuConfig;
        $this->iteratorFactory = $iteratorFactory;
        $this->itemFactory = $itemFactory;
        $this->moduleList = $moduleList;
        $this->configStructure = $configStructure;
        $this->objectFactory = $objectFactory;
        $this->metadata = $metadata;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * After get result
     *
     * @param \Magento\Backend\Model\Menu\Builder $subject
     * @param Menu $menu
     *
     * @return Menu
     */
    public function afterGetResult($subject, Menu $menu)
    {
        try {
            $menu = $this->observeMenu($menu);
            //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        } catch (\Exception $ex) {
            //do nothing - do not show our menu
        }

        return $menu;
    }

    /**
     * Observe menu
     *
     * @param Menu $menu
     *
     * @return Menu
     *
     * @throws \Exception
     */
    private function observeMenu(Menu $menu)
    {
        $item = $menu->get(self::BASE_MENU);
        if (!$item) {
            return $menu;
        }

        if (version_compare($this->metadata->getVersion(), '2.2.0', '<')
            || !$this->scopeConfig->isSetFlag(self::SETTING_ENABLE, ScopeInterface::SCOPE_STORE)

        ) {
            $menu->remove(self::BASE_MENU);
            return $menu;
        }

        $origMenu = $this->menuConfig->getMenu();
        $menuItems = $this->getMenuItems($origMenu);
        $configItems = $this->getConfigItems();
     
        foreach ($configItems as $title => $installedModule) {
            $module_title = $installedModule['label'];
            $module_id = $installedModule['id'];
            $module_full_title = $title ;

            if ($title === 'Mavenbird_Shiprestriction') {
                continue;
            }
            if (isset($menuItems[$module_full_title])) {
                $itemsToAdd = $this->cloneMenuItems($menuItems[$module_full_title], $menu);
            } else {
                $itemsToAdd = [];
            }

            if (isset($module_id)) {
                $mavenbirdItem = $this->generateMenuItem(
                    $module_full_title . '::menuconfig',
                    $module_full_title,
                    self::BASE_MENU,
                    'adminhtml/system_config/edit/section/' . $module_id,
                    __('Configuration')->render()
                );
                if (isset($mavenbirdItem)) {
                    $itemsToAdd[] = $mavenbirdItem;
                    
                }
            }
            
            $parentNodeResource = '';
            foreach ($itemsToAdd as $key => $itemToAdd) {
                $itemToAdd = $itemToAdd->toArray();
                
                if (empty($itemToAdd['action'])) {
                    $parentNodeResource = $itemToAdd['resource'];
                    unset($itemsToAdd[$key]);
                }
            }
            if ($itemsToAdd) {
                $itemId = $module_full_title . '::container';
                /** @var \Magento\Backend\Model\Menu\Item $module */
                $module = $this->itemFactory->create(
                    [
                        'data' => [
                            'id'       => $itemId,
                            'title'    => $this->normalizeTitle($module_title),
                            'module'   => $title,
                            'resource' => $this->getValidResource($module_full_title, $parentNodeResource)
                        ]
                    ]
                );
                $menu->add($module, self::BASE_MENU, 1);
                foreach ($itemsToAdd as $copy) {
                    if ($copy) {
                        $menu->add($copy, $itemId, null);
                    }
                }
            }
        }
        
        return $menu;
    }

    /**
     * According to default validation rules, title can't be longer than 50 characters
     *
     * @param string $title
     *
     * @return string
     */
    private function normalizeTitle(string $title): string
    {
        if (mb_strlen($title) > 50) {
            $title = mb_substr($title, 0, 47) . '...';
        }

        return $title;
    }

    /**
     * Get valid resource
     *
     * @param string $installedModule
     * @param string $parentNodeResource
     *
     * @return string
     */
    private function getValidResource($installedModule, $parentNodeResource)
    {
        if (!empty($parentNodeResource)) {
            return $parentNodeResource;
        }
        return $installedModule . "::config";
    }

    /**
     * Clone menu items
     *
     * @param array $menuItems
     * @param Menu $menu
     *
     * @return array
     */
    private function cloneMenuItems($menuItems, Menu $menu)
    {
        $itemsToAdd = [];
        foreach ($menuItems as $link) {
            $mavenbirdItem = $menu->get($link);
            if ($mavenbirdItem) {
                $itemData = $mavenbirdItem->toArray();
                if (isset($itemData['id'], $itemData['resource'], $itemData['title'])) {
                    $itemToAdd = $this->generateMenuItem(
                        $itemData['id'] . 'menu',
                        $this->getModuleFullName($itemData),
                        $itemData['resource'],
                        $itemData['action'],
                        $itemData['title']
                    );

                    if ($itemToAdd) {
                        $itemsToAdd[] = $itemToAdd;
                    }
                }
            }
        }
        return $itemsToAdd;
    }

    /**
     * Get module full name
     *
     * @param string $itemData
     *
     * @return string
     */
    private function getModuleFullName($itemData)
    {
        if (isset($itemData['module'])) {
            return $itemData['module'];
        } else {
            return current(explode('::', $itemData['resource']));
        }
    }

    /**
     * Generate menu items
     *
     * @param int $id
     * @param string $installedModule
     * @param string $resource
     * @param string $url
     * @param string $title
     *
     * @return bool|Menu\Item
     */
    private function generateMenuItem($id, $installedModule, $resource, $url, $title)
    {
       
        try {
            $item = $this->itemFactory->create(
                [
                    'data' => [
                        'id'           => $id,
                        'title'        => $title,
                        'module'       => $installedModule,
                        'action'       => $url,
                        'resource'     => $resource
                    ]
                ]
            );
        } catch (\Exception $ex) {

            $this->logger->warning($ex);
            $item = false;
        }
        
        return $item;
    }

    /**
     * Get install modules
     *
     * @param array $configItems
     *
     * @return array
     */
    private function getInstalledModules($configItems)
    {
        $installed = [];
        $modules = $this->moduleList->getNames();
        $dispatchResult = $this->objectFactory->create(['data' => $modules]);
        $modules = $dispatchResult->toArray();
      /*  echo  "hello<pre>";
        print_r($configItems);
        echo "</pre>";*/
        $Module_Title = [];
        foreach ($modules as $moduleName) {
            if ($moduleName === 'Mavenbird_Shiprestriction' || strpos($moduleName, 'Mavenbird_') === true) {
                $Module_Title['title'] =  $configItems[$moduleName];
            } else {
                continue;
            }
            
        }
        
        return $Module_Title;
    }

    /**
     * Get menuitems
     *
     * @param Menu $menu
     *
     * @return array|null
     */
    private function getMenuItems(Menu $menu)
    {
        if ($this->mavenbirdItems === null) {
            $all = $this->generateMavenbirdItems($menu);
            $this->mavenbirdItems = [];
            foreach ($all as $item) {
                $name = explode('::', $item);
                $name = $name[0];
                if (!isset($this->mavenbirdItems[$name])) {
                    $this->mavenbirdItems[$name] = [];
                }
                $this->mavenbirdItems[$name][] = $item;
            }
        }

        return $this->mavenbirdItems;
    }

    /**
     * Get configitems
     *
     * @return array
     */
    private function getConfigItems()
    {
        $configItems = [];
        $config = $this->generateConfigItems();
        foreach ($config as $item => $section) {
            $name = explode('::', $item);
            $name = $name[0];
            $configItems[$name] = $section;
        }

        return $configItems;
    }

    /**
     * Generate items
     *
     * @param array $menu
     * @return array
     */
    private function generateMavenbirdItems($menu)
    {
        $mavenbird = [];
        foreach ($this->getMenuIterator($menu) as $menuItem) {
            if ($this->isCollectedNode($menuItem)) {
                $mavenbird[] = $menuItem->getId();
            }
            if ($menuItem->hasChildren()) {
                foreach ($this->generateMavenbirdItems($menuItem->getChildren()) as $menuChild) {
                    $mavenbird[] = $menuChild;
                }
            }
        }

        return $mavenbird;
    }

    /**
     * Is collected node
     *
     * @param bool $menuItem
     *
     * @return bool
     */
    private function isCollectedNode($menuItem)
    {
        if (strpos($menuItem->getId(), 'Mavenbird') === false
            || strpos($menuItem->getId(), 'Mavenbird_Shiprestriction') !== false) {
            return false;
        }

        if (empty($menuItem->getAction()) || (strpos($menuItem->getAction(), 'system_config') === false)) {
            return true;
        }

        return false;
    }

    /**
     * Get menu filter iterator
     *
     * @param \Magento\Backend\Model\Menu $menu
     *
     * @return \Magento\Backend\Model\Menu\Filter\Iterator
     */
    private function getMenuIterator($menu)
    {
        return $this->iteratorFactory->create(['iterator' => $menu->getIterator()]);
    }

    /**
     * Get mdule title
     *
     * @param array $name
     *
     * @return string
     */
    private function getModuleTitle($name)
    {
        $result = $name;
        $module = $this->extensionsProvider->getFeedModuleData($name);
        if ($module && isset($module['name'])) {
            $result = $module['name'];
            $result = str_replace(' for Magento 2', '', $result);
        } else {
            $result = str_replace('Mavenbird_', '', $result);
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $result, $matches);
            if (isset($matches[1]) && $matches[1]) {
                $result = implode(' ', $matches[1]);
            }
        }

        return $result;
    }

    /**
     * Generate configitems
     *
     * @param $result
     */
    private function generateConfigItems()
    {
        $result = [];
        $configTabs = $this->configStructure->getTabs();
        $config = $this->findResourceChildren($configTabs, 'mavenbird');

        if ($config) {
            foreach ($config as $item) {
                $data = $item->getData('resource');
                if (isset($data['resource'], $data['id']) && $data['id']) {
                    $result[$data['resource']] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Find resource children
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Iterator $config
     * @param string                                                  $name
     *
     * @return \Magento\Config\Model\Config\Structure\Element\Iterator|null
     */
    private function findResourceChildren($config, $name)
    {
        /** @var \Magento\Config\Model\Config\Structure\Element\Tab|null $currentNode */
        $currentNode = null;
        foreach ($config as $node) {
            if ($node->getId() === $name) {
                $currentNode = $node;
                break;
            }
        }

        if ($currentNode) {
            return $currentNode->getChildren();
        }

        return null;
    }
}
