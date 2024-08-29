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

namespace Mavenbird\Shiprestriction\Model\Feed\FeedTypes\Ad;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

/**
 * Provides saved ads data.
 * Should not throw any exception.
 */
class Offline
{
    public const OFFLINE_ADS_FILENAME = 'offline_ads.json';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ModuleDirReader
     */
    private $moduleReader;
    /**
     * @param Filesystem $filesystem
     * @param ModuleDirReader $moduleReader
     */
    public function __construct(
        Filesystem $filesystem,
        ModuleDirReader $moduleReader
    ) {
        $this->filesystem = $filesystem;
        $this->moduleReader = $moduleReader;
    }

    /**
     * Get offline data
     *
     * @param bool $market
     *
     * @return array
     */
    public function getOfflineData($market = false)
    {
        /** @var string $etcDir */
        $etcDirPath = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Mavenbird_Shiprestriction'
        );

        $dir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $fileName = $dir->getRelativePath($etcDirPath . '/' . static::OFFLINE_ADS_FILENAME);

        if (!$dir->isExist($fileName)) {
            return [];
        }

        try {
            $content = $dir->readFile($fileName);
        } catch (\Magento\Framework\Exception\FileSystemException $exception) {
            return [];
        }

        // phpcs:disable - Magento functional or Zend functions always throw exception
        $data = json_decode($content, true) ?: [];
        //phpcs:enable

        foreach ($data as &$row) {
            if (isset($row['text_market'])) {
                if ($market) {
                    $row['text'] = $row['text_market'];
                }

                unset($row['text_market']);
            }
        }

        return $data;
    }
}
