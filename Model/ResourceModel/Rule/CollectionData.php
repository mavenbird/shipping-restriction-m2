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

namespace Mavenbird\Shiprestriction\Model\ResourceModel\Rule;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CollectionData extends AbstractCollection
{
    /**
     * Primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Deprecated since 2.4.8
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     * @deprecated Reason for deprecation (e.g., replaced with new class/method)
     * @see \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     */
    protected $coreDate;

    /**
     * Date and time handling with timezone support
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Store manager instance
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Application state object
     *
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * Customers repository interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->coreDate = $coreDate;
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, null);
    }

    /**
     * Add address filter to the collection
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return $this
     */
    public function addAddressFilter($address)
    {
        $groupId = 0;
        $customerId = $address->getQuote()->getCustomerId();

        if ($customerId) {
            $groupId = $this->customerRepositoryInterface->getById($customerId)->getGroupId();
        }

        $this->addActiveFilter()
            ->addCustomerGroupFilter($groupId)
            ->addStoreFilter($this->getStoreId($address))
            ->addDaysFilter();

        return $this;
    }

    /**
     * Retrieve store ID based on the current area code
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return int
     */
    protected function getStoreId($address)
    {
        $quote = $address->getQuote();

        return ($this->state->getAreaCode() == FrontNameResolver::AREA_CODE && $quote) ? $quote->getStoreId()
            : $this->storeManager->getStore()->getStoreId();
    }

    /**
     * Add store filter to the collection
     *
     * @param int|array $storeIds
     * @param bool $withAll
     * @return $this
     */
    public function addStoreFilter($storeIds, $withAll = true)
    {
        $condition = [];
        $field = [];

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        if ($withAll) {
            $condition[] = ['eq' => ''];
            $field[] = 'stores';
            $condition[] = ['eq' => \Magento\Cms\Ui\Component\Listing\Column\Cms\Options::ALL_STORE_VIEWS];
            $field[] = 'stores';
        }

        foreach ($storeIds as $storeId) {
            $condition[] = ['finset' => $storeId];
            $field[] = 'stores';
        }

        if (empty($field)) {
            return $this;
        }
        $this->addFieldToFilter($field, $condition);

        return $this;
    }

    /**
     * Add customer group filter to the collection
     *
     * @param int $groupId
     * @return $this
     */
    public function addCustomerGroupFilter($groupId)
    {
        $groupId = (int)$groupId;
        $this->addFieldToFilter(['cust_groups', 'cust_groups'], [
            [''],
            ['finset' => $groupId]
        ]);

        return $this;
    }

    /**
     * Add active filter to the collection
     *
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);

        return $this;
    }

    /**
     * Add days filter to the collection
     *
     * @return $this
     */
    public function addDaysFilter()
    {
        $localeDate = $this->localeDate->date();

        $this->addFieldToFilter(['days', 'days', 'days'], [
            ['null' => true],
            [''],
            ['finset' => $localeDate->format('N')]
        ]);

        $timeStamp = $localeDate->format('H') * 100 + $localeDate->format('i') + 1;

        $this->getSelect()->where('(time_from IS NULL) OR (time_to IS NULL)
        OR time_from="" OR time_from="0" OR time_to="" OR time_to="0" OR
        (time_from < ' . $timeStamp . ' AND time_to > ' . $timeStamp . ') OR
        (time_from < ' . $timeStamp . ' AND time_to < time_from) OR
        (time_to > ' . $timeStamp . ' AND time_to < time_from)');

        return $this;
    }

    /**
     * Add carriers filter to the collection
     *
     * @param array|string $methodArray
     * @return $this
     */
    public function addCarriersFilter($methodArray)
    {
        return $this->addSetFilter('carriers', $methodArray);
    }

    /**
     * Add method filter to the collection
     *
     * @param array|string $methodArray
     * @return $this
     */
    public function addMethodFilter($methodArray)
    {
        return $this->addSetFilter('methods', $methodArray);
    }

    /**
     * Add set filter for carriers/methods to the collection
     *
     * @param string $field 'methods'|'carriers'
     * @param array|string $methodArray
     * @return $this
     */
    protected function addSetFilter($field, $methodArray)
    {
        if ($field !== 'methods') {
            $field = 'carriers';
        }
        if (is_array($methodArray) && !$methodArray) {
            $this->getSelect()->where('false');

            return $this;
        } elseif (is_string($methodArray)) {
            $this->getSelect()->where($field . ' IS NULL');

            return $this;
        }

        $select = $this->getSelect();
        $connection = $select->getConnection();
        $where = [];

        foreach ($methodArray as $method) {
            $where[] = $connection->prepareSqlCondition($field, ['finset' => $method]);
        }

        $select->where(implode(' ' . \Magento\Framework\DB\Select::SQL_OR . ' ', $where));

        return $this;
    }

    /**
     * Convert collection to array
     *
     * Override default for load before getSize (compatibility with adminGws)
     *
     * @param array $arrRequiredFields
     * @return array
     */
    public function toArray($arrRequiredFields = [])
    {
        $this->load();

        return parent::toArray($arrRequiredFields);
    }
}
