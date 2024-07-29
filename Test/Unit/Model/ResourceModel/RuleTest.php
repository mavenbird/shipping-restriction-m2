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

namespace Mavenbird\Shiprestriction\Test\Unit\Model\ResourceModel;

use Mavenbird\Shiprestriction\Model\ResourceModel\Rule;
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
     * @covers Rule::_beforeSave
     */
    public function testBeforeSave()
    {
        $model = $this->getObjectManager()->getObject(Rule::class);
        $object = $this->createPartialMock(\Magento\Framework\Model\AbstractModel::class, []);
        $object->setData('stores', [15]);
        $object->setData('methods', ['test']);

        $this->invokeMethod($model, '_beforeSave', [$object]);
        $this->assertEquals('test', $object->getCarriers());
    }
}
