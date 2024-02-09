<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

abstract class AbstractProindex extends \Magento\Backend\App\Action
{

    protected function _isAllowed()
    {
        return true;
    }
}
