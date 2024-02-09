<?php
namespace Dcw\CircularDesignerTool\Controller\Adminhtml;

abstract class CircularDesignerToolTab extends \Dcw\CircularDesignerTool\Controller\Adminhtml\AbstractAction
{
    protected function _isAllowed()
    {
        return $this->_authorization
        ->isAllowed('Dcw_CircularDesignerTool::circulardesignertool_circulardesignertooltab');
    }
}
