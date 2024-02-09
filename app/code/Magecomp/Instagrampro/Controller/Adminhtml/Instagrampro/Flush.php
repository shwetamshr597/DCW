<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Instagrampro;

use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Psr\Log\LoggerInterface;

class Flush extends \Magento\Backend\App\Action
{
    protected $_modelInstagramproimageFactory;
    protected $_resultRawFactory;
    protected $_logLoggerInterface;

    public function __construct(
        Context $context,
        InstagramproimageFactory $modelInstagramproimageFactory,
        LoggerInterface $logLoggerInterface,
        RawFactory $resultRawFactory
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->_resultRawFactory = $resultRawFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store');
        try {

            $rejectedImage = $this->_modelInstagramproimageFactory->create()->getCollection()
                ->addFieldToFilter('is_visible', 0);
            $counter = 0;
            foreach ($rejectedImage as $rejected) {
                $rejected->delete();
                $counter++;
            }
            if ($counter > 0) {
                $this->messageManager->addSuccess(__('Total %1 Instagram Feed Deleted Successfully', $counter));
            } else {
                $this->messageManager->addSuccess(__('There Is No any Record For Delete'));
            }
            $this->_redirect('instagrampro/instagrampro/new', ['store' => $storeId]);
        } catch (\Exception $e) {
            $this->_logLoggerInterface->debug($e->getMessage());
            $this->_redirect('instagrampro/instagrampro/new', ['store' => $storeId]);
        }
    }
}
