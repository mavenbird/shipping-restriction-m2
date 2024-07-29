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

use Mavenbird\Shiprestriction\Model\Rule;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as Resource;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Abstract class for saving rules in admin panel.
 */
abstract class AbstractSave extends Action
{
    /**
     * @var string
     */
    protected $dataPersistorKey = '';

    /**
     * @var Rule
     */
    private $ruleModel;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Constructor.
     *
     * @param Action\Context          $context        Context instance.
     * @param Rule                    $ruleModel      Rule model instance.
     * @param Resource                $resource       Resource model instance.
     * @param DataPersistorInterface  $dataPersistor  Data persistor instance.
     */
    public function __construct(
        Action\Context $context,
        Rule $ruleModel,
        Resource $resource,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Execute save action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $ruleId = (int) $this->getRequest()->getParam('rule_id');

            try {
                if ($ruleId) {
                    $this->resource->load($this->ruleModel, $ruleId);
                }

                $this->prepareData($data);

                $this->ruleModel->addData($data);
                $this->ruleModel->loadPost($data); // Load additional data

                $this->resource->save($this->ruleModel);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $this->ruleModel->getId()]);
                }

                return $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception, $exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the rule data. Please review log and try again.')
                );
            }
            $this->dataPersistor->set($this->dataPersistorKey, $this->getRequest()->getParams());

            if (!empty($ruleId)) {
                return $this->_redirect('*/*/edit', ['id' => $ruleId]);
            } else {
                return $this->_redirect('*/*/new');
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Prepare data before saving.
     *
     * @param array $data Data to be prepared.
     * @return void
     */
    abstract protected function prepareData(&$data);
}
