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
use Mavenbird\Shiprestriction\Model\LinkValidator;
use Mavenbird\Shiprestriction\Model\ModuleInfoProvider;
use Mavenbird\Shiprestriction\Model\Parser;
use Mavenbird\Shiprestriction\Model\Serializer;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Escaper;

class Extensions
{
    public const EXTENSIONS_CACHE_ID = 'shiprestriction_extensions';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var FeedContentProvider
     */
    private $feedContentProvider;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var LinkValidator
     */
    private $linkValidator;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param Serializer $serializer
     * @param CacheInterface $cache
     * @param FeedContentProvider $feedContentProvider
     * @param Parser $parser
     * @param Escaper $escaper
     * @param LinkValidator $linkValidator
     * @param ModuleInfoProvider $moduleInfoProvider
     */
    public function __construct(
        Serializer $serializer,
        CacheInterface $cache,
        FeedContentProvider $feedContentProvider,
        Parser $parser,
        Escaper $escaper,
        LinkValidator $linkValidator,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->feedContentProvider = $feedContentProvider;
        $this->parser = $parser;
        $this->escaper = $escaper;
        $this->linkValidator = $linkValidator;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * Execute
     *
     * @return array
     */
    public function execute(): array
    {
        if ($cache = $this->cache->load(self::EXTENSIONS_CACHE_ID)) {
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
        $content = $this->feedContentProvider->getFeedContent(
            $this->feedContentProvider->getFeedUrl(FeedContentProvider::URN_EXTENSIONS)
        );
        $feedXml = $this->parser->parseXml($content);

        if (isset($feedXml->channel->item)) {
            $result = $this->prepareFeedData($feedXml);
        }
        $this->cache->save(
            $this->serializer->serialize($result),
            self::EXTENSIONS_CACHE_ID,
            [self::EXTENSIONS_CACHE_ID]
        );

        return $result;
    }

    /**
     * Prepare feed data
     *
     * @param \SimpleXMLElement $feedXml
     * @return array
     */
    private function prepareFeedData(\SimpleXMLElement $feedXml): array
    {
        $marketplaceOrigin = $this->moduleInfoProvider->isOriginMarketplace();
        $result = [];

        foreach ($feedXml->channel->item as $item) {
            $code = $this->escaper->escapeHtml($item->code ?? '');

            if (!isset($result[$code])) {
                $result[$code] = [];
            }
            $title = $this->escaper->escapeHtml($item->title ?? '');
            $productPageLink = $marketplaceOrigin ? $item->market_link : $item->link;

            if (!$this->linkValidator->validate((string)$productPageLink)
                || !$this->linkValidator->validate((string)($item->guide ?? ''))
            ) {
                continue;
            }
            $result[$code][$title] = [
                'name' => $title,
                'url' => $this->escaper->escapeUrl((string)($productPageLink ?? '')),
                'version' => $this->escaper->escapeHtml((string)($item->version ?? '')),
                'conflictExtensions' => $this->escaper->escapeHtml((string)($item->conflictExtensions ?? '')),
                'guide' => $this->escaper->escapeUrl((string)($item->guide ?? ''))
            ];
        }

        return $result;
    }
}
