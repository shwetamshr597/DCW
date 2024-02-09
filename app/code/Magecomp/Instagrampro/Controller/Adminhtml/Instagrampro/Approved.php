<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Instagrampro;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Approved extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_template = 'instagrampro/approved.phtml';

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
        $resultPage->getConfig()->getTitle()->prepend(__('Instagrampro'));
        $resultPage->getConfig()->getTitle()->prepend(__('Approved Images'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magecomp_Instagrampro::instagrampro');
    }
}
