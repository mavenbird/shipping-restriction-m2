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


namespace Mavenbird\Shiprestriction\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class SelectSet extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $options = $this->getData('config/options');
        $key = $this->getName();

        $emptyValue = $this->getData('config/emptyValue');

        foreach ($dataSource['data']['items'] as &$item) {
            $newValue = [];
            if (!$item[$key]) {
                $item[$key] = $emptyValue;
                continue;
            }
            if (is_string($item[$key])) {
                $item[$key] = explode(',', $item[$key]);
            }
            foreach ($options as $option) {
                if (in_array($option['value'], $item[$key])) {
                    $newValue[] = $option['label'];
                }
            }
            if (empty($newValue)) {
                $item[$key] = $emptyValue;
            } else {
                $item[$key] = implode('<br/>', $newValue); // @codingStandardsIgnoreLine
            }
        }

        return $dataSource;
    }
}
