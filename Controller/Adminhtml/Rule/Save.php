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

namespace Mavenbird\Shiprestriction\Controller\Adminhtml\Rule;

class Save extends \Mavenbird\Shiprestriction\Controller\Adminhtml\Rule\AbstractSave
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mavenbird_Shiprestriction::rule';

    /**
     * @var string $dataPersistorKey
     */
    protected $dataPersistorKey = \Mavenbird\Shiprestriction\Model\ConstantsInterface::DATA_PERSISTOR_FORM;

    /**
     * Prepare data
     *
     * @param string $data
     */
    protected function prepareData(&$data)
    {
        if (isset($data['rule_id'])) {
            unset($data['rule_id']);
        }

        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }

        unset($data['rule']);
    }
}
