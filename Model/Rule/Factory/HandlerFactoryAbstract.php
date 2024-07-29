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
 * Class HandlerFactoryAbstract
 */
abstract class HandlerFactoryAbstract implements HandleFactoryInterface
{
    /**
     * @var array
     */
    protected $handlers;

    /**
     * Create Customer_handle
     *
     * @param string $type
     *
     * @return array
     */
    public function create($type = self::CUSTOMER_HANDLE)
    {
        return $this->getConditionsByType($type);
    }

    /**
     * Get handler type
     *
     * @param bool $type
     *
     * @return bool|mixed
     */
    public function getHandlerByType($type)
    {
        return isset($this->handlers[$type]) ? $this->handlers[$type] : false;
    }

    /**
     * Get condition by type
     *
     * @param array $type
     *
     * @return array
     */
    abstract protected function getConditionsByType($type);
}
