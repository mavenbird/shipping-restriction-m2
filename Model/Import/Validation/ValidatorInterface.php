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

/**
 * @since 1.4.6
 */
interface ValidatorInterface
{
    /**
     * Return array with error codes. Return true on validation pass
     *
     * @param array  $rowData
     * @param string $behavior
     *
     * @throws \Mavenbird\Shiprestriction\Exceptions\StopValidation
     * @return array|bool
     */
    public function validateRow(array $rowData, $behavior);

    /**
     * Return array: error_code => error_message
     *
     * @return array
     */
    public function getErrorMessages();

    /**
     * Add run time
     *
     * @param string $message
     * @param int $level
     *
     * @return ValidatorInterface
     */
    public function addRuntimeError($message, $level);
}
