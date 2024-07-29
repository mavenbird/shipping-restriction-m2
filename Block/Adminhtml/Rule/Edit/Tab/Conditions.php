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


namespace Mavenbird\Shiprestriction\Block\Adminhtml\Rule\Edit\Tab;

use Mavenbird\Shiprestriction\Model\ConstantsInterface;


/**
 * Conditions Fieldset
 */
class Conditions extends \Mavenbird\Shiprestriction\Block\Adminhtml\Rule\Edit\Tab\ConditionsData
{
    public const FORM_NAME = 'mavenbird_ship_rule_form';

    /**
     * Construct
     *
     * @param ConstantsInterface::REGISTRY_KEY
     */
    public function _construct()
    {
        $this->setRegistryKey(ConstantsInterface::REGISTRY_KEY);
        parent::_construct();
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        $form = $this->_formFactory->create();
        $renderer = $this->rendererFieldset->setTemplate(
            'Mavenbird_Shiprestriction::ui/conditions/fieldset.phtml'
        )->setFieldSetId(self::RULE_CONDITIONS_FIELDSET_NAMESPACE)->setNewChildUrl(
            $this->getUrl(
                'mavenbird_shiprestriction/rule/newConditionHtml',
                ['form' => self::FORM_NAME, 'form_namespace' => self::RULE_CONDITIONS_FIELDSET_NAMESPACE]
            )
        );

        $fieldset = $form->addFieldset(
            self::RULE_CONDITIONS_FIELDSET_NAMESPACE,
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'data-form-part' => self::FORM_NAME
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );

        $this->setConditionFormName(
            $model->getConditions(),
            self::RULE_CONDITIONS_FIELDSET_NAMESPACE,
            self::FORM_NAME
        );

        return $form;
    }
}
