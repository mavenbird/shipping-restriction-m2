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

namespace Mavenbird\Shiprestriction\Ui\DataProvider;

use Mavenbird\Shiprestriction\Model\MethodConverter;
use Mavenbird\Shiprestriction\Model\ResourceModel\Rule\Collection as ShiprestrictionCollection;

/**
 * Abstract Data Provider for Shiprestriction
 *
 * @method ShiprestrictionCollection getCollection()
 */
class AbstractDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Field name for methods
     */
    public const METHODS_FIELD = 'methods';

    /**
     * @var MethodConverter
     */
    protected $converter;

    /**
     * @var ShiprestrictionCollection
     */
    protected $collection;

    /**
     * AbstractDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ShiprestrictionCollection $collection
     * @param MethodConverter $converter
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ShiprestrictionCollection $collection,
        MethodConverter $converter,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collection;
        $this->converter = $converter;
    }

    /**
     * Retrieve data from the data source
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();

        if (empty($data['totalRecords'])) {
            return $data;
        }

        foreach ($data['items'] as &$item) {
            $item[self::METHODS_FIELD] = $this->converter->convert($item[self::METHODS_FIELD]);
            $item[self::METHODS_FIELD] = $item[self::METHODS_FIELD] ?: __('Any');
        }

        return $data;
    }

    /**
     * Add a filter to the collection
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @return ShiprestrictionCollection
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $collection = $this->getCollection();
        switch ($filter->getField()) {
            case self::METHODS_FIELD:
                $collection->addMethodFilter($this->converter->getCodes($filter->getValue()));
                break;
            case 'carriers':
                $collection->addCarriersFilter([$filter->getValue()]);
                break;
            case 'stores':
                $collection->addStoreFilter($filter->getValue());
                break;
            case 'cust_groups':
                $collection->addCustomerGroupFilter($filter->getValue());
                break;
            default:
                parent::addFilter($filter);
        }

        return $collection;
    }
}
