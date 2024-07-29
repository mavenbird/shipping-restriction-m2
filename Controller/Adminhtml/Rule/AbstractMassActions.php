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
use Magento\Framework\Data\Collection\AbstractDb as Collection;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as Resource;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Skeleton for MassActions.
 *
 * This abstract class provides methods to handle mass actions such as mass deletion and mass status updates.
 */
abstract class AbstractMassActions extends Action
{
    /**
     * Constant for activate action.
     */
    public const ACTIVATE = 'activate';

    /**
     * Constant for inactivate action.
     */
    public const INACTIVATE = 'inactivate';

    /**
     * Constant for delete action.
     */
    public const DELETE = 'delete';

    /**
     * List of allowed actions.
     */
    public const ALLOWED_ACTIONS = [self::ACTIVATE, self::INACTIVATE, self::DELETE];

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param Collection $collection
     * @param Resource $resource
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        Collection $collection,
        Resource $resource
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collection = $collection;
        $this->resource = $resource;
    }

    /**
     * Execute mass action.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->filter->getCollection($this->collection);
        $action = $this->getRequest()->getParam('action');

        if (in_array($action, self::ALLOWED_ACTIONS)) {
            switch ($action) {
                case self::DELETE:
                    $this->massDelete();
                    break;
                case self::INACTIVATE:
                    $this->massStatusUpdate(0);
                    break;
                case self::ACTIVATE:
                    $this->massStatusUpdate(1);
                    break;
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Execute mass deletion.
     *
     * @return void
     */
    protected function massDelete()
    {
        $size = $this->collection->getSize();

        try {
            $this->collection->walk(self::DELETE);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t delete records(s) right now. Please review log and try again.')
            );

            return;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $size));
    }

    /**
     * Execute mass status update.
     *
     * @param int $status
     * @return void
     */
    protected function massStatusUpdate($status)
    {
        $recordsUpdated = 0;

        try {
            /** @var \Magento\Framework\Model\AbstractModel $record */
            foreach ($this->collection->getItems() as $record) {
                $record->setIsActive($status);
                $this->resource->save($record);
                $recordsUpdated++;
            }
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Can\'t update some items. Please review log and try again.')
            );

            return;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $recordsUpdated));
    }
}
