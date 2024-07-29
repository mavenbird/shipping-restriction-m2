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

use Mavenbird\Shiprestriction\Model\Rule;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class RuleTest
 *
 * @see Rule
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Rule
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->createPartialMock(Rule::class, []);
    }

    /**
     * @covers Rule::prepareForEdit
     */
    public function testPrepareForEdit()
    {
        $this->model->setData('stores', 12);
        $this->model->setData('cust_groups', [15]);
        $this->model->setCarriers('test');
        $this->model->prepareForEdit();
        $this->assertEquals(['test', ''], $this->model->getMethods());
    }

    /**
     * @covers Rule::getStores
     */
    public function testGetStores()
    {
        $this->model->setData('stores', '1, 2, 3');
        $this->assertEquals([1, 2, 3], $this->model->getStores());
    }

    /**
     * @covers Rule::setStores
     */
    public function testSetStores()
    {
        $this->model->setStores([1, 2, 3]);
        $this->assertEquals('1,2,3', $this->model->getData('stores'));
    }
}