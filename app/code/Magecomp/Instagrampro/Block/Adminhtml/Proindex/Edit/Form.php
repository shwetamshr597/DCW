<?php

namespace Magecomp\Instagrampro\Block\Adminhtml\Proindex\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        try {
            /** @var \Magento\Framework\Data\Form $form */
            $form = $this->_formFactory->create(
                ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
            );
            $form->setUseContainer(true);
            $this->setForm($form);
        } catch (\Exception $exception) {

        }
        return parent::_prepareForm();
    }
}
