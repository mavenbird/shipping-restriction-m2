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


namespace Mavenbird\Shiprestriction\Model\Feed;

use Mavenbird\Shiprestriction\Model\Feed\FeedTypes\Ads;
use Magento\Framework\Module\FullModuleList;

/**
 * Class AdsProvider provide ads data
 */
class AdsProvider
{
    /**
     * @var FullModuleList
     */
    private $moduleList;

    /**
     * @var Ads
     */
    private $adsFeed;

    /**
     * @param FullModuleList $moduleList
     * @param Ads $adsFeed
     */
    public function __construct(
        FullModuleList $moduleList,
        Ads $adsFeed
    ) {
        $this->moduleList = $moduleList;
        $this->adsFeed = $adsFeed;
    }

    /**
     * Get display advertise
     *
     * @param string $moduleCode
     *
     * @return array
     */
    public function getDisplayAdvertise($moduleCode)
    {
        $adsData = $this->adsFeed->execute();

        return $this->getActiveAdvertise($adsData, $moduleCode);
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
