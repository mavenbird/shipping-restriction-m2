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

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class EncodingValidator extends Validator implements ValidatorInterface
{
    public const ENCODING_ERROR = 'encodingError';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ENCODING_ERROR => '<b>Error!</b> Row has characters with Wrong Encoding'
    ];

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];
        foreach ($rowData as $value) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $this->errors[self::ENCODING_ERROR] = ProcessingError::ERROR_LEVEL_CRITICAL;
                break;
            }
        }

        return parent::validateResult();
    }
}
