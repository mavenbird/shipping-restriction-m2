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


namespace Mavenbird\Shiprestriction\Test\Unit\Model\Modifiers;

use Mavenbird\Shiprestriction\Model\Modifiers\Subtotal;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class SubtotalTest
 *
 * @see Subtotal
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SubtotalTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;

    const SUBTOTAL = 1000;
    const BASE_SUBTOTAL = 100;
    const TAX_AMOUNT = 100;
    const BASE_TAX_AMOUNT = 10;
    const DISCOUNT_AMOUNT = 250;
    const BASE_DISCOUNT_AMOUNT = 25;

    /**
     * @covers Subtotal::modify
     *
     * @dataProvider getTestData
     *
     * @param bool $tax
     * @param bool $discount
     * @param float $expectedSubtotal
     * @param float $expectedBaseSubtotal
     */
    public function testModify($tax, $discount, $expectedSubtotal, $expectedBaseSubtotal)
    {
        /** @var \Magento\Quote\Model\Quote\Address|MockObject $quoteAddress */
        $quoteAddress = $this->createPartialMock(\Magento\Quote\Model\Quote\Address::class, []);
        $quoteAddress
            ->setSubtotal(static::SUBTOTAL)
            ->setBaseSubtotal(static::BASE_SUBTOTAL)
            ->setTaxAmount(static::TAX_AMOUNT)
            ->setBaseTaxAmount(static::BASE_TAX_AMOUNT)
            ->setDiscountAmount(static::DISCOUNT_AMOUNT)
            ->setBaseDiscountAmount(static::BASE_DISCOUNT_AMOUNT);

        /** @var MockObject $config */
        $config = $this->createMock(\Mavenbird\Shiprestriction\Model\Config::class);
        $config->expects($this->once())->method('getTaxIncludeConfig')->willReturn($tax);
        $config->expects($this->once())->method('getUseSubtotalConfig')->willReturn($discount);

        $model = $this->getObjectManager()->getObject(Subtotal::class, ['config' => $config]);
        /** @var \Magento\Quote\Model\Quote\Address $result */
        $result = $model->modify($quoteAddress);

        $this->assertInstanceOf(\Magento\Quote\Model\Quote\Address::class, $result);
        $this->assertFalse(\spl_object_hash($quoteAddress) === \spl_object_hash($model));
        $this->assertEquals($expectedSubtotal, $result->getSubtotal());
        $this->assertEquals($expectedBaseSubtotal, $result->getBaseSubtotal());
        $this->assertArrayHasKey('package_value_with_discount', $result->getData());
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            [false, false, static::SUBTOTAL, static::BASE_SUBTOTAL],
            [true, false, static::SUBTOTAL + static::TAX_AMOUNT, static::BASE_SUBTOTAL + static::BASE_TAX_AMOUNT],
            [
                false,
                true,
                static::SUBTOTAL + static::DISCOUNT_AMOUNT,
                static::BASE_SUBTOTAL + static::BASE_DISCOUNT_AMOUNT
            ],
            [
                true,
                true,
                static::SUBTOTAL + static::DISCOUNT_AMOUNT + static::TAX_AMOUNT,
                static::BASE_SUBTOTAL + static::BASE_DISCOUNT_AMOUNT + static::BASE_TAX_AMOUNT
            ],
        ];
    }
}
