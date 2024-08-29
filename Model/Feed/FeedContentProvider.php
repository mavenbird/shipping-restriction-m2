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

use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\StoreManagerInterface;
use Laminas\Uri\UriFactory;
use Laminas\Uri\Uri;

/**
 * Class FeedContentProvider for reading file content by url
 */
class FeedContentProvider
{
    /**
     * Path to NEWS
     */
    public const URN_NEWS = 'mavenbird.com/feed-news-segments.xml'; // do not use https:// or http

    /**
     * Path to ADS
     */
    public const URN_ADS = 'mavenbird.com/media/marketing/upsells.csv';

    /**
     * Path to EXTENSIONS
     */
    public const URN_EXTENSIONS = 'mavenbird.com/feed-extensions-m2.xml';

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Uri
     */
    private $baseUrlObject;

    /**
     * @param CurlFactory $curlFactory
     * @param ProductMetadataInterface $productMetadata
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CurlFactory $curlFactory,
        ProductMetadataInterface $productMetadata,
        StoreManagerInterface $storeManager
    ) {
        $this->curlFactory = $curlFactory;
        $this->productMetadata = $productMetadata;
        $this->storeManager = $storeManager;
    }

    /**
     * Get feed content
     *
     * @param string $url
     *
     * @return false|string
     */
    public function getFeedContent($url)
    {
        /** @var Curl $curlObject */
        $curlObject = $this->curlFactory->create();
        $curlObject->setConfig(
            [
                'timeout' => 2,
                'useragent' => $this->productMetadata->getName()
                    . '/' . $this->productMetadata->getVersion()
                    . ' (' . $this->productMetadata->getEdition() . ')'
            ]
        );
        $curlObject->write(\Laminas\Http\Request::METHOD_GET, $url, '1.0');
        $result = $curlObject->read();

        if ($result === false || $result === '') {
            return false;
        }
        $result = preg_split('/^\r?$/m', $result, 2);
        preg_match("/(?i)(\W|^)(Status: 404 File not found)(\W|$)/", $result[0], $notFoundFile);

        if ($notFoundFile) {
            return false;
        }
        $result = trim($result[1]);
        $curlObject->close();

        return $result;
    }

    /**
     * Get feed url
     *
     * @param string $urn
     * @param bool $needFollowLocation
     *
     * @return string
     */
    public function getFeedUrl($urn, $needFollowLocation = false)
    {
        if ($needFollowLocation) {
            return 'https://' . $urn;
        }

        $scheme = $this->getCurrentScheme();
        $protocol = $scheme ?: 'http://';

        return $protocol . $urn;
    }

    /**
     * Get current scheme
     *
     * @return string
     */
    public function getCurrentScheme()
    {
        $scheme = $this->getCoreUrlObject()->getScheme();
        if ($scheme) {
            return $scheme . '://';
        }

        return '';
    }

    /**
     * Get domain zone
     *
     * @return string
     */
    public function getDomainZone()
    {
        $host = $this->getCoreUrlObject()->getHost();
        $host = explode('.', $host);

        return end($host);
    }

    /**
     * Get core url object
     *
     * @return Uri
     */
    private function getCoreUrlObject()
    {
        if ($this->baseUrlObject === null) {
            $url = $this->storeManager->getStore()->getBaseUrl();
            $this->baseUrlObject = UriFactory::factory($url);
        }

        return $this->baseUrlObject;
    }
}
