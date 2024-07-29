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


namespace Mavenbird\Shiprestriction\Model\Rule\Factory;

use Mavenbird\Shiprestriction\Model\Rule\Condition\ConditionBuilder as Conditions;

class HandleFactory extends HandlerFactoryAbstract
{
    /**
     * HandleFactory constructor.
     * @param array $handlers
     */
    public function __construct(
        array $handlers
    ) {
        $this->handlers = $handlers;
    }

    /**
     * Reterive condition by type
     *
     * @param string $type
     * @return array
     */
    protected function getConditionsByType($type)
    {
        $typeCode = ucfirst($type);
        $conditions = [];

        if ($condition = $this->getHandlerByType($type)) {
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = [];
            foreach ($conditionAttributes as $code => $label) {
                $attributes[] = [
                    'value' => Conditions::MAVENBIRD_COMMON_RULES_PATH_TO_CONDITIONS . $typeCode . '|' . $code,
                    'label' => $label,
                ];
            }

            $conditions[] = [
                'value' => $attributes,
                'label' => $condition->getConditionLabel(),
            ];
        }

        return $conditions;
    }
}
