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
namespace Mavenbird\Shiprestriction\Setup\Operation;

/**
 * Convert saved rule data to new format.
 */
class ChangeMethodsFormat
{
    /**
     * @var \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Mavenbird\Shiprestriction\Model\MethodConverter
     */
    private $methods;

    /**
     * @param \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Mavenbird\Shiprestriction\Model\MethodConverter $methods
     */
    public function __construct(
        \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Mavenbird\Shiprestriction\Model\MethodConverter $methods
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->methods = $methods;
    }

    /**
     * Excute
     */
    public function execute()
    {
        $newMethods = $this->methods->getCarrierMethods();

        /** @var \Mavenbird\Shiprestriction\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->collectionFactory->create();
        $rules = $collection->loadData();

        /** @var \Mavenbird\Shiprestriction\Model\Rule $rule */
        foreach ($rules as $rule) {
            $result = [];
            $oldMethods = $rule->getMethods();

            $oldMethods = str_replace("\r\n", "\n", $oldMethods);
            $oldMethods = str_replace("\r", "\n", $oldMethods);
            $oldMethods = trim($oldMethods);

            if (empty($oldMethods)) {
                $rule->setMethods(implode(',', $result));

                continue;
            }

            $oldMethods = array_unique(explode("\n", $oldMethods));

            foreach ($oldMethods as $oldMethod) {
                $oldMethod = explode('::', $oldMethod);
                $methodPos = count($oldMethod) == 1 ? 0 : 1;
                $method = trim($oldMethod[$methodPos]);

                foreach ($newMethods as $currentKey => $currentValue) {
                    if (stripos($currentValue, $method) !== false) {
                        $result[] = $currentKey;
                    }
                }
            }

            $rule->setMethods(implode(',', $result));
        }

        $collection->save();
    }
}
