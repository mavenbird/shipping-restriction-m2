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

use Magento\Framework\Escaper;

/**
 * Class Parser for parsing xml/csv formats
 */
class Parser
{
    public const RESTRICTED_CHARS = [
        "\r\n",
        "\n",
        "\r"
    ];

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper The escaper instance to handle escaping.
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * ParseXml
     *
     * @param string $xmlContent
     *
     * @return bool|\SimpleXMLElement
     */
    public function parseXml($xmlContent)
    {
        try {
            $xml = new \SimpleXMLElement($xmlContent);
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Parsecsv
     *
     * @codingStandardsIgnoreStart
     * 
     * Using fgetcsv for multiline values. Most optimized variant, Magento don't have alternatives.
     *
     * @param string $csvContent
     *
     * @return array
     */
    public function parseCsv($csvContent)
    {
        try {
            $fp = fopen('php://temp', 'r+');
            fwrite($fp, $csvContent);
            rewind($fp);

            $data = [];
            $header = [];
            $isFirstLine = true;
            while (($row = fgetcsv($fp)) !== false) { // for multiline values
                $row = array_map([$this, "escape"], $row);

                if ($isFirstLine) {
                    $isFirstLine = false;
                    $header = $row;

                    $row = array_combine($header, $row);
                    if (!isset($row['module_code'], $row['tab_name'], $row['upsell_module_code'], $row['text'], $row['priority'])) {
                        return [];
                    }

                    continue;
                }

                $data[] = array_combine($header, $row);
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }
    /** @codingStandardsIgnoreEnd */

    /**
     * Delete space from selected data
     *
     * @param array $data
     * @param array $columns
     *
     * @return array
     */
    public function trimCsvData($data, $columns = [])
    {
        foreach ($data as $k => $element) {
            foreach ($columns as $column) {
                if (isset($element[$column])) {
                    $data[$k][$column] = preg_replace(
                        '/\s+/',
                        '',
                        $element[$column]
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Escape
     *
     * @param string $value
     *
     * @return string
     */
    public function escape($value)
    {
        $value = $this->escaper->escapeHtml($value);
        $value = str_replace(static::RESTRICTED_CHARS, ' ', $value);

        return $value;
    }
}
