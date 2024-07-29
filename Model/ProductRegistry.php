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

class ProductRegistry
{
    /**
     * @var array
     */
    protected $products = [];

    /**
     * Add product
     *
     * @param array $name
     * @return $this
     */
    public function addProduct($name)
    {
        if (!in_array($name, $this->products)) {
            $this->products [] = $name;
        }

        return $this;
    }

    /**
     * Clear products
     *
     * @return $this
     */
    public function clearProducts()
    {
        $this->products = [];

        return $this;
    }

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set products
     *
     * @param array $products
     *
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->products = $products;

        return $this;
    }
}
