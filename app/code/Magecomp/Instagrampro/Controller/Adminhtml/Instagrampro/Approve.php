<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Instagrampro;

use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Approve extends \Magento\Backend\App\Action
{
    protected $_modelInstagramproimageFactory;
    protected $_resultRawFactory;
    protected $_logLoggerInterface;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        InstagramproimageFactory $modelInstagramproimageFactory,
        LoggerInterface $logLoggerInterface,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->_resultRawFactory = $resultRawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $imageId = $this->getRequest()->getParam('id');
            $image = $this->_modelInstagramproimageFactory->create()->load($imageId);
            if ($image->getId()) {
                $image->setIsApproved(1)->save();
            }
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData([
                'success' => "success"
            ]);
        } catch (\Exception $e) {
            $this->_logLoggerInterface->debug($e->getMessage());
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData([
                'success' => "error"
            ]);
        }
    }
}
