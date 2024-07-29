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

/**
 * Interface HandleFactoryInterface
 */
interface HandleFactoryInterface
{
    public const CUSTOMER_HANDLE = 'customer';
    public const ORDERS_HANDLE = 'orders';

    public const TOTAL_COMBINE_HANDLE = 'total';

    /**
     * Create customer handle based on type
     *
     * @param string $type
     *
     * @return array
     */
    public function create($type = self::CUSTOMER_HANDLE);
}
