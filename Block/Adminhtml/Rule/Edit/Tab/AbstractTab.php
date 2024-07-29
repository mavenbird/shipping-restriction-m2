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
 * @package    Mavenbird_shiprestriction
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

namespace Mavenbird\Shiprestriction\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * @deprecated
 * Class AbstractTab
 */
abstract class AbstractTab extends Generic implements TabInterface
{
    /**
     * @var \Mavenbird\Shiprestriction\Model\OptionProvider\Pool
     */
    protected $poolOptionProvider;

    /**
     * @var string
     */
    protected $registryKey = '';

    /**
     * AbstractTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mavenbird\Shiprestriction\Model\OptionProvider\Pool $poolOptionProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mavenbird\Shiprestriction\Model\OptionProvider\Pool $poolOptionProvider,
        array $data = []
    ) {
        $this->poolOptionProvider = $poolOptionProvider;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return $this->getLabel();
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get registry key
     *
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->registryKey;
    }

    /**
     * Set registry key
     *
     * @param string $registryKey
     *
     * @return $this
     */
    public function setRegistryKey($registryKey)
    {
        $this->registryKey = $registryKey;

        return $this;
    }

    /**
     * Get Model
     *
     * @return mixed
     */
    protected function getModel()
    {
        return $this->_coreRegistry->registry($this->getRegistryKey());
    }

    /**
     * Get label
     *
     * @return \Magento\Framework\Phrase|string Tab label and title
     */
    abstract protected function getLabel();

    /**
     * Doing for possibility extend and additional new fields in tab form
     *
     * @param \Magento\Rule\Model\AbstractModel $model
     *
     * @return \Magento\Framework\Data\Form $form
     */
    abstract protected function formInit($model);
}
