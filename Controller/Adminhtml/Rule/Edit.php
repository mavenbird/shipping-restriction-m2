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

use Magento\Framework\Phrase;

/**
 * Edit action.
 */
class Edit extends \Mavenbird\Shiprestriction\Controller\Adminhtml\Rule\AbstractEdit
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mavenbird_Shiprestriction::rule';

    /**
     * @var string $registryKey
     */
    protected $registryKey = \Mavenbird\Shiprestriction\Model\ConstantsInterface::REGISTRY_KEY;

    /**
     * Get default title
     *
     * @return Phrase
     */
    protected function getDefaultTitle()
    {
        return __('Add new Shipping Restriction Rule');
    }

    /**
     * Get error message
     *
     * @param int $ruleId
     *
     * @return Phrase
     */
    protected function getErrorMessage($ruleId)
    {
        return __('Unable to load Shipping Restriction Rule with ID %1. Please review the log and try again.', $ruleId);
    }
}
