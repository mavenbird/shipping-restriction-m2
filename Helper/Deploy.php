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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Deploy extends AbstractHelper
{

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootWrite;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $rootRead;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    public const DEFAULT_FILE_PERMISSIONS = 0666;
    public const DEFAULT_DIR_PERMISSIONS = 0777;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);

        $this->filesystem = $filesystem;
        $this->rootWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->rootRead = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Deploy folder
     *
     * @param string $folder
     */
    public function deployFolder($folder)
    {
        $from = $this->rootRead->getRelativePath($folder);
        $this->moveFilesFromTo($from, '');
    }

    /**
     * Move files from one directory to another.
     *
     * @param string $fromPath The path of the directory from which files are to be moved.
     * @param string $toPath   The target directory path where files are to be moved.
     */
    public function moveFilesFromTo($fromPath, $toPath)
    {
        //phpcs:ignore
        $baseName = basename($fromPath);
        $files = $this->rootRead->readRecursively($fromPath);
        array_unshift($files, $fromPath);

        foreach ($files as $file) {
            $newFileName = $this->getNewFilePath(
                $file,
                $fromPath,
                ltrim($toPath . '/' . $baseName, '/')
            );

            if ($this->rootRead->isExist($newFileName)) {
                continue;
            }

            if ($this->rootRead->isFile($file)) {
                $this->rootWrite->copyFile($file, $newFileName);

                $this->rootWrite->changePermissions(
                    $newFileName,
                    self::DEFAULT_FILE_PERMISSIONS
                );
            } elseif ($this->rootRead->isDirectory($file)) {
                $this->rootWrite->create($newFileName);

                $this->rootWrite->changePermissions(
                    $newFileName,
                    self::DEFAULT_DIR_PERMISSIONS
                );
            }
        }
    }

    /**
     * Get new file path
     *
     * @param string $filePath
     * @param string $fromPath
     * @param string $toPath
     */
    protected function getNewFilePath($filePath, $fromPath, $toPath)
    {
        return str_replace($fromPath, $toPath, $filePath);
    }
}
