<?php

namespace Magecomp\Instagrampro\Block\Adminhtml\Proindex;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('instagrampro_data')->getId()) {
            return __("Edit Image '%1'", $this->escapeHtml($this->_coreRegistry->registry('instagrampro_data')->getTitle()));
        } else {
            return __('New Image');
        }
    }

    public function getSaveUrl()
    {
        $storeId = $this->getRequest()->getParam('store');
        return $this->getUrl('*/*/save', ['_current' => true, 'store' => $storeId, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    protected function _construct()
    {
        $this->_objectId = 'image_id';
        $this->_blockGroup = 'Magecomp_Instagrampro';
        $this->_controller = 'adminhtml_proindex';

        parent::_construct();

        if ($this->_isAllowedAction('Magecomp_Instagrampro::instagrampro')) {
            $this->buttonList->update('save', 'label', __('Save Image Information'));
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Magecomp_Instagrampro::instagrampro')) {
            $this->addButton(
                'delete',
                [
                    'label' => __('Delete Entry'),
                    'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                        . ','
                        . json_encode($this->getDeleteUrl())
                        . ')',
                    'class' => 'scalable delete',
                    'level' => -1
                ]
            );
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getDeleteUrl(array $args = [])
    {
        return $this->getUrl('*/*/delete', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        $storeId = $this->getRequest()->getParam('store');
        return $this->getUrl('*/*/save', ['_current' => true, 'store' => $storeId, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
