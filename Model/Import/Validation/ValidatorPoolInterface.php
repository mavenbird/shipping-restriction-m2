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

interface ValidatorPoolInterface
{
    /**
     * Get validators
     *
     * @return \Mavenbird\Shiprestriction\Model\Import\Validation\ValidatorInterface[]
     */
    public function getValidators();

    /**
     * Add validator
     *
     * @param \Mavenbird\Shiprestriction\Model\Import\Validation\ValidatorInterface $validator
     *
     * @return void
     */
    public function addValidator($validator);
}
