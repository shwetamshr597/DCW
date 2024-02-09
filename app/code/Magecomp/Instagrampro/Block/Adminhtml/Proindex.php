<?php

namespace Magecomp\Instagrampro\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Proindex extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_proindex';
        $this->_blockGroup = 'Magecomp_Instagrampro';
        $this->_headerText = __('Manage Images');

        parent::_construct();
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
