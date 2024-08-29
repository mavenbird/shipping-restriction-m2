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

declare(strict_types=1);

namespace Mavenbird\Shiprestriction\Model\Feed\FeedTypes;

use Mavenbird\Shiprestriction\Model\Feed\FeedContentProvider;
use Mavenbird\Shiprestriction\Model\ModuleInfoProvider;
use Mavenbird\Shiprestriction\Model\Parser;
use Mavenbird\Shiprestriction\Model\Serializer;
use Magento\Framework\Config\CacheInterface;

class Ads
{
    public const CSV_CACHE_ID = 'mavenbird_shiprestriction_csv';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FeedContentProvider
     */
    private $feedContentProvider;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Ad\Offline
     */
    private $adOffline;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param CacheInterface $cache
     * @param Serializer $serializer
     * @param FeedContentProvider $feedContentProvider
     * @param Parser $parser
     * @param Ad\Offline $adOffline
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        CacheInterface $cache,
        Serializer $serializer,
        FeedContentProvider $feedContentProvider,
        Parser $parser,
        Ad\Offline $adOffline,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->feedContentProvider = $feedContentProvider;
        $this->parser = $parser;
        $this->adOffline = $adOffline;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * Excute
     *
     * @return array
     */
    public function execute(): array
    {
        if ($cache = $this->cache->load(self::CSV_CACHE_ID)) {
            return $this->serializer->unserialize($cache);
        }

        return $this->getFeed();
    }

    /**
     * Get feed
     *
     * @return array
     */
    public function getFeed(): array
    {
        $result = [];

        if (!$this->moduleInfoProvider->isOriginMarketplace()) {
            $content = $this->feedContentProvider->getFeedContent(
                $this->feedContentProvider->getFeedUrl(FeedContentProvider::URN_ADS, true)
            );
            $result = $this->parser->parseCsv($content);
        }

        if (!$result) {
            $result = $this->adOffline->getOfflineData($this->moduleInfoProvider->isOriginMarketplace());
        }
        $result = $this->parser->trimCsvData($result, ['upsell_module_code', 'module_code']);
        $this->cache->save(
            $this->serializer->serialize($result),
            self::CSV_CACHE_ID,
            [self::CSV_CACHE_ID]
        );

        return $result;
    }
}
