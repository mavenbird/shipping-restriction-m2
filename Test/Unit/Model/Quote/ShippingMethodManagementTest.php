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

namespace Mavenbird\Shiprestriction\Test\Unit\Model\Quote;

use Mavenbird\Shiprestriction\Model\Quote\ShippingMethodManagement;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ShippingMethodManagementTest
 *
 * @see ShippingMethodManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ShippingMethodManagementTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ShippingMethodManagement
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->createPartialMock(ShippingMethodManagement::class, ['getEstimatedRates']);
    }

    /**
     * @covers ShippingMethodManagement::estimateByAddressId
     */
    public function testEstimateByAddressId()
    {
        $quoteRepository = $this->createMock(\Magento\Quote\Api\CartRepositoryInterface::class);
        $addressRepository = $this->createMock(\Magento\Customer\Api\AddressRepositoryInterface::class);
        $quote = $this->createMock(Quote::class);
        $address = $this->createMock(\Magento\Customer\Api\Data\AddressInterface::class);

        $quoteRepository->expects($this->any())->method('getActive')->willReturn($quote);
        $addressRepository->expects($this->any())->method('getById')->willReturn($address);
        $quote->expects($this->any())->method('isVirtual')->willReturnOnConsecutiveCalls(true, false, false);
        $quote->expects($this->any())->method('getItemsCount')->willReturnOnConsecutiveCalls(0, 5);
        $this->model->expects($this->once())->method('getEstimatedRates');

        $this->setProperty($this->model, 'quoteRepository', $quoteRepository);
        $this->setProperty($this->model, 'addressRepository', $addressRepository);

        $this->assertEquals([], $this->model->estimateByAddressId(1, 2));
        $this->assertEquals([], $this->model->estimateByAddressId(1, 2));
        $this->model->estimateByAddressId(1, 2);
    }

    /**
     * @covers ShippingMethodManagement::estimateByAddress
     */
    public function testEstimateByAddress()
    {
        $quoteRepository = $this->createMock(\Magento\Quote\Api\CartRepositoryInterface::class);
        $quote = $this->createMock(Quote::class);
        $address = $this->createMock(\Magento\Quote\Api\Data\EstimateAddressInterface::class);

        $quote->expects($this->any())->method('isVirtual')->willReturnOnConsecutiveCalls(true, false, false);
        $quote->expects($this->any())->method('getItemsCount')->willReturnOnConsecutiveCalls(0, 5);
        $quoteRepository->expects($this->any())->method('getActive')->willReturn($quote);
        $address->expects($this->once())->method('getCustomAttributes')->willReturn([]);
        $this->model->expects($this->once())->method('getEstimatedRates');

        $this->setProperty($this->model, 'quoteRepository', $quoteRepository);

        $this->assertEquals([], $this->model->estimateByAddress(1, $address));
        $this->assertEquals([], $this->model->estimateByAddress(1, $address));
        $this->model->estimateByAddress(1, $address);
    }
}