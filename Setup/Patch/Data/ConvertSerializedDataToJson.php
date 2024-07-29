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

namespace Mavenbird\Shiprestriction\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Mavenbird\Core\Setup\SerializedFieldDataConverter;

class ConvertSerializedDataToJson implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * ConvertSerializedDataToJson constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SerializedFieldDataConverter $fieldDataConverter
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SerializedFieldDataConverter $fieldDataConverter
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->fieldDataConverter = $fieldDataConverter;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // Convert serialized data to JSON
        $this->fieldDataConverter->convertSerializedDataToJson(
            'mavenbird_shiprestriction_rule',
            'rule_id',
            ['conditions_serialized']
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        // No dependencies in this example
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        // No aliases in this example
        return [];
    }
}
