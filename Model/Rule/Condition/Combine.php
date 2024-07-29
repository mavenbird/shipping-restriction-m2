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


namespace Mavenbird\Shiprestriction\Model\Rule\Condition;

class Combine extends \Mavenbird\Shiprestriction\Model\Rule\Condition\CombineData
{
    public const MAVENBIRD_SHIP_RESTRICTION_PATH_TO_CONDITIONS = 'Mavenbird\Shiprestriction\Model\Rule\Condition\\';

    /**
     * @var string
     */
    protected $conditionsAddressPath = self::MAVENBIRD_SHIP_RESTRICTION_PATH_TO_CONDITIONS .'Address';

    /**
     * Combine constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Address $conditionAddress
     * @param \Mavenbird\Shiprestriction\Model\Rule\Factory\HandleFactory $handleFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Mavenbird\Shiprestriction\Model\Rule\Factory\CombineHandleFactory $combineHandleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Address $conditionAddress,
        \Mavenbird\Shiprestriction\Model\Rule\Factory\HandleFactory $handleFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Mavenbird\Shiprestriction\Model\Rule\Factory\CombineHandleFactory $combineHandleFactory,
        array $data = []
    ) {
        $this->_conditionAddress = $conditionAddress;
        $this->setType(self::MAVENBIRD_SHIP_RESTRICTION_PATH_TO_CONDITIONS . 'Combine');

        parent::__construct(
            $context,
            $eventManager,
            $conditionAddress,
            $handleFactory,
            $moduleManager,
            $combineHandleFactory,
            $data
        );
    }
}
