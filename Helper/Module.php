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

namespace Mavenbird\Shiprestriction\Helper;

use SimpleXMLElement;
use Magento\Framework\HTTP\Client\Curl as CurlClient;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Module
{
    public const EXTENSIONS_PATH = 'shiprestriction_extensions';
    public const URL_EXTENSIONS = 'http://mavenbird.com/feed-extensions-m2.xml';
    public const ALLOWED_DOMAINS = [
        'mavenbird.com',
        'marketplace.magento.com'
    ];

    /**
     * @var string $serializer
     */
    protected $serializer;

    /**
     * @var string $curlClint
     */
    protected $curlClient;

    /**
     * @var string $cache
     */
    protected $cache;

    /**
     * @var array $restrictedModules
     */
    protected $restrictedModules = [
        'Mavenbird_Shiprestriction',
        'Mavenbird_Router'
    ];

    /**
     * @var array $moduleDataStorage
     */
    protected $moduleDataStorage = [];

    /**
     * @var null $modulesData
     */
    private $modulesData = null;

    /**
     * @var string $moduleReader
     */
    private $moduleReader;

    /**
     * @var string $filesystem
     */
    private $filesystem;

    /**
     * @var string $jsonDecoder
     */
    private $jsonDecoder;

    /**
     * @var string $escaper
     */
    private $escaper;

    /**
     * @param JsonSerializer $serializer
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Driver\File $filesystem
     * @param DecoderInterface $jsonDecoder
     * @param CurlClient $curl
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        JsonSerializer $serializer,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Driver\File $filesystem,
        DecoderInterface $jsonDecoder,
        CurlClient $curl,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->curlClient = $curl;
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->jsonDecoder = $jsonDecoder;
        $this->escaper = $escaper;
    }

    /**
     * Retrieve all extensions from cache or reload if not available.
     *
     * @return array|mixed
     */
    public function getAllExtensions()
    {
        $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        if ($serialized === false) {
            $this->reload();
            $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        }
        $result = $this->serializer->unserialize($serialized);

        return $result;
    }

    /**
     * Reload extensions data from feed and cache it.
     */
    public function reload()
    {
        $feedData = [];
        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            $marketplaceOrigin = $this->isOriginMarketplace();

            foreach ($feedXml->channel->item as $item) {
                $code = $this->escaper->escapeHtml((string)$item->code);

                if (!isset($feedData[$code])) {
                    $feedData[$code] = [];
                }

                $title = $this->escaper->escapeHtml((string)$item->title);
                $productPageLink = $marketplaceOrigin ? $item->market_link : $item->link;

                if (!$this->validateLink($productPageLink) || !$this->validateLink($item->guide)) {
                    continue;
                }

                $feedData[$code][$title] = [
                    'name'               => $title,
                    'url'                => $this->escaper->escapeUrl((string)($productPageLink)),
                    'version'            => $this->escaper->escapeHtml((string)$item->version),
                    'conflictExtensions' => $this->escaper->escapeHtml((string)$item->conflictExtensions),
                    'guide'              => $this->escaper->escapeUrl((string)$item->guide),
                ];
            }

            if ($feedData) {
                $this->cache->save($this->serialize($feedData), self::EXTENSIONS_PATH);
            }
        }
    }

    /**
     * Fetch XML data from extensions feed URL.
     *
     * @return bool|SimpleXMLElement
     */
    protected function getFeedData()
    {
        try {
            $curlClient = $this->getCurlClient();
            $curlClient->get(self::URL_EXTENSIONS);
            $data = $curlClient->getBody();
            $xml = new SimpleXMLElement($data);
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Get or initialize Curl client.
     *
     * @return CurlClient
     */
    public function getCurlClient()
    {
        if ($this->curlClient === null) {
            $this->curlClient = new CurlClient();
        }

        return $this->curlClient;
    }

    /**
     * Serialize data using configured serializer.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

     /**
      * Retrieve list of restricted modules.
      *
      * @return array
      */
    public function getRestrictedModules()
    {
        return $this->restrictedModules;
    }

    /**
     * Retrieve module information from composer.json.
     *
     * @param string $moduleCode Module code.
     * @return array
     */
    public function getModuleInfo($moduleCode)
    {
        if (!isset($this->moduleDataStorage[$moduleCode])) {
            $this->moduleDataStorage[$moduleCode] = [];

            try {
                $dir = $this->moduleReader->getModuleDir('', $moduleCode);
                $file = $dir . '/composer.json';

                $string = $this->filesystem->fileGetContents($file);
                $this->moduleDataStorage[$moduleCode] = $this->jsonDecoder->decode($string);
            } catch (\Magento\Framework\Exception\FileSystemException $e) {
                $this->moduleDataStorage[$moduleCode] = [];
            }
        }

        return $this->moduleDataStorage[$moduleCode];
    }

    /**
     * Get feed module data
     *
     * @param string $moduleCode
     */
    public function getFeedModuleData($moduleCode)
    {
        $moduleData = [];
        if ($this->modulesData === null || $this->modulesData === false) {
            $this->modulesData = $this->getAllExtensions();
        }

        if ($this->modulesData && isset($this->modulesData[$moduleCode])) {
            $module = $this->modulesData[$moduleCode];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }

            $moduleData = $module;
        }

        return $moduleData;
    }

    /**
     * Is orgin market place
     *
     * @param string $moduleCode
     */
    public function isOriginMarketplace($moduleCode = 'Mavenbird_Shiprestriction')
    {
        $moduleInfo = $this->getModuleInfo($moduleCode);
        $origin = isset($moduleInfo['extra']['origin']) ? $moduleInfo['extra']['origin'] : null;

        return 'marketplace' === $origin;
    }

    /**
     * Validate link
     *
     * @param string $link
     */
    public function validateLink($link)
    {
        if (! (string) $link) { // fix for xml object
            return true;
        }

        foreach (static::ALLOWED_DOMAINS as $allowedDomain) {
            if (preg_match('/^http[s]?:\/\/' . $allowedDomain . '\/.*$/', $link) === 1) {
                return true;
            }
        }

        return false;
    }
}
