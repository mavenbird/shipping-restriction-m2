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

namespace Mavenbird\Shiprestriction\Model\OptionProvider\Provider;

/**
 * Provider
 */
class RulesOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    public const PAGE_SIZE = 7500;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $rules = [
            [
                'value' => '0',
                'label' => ' '
            ]
        ];
        $pageNum = 1;

        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->setPageSize(self::PAGE_SIZE)
            ->setCurPage($pageNum);

        $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['rule_id', 'name']);

        while ($pageNum <= $collection->getLastPageNumber()) {
            foreach ($collection->getData() as $rule) {
                $rules[] = [
                    'value' => $rule['rule_id'],
                    'label' => $rule['name']
                ];
            }
            $collection->setCurPage(++$pageNum)->resetData();
        }

        return $rules;
    }
}
