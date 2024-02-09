<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

class Grid extends AbstractProindex
{
    public function execute()
    {
        $this->_initAction();
        $this->renderLayout();
    }
}
