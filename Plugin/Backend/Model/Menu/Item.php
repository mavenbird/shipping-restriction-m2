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
use Magento\Backend\Model\Menu\Item as NativeItem;

class Item
{
    public const BASE_MARKETPLACE = 'Mavenbird_Shiprestriction::marketplace';

    public const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_user_guide';

    public const MARKET_URL = 'https://mavenbird.com/magento-2-extensions.html'
    . '?utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_catalog';

    public const MAGENTO_MARKET_URL = 'https://marketplace.magento.com/partner/Mavenbird';

    /**
     * @var ExtensionsProvider
     */
    private $extensionsProvider;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param ExtensionsProvider $extensionsProvider
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        ExtensionsProvider $extensionsProvider,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->extensionsProvider = $extensionsProvider;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * After get url
     *
     * @param NativeItem $subject
     * @param string $url
     *
     * @return string
     */
    public function afterGetUrl(NativeItem $subject, $url)
    {
        $id = $subject->getId();
        if ($id === self::BASE_MARKETPLACE) {
            $url = $this->moduleInfoProvider->isOriginMarketplace() ? self::MAGENTO_MARKET_URL : self::MARKET_URL;
        }

        /* we can't add guide link into item object - find link again */
        if (strpos($id, '::menuguide') !== false
            && strpos($id, 'Mavenbird') !== false
        ) {
            $moduleCode = explode('::', $subject->getId());
            $moduleCode = $moduleCode[0];
            $moduleInfo = $this->extensionsProvider->getFeedModuleData($moduleCode);
            if (isset($moduleInfo['guide']) && $moduleInfo['guide']) {
                $url = $moduleInfo['guide'];
                $seoLink = self::SEO_PARAMS;
                if (strpos($url, '?') !== false) {
                    $seoLink = str_replace('?', '&', $seoLink);
                }
                $url .= $seoLink;
            } else {
                $url = '';
            }
        }

        return $url;
    }
}
