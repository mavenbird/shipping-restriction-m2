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


namespace Mavenbird\Shiprestriction\Model\Import\Validation;

use Mavenbird\Shiprestriction\Model\Import\AbstractImport;

class Validator implements ValidatorInterface
{
    /**
     * @var \Magento\Framework\DataObject
     */
    protected $validationData;
    /**
     * @param \Magento\Framework\DataObject $validationData
     *
     */
    public function __construct(\Magento\Framework\DataObject $validationData)
    {
        $this->validationData = $validationData;
    }

    /**
     * @var $errors
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $messageTemplates = [

    ];

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $behavior)
    {
        return true;
    }

    /**
     * Usual behavior at the end of validation. Help function
     *
     * @return array|bool
     */
    public function validateResult()
    {
        if ($this->errors) {
            return $this->errors;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessages()
    {
        return $this->messageTemplates;
    }

    /**
     * @inheritDoc
     */
    public function addRuntimeError($message, $level)
    {
        if (!isset($this->errors[AbstractImport::RUNTIME_ERRORS])) {
            $this->errors[AbstractImport::RUNTIME_ERRORS] = [];
        }

        $this->errors[AbstractImport::RUNTIME_ERRORS][(string)(__('<b>Error!</b> ')) . $message] = $level;

        return $this;
    }
}
