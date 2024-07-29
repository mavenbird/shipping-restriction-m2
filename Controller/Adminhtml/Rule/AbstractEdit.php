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
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Skeleton for Edit Action.
 *
 * This abstract class serves as a foundation for edit actions related to rules in the admin panel.
 */
abstract class AbstractEdit extends Action
{
    /**
     * @var string
     */
    protected $registryKey = '';

    /**
     * @var Rule
     */
    private $ruleModel;

    /**
     * @var AbstractDb
     */
    private $resource;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param Rule $ruleModel
     * @param AbstractDb $resource
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        Rule $ruleModel,
        AbstractDb $resource,
        Registry $coreRegistry
    ) {
        parent::__construct($context);

        $this->ruleModel = $ruleModel;
        $this->resource = $resource;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $ruleId = $this->getRequest()->getParam('id');

        $this->coreRegistry->register($this->registryKey, $this->ruleModel);

        if ($ruleId) {
            try {
                $this->resource->load($this->ruleModel, $ruleId);
                $resultPage->getConfig()->getTitle()->prepend($this->ruleModel->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                return $this->_redirect('*/*');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    $this->getErrorMessage($ruleId)
                );

                return $this->_redirect('*/*');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend($this->getDefaultTitle());
        }

        return $resultPage;
    }

    /**
     * Retrieve default title.
     *
     * @return Phrase
     */
    abstract protected function getDefaultTitle();

    /**
     * Retrieve error message.
     *
     * @param int $ruleId
     * @return Phrase
     */
    abstract protected function getErrorMessage($ruleId);
}
