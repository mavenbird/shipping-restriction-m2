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


namespace Mavenbird\Shiprestriction\Test\Unit\Model\OptionProvider\Provider;

use Mavenbird\Shiprestriction\Model\OptionProvider\Provider\TimesOptionProvider;
use Mavenbird\Shiprestriction\Test\Unit\Traits;

/**
 * Class TimesOptionProviderTest
 *
 * @see TimesOptionProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class TimesOptionProviderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;

    /**
     * @covers TimesOptionProvider::toOptionArray
     */
    public function testToOptionArray()
    {
        /** @var TimesOptionProvider $model */
        $model = $this->getObjectManager()->getObject(TimesOptionProvider::class);
        $result = $model->toOptionArray();

        $this->assertInstanceOf(\Magento\Framework\Phrase::class, current($result)['label']);
        $this->assertCount(24 * 60 / 15 + 1, $result);
        $this->assertEquals(2346, end($result)['value']);

        $sameResult = $model->toOptionArray();
        $this->assertEquals($result, $sameResult);
    }
}
