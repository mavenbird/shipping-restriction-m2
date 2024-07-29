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

namespace Mavenbird\Shiprestriction\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Request\Http;

class DeleteConditionHandle implements ObserverInterface
{
    /**
     * Not supported conditions to be deleted.
     */
    public const NOT_SUPPORTED_CONDITIONS = [
        'Mavenbird\Conditions\Model\Rule\Condition\Address|payment_method',
    ];

    /**
     * @var Http
     */
    private $request;

    /**
     * DeleteConditionHandle constructor.
     *
     * @param Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    /**
     * Execute method to handle the deletion of unsupported conditions.
     *
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $moduleName = $this->request->getModuleName();

        if ($this->isShiprestrictionModule($moduleName)
            && is_array($conditions = $observer->getAdditional()->getConditions())
        ) {
            $promoConditions = [];

            foreach ($conditions as $condition) {
                if ($this->isAdvancedConditions($condition)) {
                    foreach ($condition['value'] as $key => $condAttribute) {
                        if (in_array($condAttribute['value'], self::NOT_SUPPORTED_CONDITIONS)) {
                            unset($condition['value'][$key]);
                        }
                    }
                }
                $promoConditions[] = $condition;
            }

            $observer->getAdditional()->setConditions($promoConditions);
        }

        return $this;
    }

    /**
     * Check if the condition is an advanced condition.
     *
     * @param array $condition
     * @return bool
     */
    private function isAdvancedConditions($condition)
    {
        return is_array($condition)
            && isset($condition['label'])
            && $condition['label']->getText() === \Mavenbird\Conditions\Model\Constants::MODULE_NAME;
    }

    /**
     * Check if the module name belongs to the common rules module.
     *
     * @param string $moduleName
     * @return bool
     */
    private function isShiprestrictionModule($moduleName)
    {
        return $moduleName === 'mavenbird_shiprestriction'
            || $moduleName === 'mavenbird_shiprules'
            || $moduleName === 'mavenbird_payrestriction';
    }
}
