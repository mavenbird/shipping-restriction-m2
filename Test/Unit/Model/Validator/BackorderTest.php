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


namespace Mavenbird\Shiprestriction\Test\Unit\Model\Validator;

use Mavenbird\Shiprestriction\Model\Rule as CommonRule;
use Mavenbird\Shiprestriction\Model\Validator\Backorder;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class BackorderTest
 *
 * @see Backorder
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class BackorderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;

    /**
     * @covers Backorder::validate
     *
     * @dataProvider getTestData
     *
     * @param int $backorders
     * @param array $items
     * @param bool $expectedResult
     */
    public function testValidate($backorders, $items, $expectedResult)
    {
        /** @var MockObject $rule */
        $rule = $this->getMockBuilder(\Magento\Rule\Model\AbstractModel::class)
            ->setMethods(['getOutOfStock'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $rule->expects($this->once())->method('getOutOfStock')->willReturn($backorders);

        /** @var Backorder $model */
        $model = $this->getObjectManager()->getObject(Backorder::class);

        $this->assertEquals($expectedResult, $model->validate($rule, $items));
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            [CommonRule::BACKORDERS_ONLY, $this->getItemsArray(5), true],
            [CommonRule::BACKORDERS_ONLY, $this->getItemsArray(5, 7), false],
            [CommonRule::NON_BACKORDERS, $this->getItemsArray(0, 7), true],
            [CommonRule::NON_BACKORDERS, $this->getItemsArray(5), false],
            [CommonRule::ALL_ORDERS, $this->getItemsArray(), true],
        ];
    }

    /**
     * @param int $backorders
     * @param int $nonBackorders
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    private function getItemsArray($backorders = 0, $nonBackorders = 0)
    {
        $items = [];

        for ($i = 1; $i < $backorders; ++$i) {
            /** @var MockObject|\Magento\Quote\Model\Quote\Item $item */
            $item = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, []);
            $item->setBackorders(\rand(1, 10));

            $items[] = $item;
        }

        for ($i = 1; $i < $nonBackorders; ++$i) {
            /** @var MockObject|\Magento\Quote\Model\Quote\Item $item */
            $item = $this->createPartialMock(\Magento\Quote\Model\Quote\Item::class, []);
            $item->setBackorders(0);

            $items[] = $item;
        }

        return $items;
    }
}
