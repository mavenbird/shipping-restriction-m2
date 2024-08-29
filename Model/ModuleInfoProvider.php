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

namespace Mavenbird\Shiprestriction\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;

class ModuleInfoProvider
{

    /**
     * @var $moduleDataStorage
     */
    protected $moduleDataStorage = [];

     /**
      * @var $restrictedModules
      */
    protected $restrictedModules = [
        'Mavenbird_Shiprestriction',
        'Mavenbird_Router'
    ];

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @param Reader $moduleReader
     * @param File $filesystem
     * @param Serializer $serializer
     */
    public function __construct(
        Reader $moduleReader,
        File $filesystem,
        Serializer $serializer
    ) {
        $this->moduleReader = $moduleReader;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    /**
     * Read info about extension from composer json file
     *
     * @param string $moduleCode
     *
     * @return mixed
     */
    public function getModuleInfo(string $moduleCode)
    {
        if (!isset($this->moduleDataStorage[$moduleCode])) {
            $this->moduleDataStorage[$moduleCode] = [];

            try {
                $dir = $this->moduleReader->getModuleDir('', $moduleCode);
                $file = $dir . '/composer.json';

                $string = $this->filesystem->fileGetContents($file);
                $this->moduleDataStorage[$moduleCode] = $this->serializer->unserialize($string);
            } catch (FileSystemException $e) {
                $this->moduleDataStorage[$moduleCode] = [];
            }
        }

        return $this->moduleDataStorage[$moduleCode];
    }

    /**
     * Check whether module was installed via Magento Marketplace
     *
     * @param string $moduleCode
     *
     * @return bool
     */
    public function isOriginMarketplace(string $moduleCode = 'Mavenbird_Shiprestriction'): bool
    {
        $moduleInfo = $this->getModuleInfo($moduleCode);
        $origin = isset($moduleInfo['extra']['origin']) ? $moduleInfo['extra']['origin'] : null;

        return 'marketplace' === $origin;
    }

    /**
     * Get Restricted Modules
     *
     * @return array
     */
    public function getRestrictedModules(): array
    {
        return $this->restrictedModules;
    }
}
