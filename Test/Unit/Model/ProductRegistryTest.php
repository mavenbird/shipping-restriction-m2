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

namespace Mavenbird\Shiprestriction\Test\Unit\Model;

use Mavenbird\Shiprestriction\Model\ProductRegistry;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ProductRegistryTest
 *
 * @see ProductRegistry
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ProductRegistryTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers ProductRegistry::_beforeSave
     */
    public function testAddProduct()
    {
        $model = $this->getObjectManager()->getObject(ProductRegistry::class);
        $model->addProduct('test1');
        $model->addProduct('test2');
        $this->assertEquals(['test1', 'test2'], $model->getProducts());
        $model->addProduct('test1');
        $this->assertEquals(['test1', 'test2'], $model->getProducts());
    }
}
