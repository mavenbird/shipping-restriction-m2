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

namespace Mavenbird\Shiprestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;

/**
 * Skeleton for Delete Action.
 */
abstract class AbstractDelete extends Action
{
    /**
     * @var \Mavenbird\Shiprestriction\Model\Rule
     */
    private $ruleModel;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    private $resource;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param \Mavenbird\Shiprestriction\Model\Rule $ruleModel
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        Action\Context $context,
        \Mavenbird\Shiprestriction\Model\Rule $ruleModel,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
    ) {
        parent::__construct($context);

        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('id');

        if ($ruleId) {
            try {
                $this->resource
                    ->load($this->ruleModel, $ruleId)
                    ->delete($this->ruleModel);
                $this->messageManager->addSuccessMessage(__('You deleted the item.'));

                return $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception, $exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete item right now. Please review log and try again.')
                );

                return $this->_redirect('*/*/edit', ['id' => $ruleId]);
            }

            return $this->_redirect('*/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an item to delete.'));

        return $this->_redirect('*/*/');
    }
}
