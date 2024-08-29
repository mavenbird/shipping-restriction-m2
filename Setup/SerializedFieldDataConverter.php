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


namespace Mavenbird\Shiprestriction\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;

class SerializedFieldDataConverter
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $fieldConverter;

    /**
     * @param ResourceConnection $resourceConnection
     * @param AggregatedFieldDataConverter $fieldConverter
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        AggregatedFieldDataConverter $fieldConverter
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->fieldConverter = $fieldConverter;
    }

    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param string|string[] $tableName
     * @param string          $identifierField
     * @param string|string[] $fields
     * @return void
     */
    public function convertSerializedDataToJson($tableName, $identifierField, $fields)
    {
        $convertData = [];

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $convertData[] = $this->getConvertedData($tableName, $identifierField, $field);
            }
        } else {
            $convertData[] = $this->getConvertedData($tableName, $identifierField, $fields);
        }

        $this->fieldConverter->convert(
            $convertData,
            $this->resourceConnection->getConnection()
        );
    }

    /**
     * Get convert data
     *
     * @param string|string[] $tableName
     * @param string          $identifierField
     * @param string          $field
     *
     * @return FieldToConvert
     */
    protected function getConvertedData($tableName, $identifierField, $field)
    {
        $instance = new FieldToConvert(
            SerializedToJson::class,
            $this->resourceConnection->getTableName($tableName),
            $identifierField,
            $field
        );

        return $instance;
    }
}
