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


namespace Mavenbird\Shiprestriction\Plugin;

/**
 * Class ProductAttributes
 */
abstract class ProductAttributesData
{
    /**
     * @var null
     */
    private $resourceTable;

    /**
     * Resourcetable
     *
     * @param null $resourceTable
     */
    public function __construct($resourceTable = null)
    {
        $this->resourceTable = $resourceTable;
    }

    /**
     * After Get Product attributes
     *
     * @param \Magento\Quote\Model\Quote\Config $subject
     * @param array $attributesTransfer
     * @return array|mixed
     */
    public function afterGetProductAttributes(\Magento\Quote\Model\Quote\Config $subject, $attributesTransfer)
    {
        $attributes = $this->resourceTable->getAttributes();

        foreach ($attributes as $code) {
            $attributesTransfer[] = $code;
        }

        return $attributesTransfer;
    }
}
