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

use Mavenbird\Shiprestriction\Helper\Module;
use Mavenbird\Shiprestriction\Model\Ad\Offline as AdOfflineProvider;
use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Config\CacheInterface;

/**
 * Class AdsProvider provide ads data
 */
class AdsProvider
{
    public const CSV_CACHE_ID = 'mavenbird_shiprestriction_csv';

    /**
     * @var FeedContent
     */
    private $feedContent;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FullModuleList
     */
    private $moduleList;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AdOfflineProvider
     */
    private $adOffline;

    /**
     * @var Module
     */
    private $moduleHelper;
    /**
     * @param FeedContent $feedContent
     * @param Parser $parser
     * @param CacheInterface $cache
     * @param Serializer $serializer
     * @param FullModuleList $moduleList
     * @param Config $config
     * @param AdOfflineProvider $adOffline
     * @param Module $moduleHelper
     */
    public function __construct(
        FeedContent $feedContent,
        Parser $parser,
        CacheInterface $cache,
        Serializer $serializer,
        FullModuleList $moduleList,
        Config $config,
        AdOfflineProvider $adOffline,
        Module $moduleHelper
    ) {
        $this->feedContent = $feedContent;
        $this->parser = $parser;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->moduleList = $moduleList;
        $this->config = $config;
        $this->adOffline = $adOffline;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Execute
     *
     * @return array
     */
    public function execute()
    {
       # $this->cache->remove(self::CSV_CACHE_ID);
        if ($cache = $this->cache->load(self::CSV_CACHE_ID)) {
            $result = $this->serializer->unserialize($cache);
        } else {
            $result = [];

            if (!$this->moduleHelper->isOriginMarketplace()) {
                $content = $this->feedContent->getFeedContent($this->feedContent->getFeedUrl(Feed::URN_ADS, true));
                $result = $this->parser->parseCsv($content);
            }

            if (!$result) {
                $result = $this->adOffline->getOfflineData($this->moduleHelper->isOriginMarketplace());
            }

            $result = $this->parser->trimCsvData($result, ['upsell_module_code', 'module_code']);

            $this->cache->save(
                $this->serializer->serialize($result),
                self::CSV_CACHE_ID,
                [self::CSV_CACHE_ID],
                (int)$this->config->getFrequencyInSec()
            );
        }

        return $result;
    }

    /**
     * Get display Advertise
     *
     * @param string $moduleCode
     *
     * @return array
     */
    public function getDisplayAdvertise($moduleCode)
    {
        $adsData = $this->execute();
        $displayedAdvertise = $this->getActiveAdvertise($adsData, $moduleCode);

        return $displayedAdvertise;
    }

    /**
     * Validate link
     *
     * @param string $link
     *
     * @return bool
     */
    public function validateLink($link)
    {
        return $this->moduleHelper->validateLink($link);
    }

    /**
     * Sorting $sortAds by priority in CSV
     *
     * @param array $currentElement
     * @param array $nextElement
     *
     * @return int
     */
    protected function sortByPriority($currentElement, $nextElement)
    {
        if ($currentElement['priority'] == $nextElement['priority']) {
            return 0;
        }

        return ($currentElement['priority'] < $nextElement['priority']) ? -1 : 1;
    }

    /**
     * Get active advertise
     *
     * @param array $adsData
     * @param string $moduleCode
     *
     * @return array
     */
    private function getActiveAdvertise($adsData, $moduleCode)
    {
        $upsellModuleAds = $this->getUpsell($adsData, $moduleCode);

        foreach ($upsellModuleAds as $advertise) {
            if (isset($advertise['upsell_module_code']) && !empty($advertise['upsell_module_code'])
                && !$this->moduleList->has($advertise['upsell_module_code'])
            ) {

                return $advertise;
            }
        }

        return [];
    }

    /**
     * Get upsell
     *
     * @param array $adsData
     * @param string $moduleCode
     *
     * @return array
     */
    private function getUpsell($adsData, $moduleCode)
    {
        $sortAds = [];
        $emptyPriority = [];

        foreach ($adsData as $moduleAds) {
            if ((isset($moduleAds['module_code']) && $moduleAds['module_code'] === $moduleCode)
                || ($this->isAllowedEverywhere($moduleAds) && 'Mavenbird_Shiprestriction' !== $moduleCode)
            ) {
                if (isset($moduleAds['priority'])) {
                    $moduleAds['priority'] = str_replace(' ', '', $moduleAds['priority']);

                    if ($moduleAds['priority'] === '' || !is_numeric($moduleAds['priority'])) {
                        $emptyPriority[] = $moduleAds;
                    } else {
                        $priority = (int)$moduleAds['priority'];

                        while (isset($sortAds[$priority])) {
                            $priority++;
                        }
                        $sortAds[$priority] = $moduleAds;
                    }
                }
            }
        }

        usort($sortAds, [$this, 'sortByPriority']);

        if (!empty($emptyPriority)) {
            end($sortAds);         // move the internal pointer to the end of the array
            $lastKeySortAds = key($sortAds) + 1;

            foreach ($emptyPriority as $emptyPriorityElement) {
                $sortAds[$lastKeySortAds] = $emptyPriorityElement;
                $lastKeySortAds++;
            }
        }

        return $sortAds;
    }

    /**
     * Is allowed everywhere
     *
     * @param array $moduleAds
     *
     * @return bool
     */
    private function isAllowedEverywhere($moduleAds)
    {
        return isset($moduleAds['module_code']) && '*' === $moduleAds['module_code'];
    }
}
