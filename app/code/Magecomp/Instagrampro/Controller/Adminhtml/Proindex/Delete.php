<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Backend\App\Action\Context;

class Delete extends AbstractProindex
{
    protected $_modelInstagramproimageFactory;

    public function __construct(
        Context $context,
        InstagramproimageFactory $modelInstagramproimageFactory
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('id')) {
            try {
                $instagramproModel = $this->_modelInstagramproimageFactory->create();
                $instagramproModel->setId($this->getRequest()->getParam('id'))->delete();

                $this->messageManager->addSuccess('Image successfully deleted');

                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }
}
