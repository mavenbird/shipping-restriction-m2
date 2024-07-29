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

namespace Mavenbird\Shiprestriction\Model\Rule\Condition\Total;

use Magento\Rule\Model\Condition\Context;

class Status extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    private $status;

    /**
     * Status constructor.
     * @param Context $context
     * @param \Magento\Sales\Model\Order\Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Status $status,
        array $data = []
    ) {
        $this->setType(\Mavenbird\Shiprestriction\Model\Rule\Condition\Total\Status::class)
            ->setValue(null);
        $this->status = $status;
        parent::__construct($context, $data);
    }

    /**
     * Load Attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $statuses = $this->status->getResourceCollection()->getData();
        $options  = $this->getAttributeOptions();

        foreach ($statuses as $status) {
            $options[$status['status']] = $status['label'];
        }

        $this->setAttributeOption($options);

        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '='  => __('is'),
                '<>' => __('is not'),
            ]
        );

        return $this;
    }

    /**
     * As Html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            __(
                sprintf(
                    "Order Status %s %s",
                    $this->getOperatorElement()->getHtml(),
                    $this->getAttributeElement()->getHtml()
                )
            );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * Validate model
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return array
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $result = ['status' => $this->getOperatorForValidate() . "'" . $this->getAttributeElement()->getValue() . "'"];

        return $result;
    }
}
