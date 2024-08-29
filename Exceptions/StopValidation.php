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


namespace Mavenbird\Shiprestriction\Exceptions;

class StopValidation extends \Exception
{
    /**
     * @var array|bool
     */
    protected $validateResult;

    /**
     * @param array|bool $validateResult
     */
    public function __construct($validateResult)
    {
        $this->validateResult = $validateResult;
    }

    /**
     * Get validate result
     *
     * @return array|bool
     */
    public function getValidateResult()
    {
        return $this->validateResult;
    }
}
