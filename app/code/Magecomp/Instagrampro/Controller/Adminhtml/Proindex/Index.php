<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magecomp_Instagrampro::instagrampro');
        $resultPage->addBreadcrumb(__('Magecomp'), __('Magecomp'));
        $resultPage->addBreadcrumb(__('Instagrampro'), __('Instagrampro'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Images'));

        $dataPersistor = $this->_objectManager->get('Magento\Framework\App\Request\DataPersistorInterface');
        $dataPersistor->clear('instagrampro_data');

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magecomp_Instagrampro::instagrampro');
    }
}
