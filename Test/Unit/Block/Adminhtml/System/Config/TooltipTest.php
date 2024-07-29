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

namespace Mavenbird\Shiprestriction\Test\Unit\Block\Adminhtml\System\Config;

use Mavenbird\Shiprestriction\Block\Adminhtml\System\Config\Tooltip;
use Mavenbird\Shiprestriction\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class TooltipTest
 *
 * @see Tooltip
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class TooltipTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Save::_renderValue
     */
    public function testRenderValue()
    {
        $result = '<td class="value with-tooltip">test<div '
            . 'data-bind="{\'tooltip\':{\'trigger\':\'[data-tooltip-trigger=trigger]\',\'action\':\'click\','
            . '\'delay\':0,\'track\':false,\'position\':\'top\'}}" class="hidden">tooltip</div><div '
            . 'class="tooltip" data-tooltip-trigger="trigger"><span class="help"><span></span></div>'
            . '<p class="note"><span>comment</span></p></td>';
        $block = $this->createPartialMock(Tooltip::class, ['_getElementHtml']);
        $element = $this->getObjectManager()->getObject(\Magento\Framework\Data\Form\Element\Fieldset::class);

        $block->expects($this->any())->method('_getElementHtml')->willReturn('test');

        $this->assertEquals('<td class="value">test</td>', $this->invokeMethod($block, '_renderValue', [$element]));

        $element->setTooltip('tooltip');
        $element->setComment('comment');
        $this->assertEquals($result, $this->invokeMethod($block, '_renderValue', [$element]));
    }
}
