<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Auth;

use Magecomp\Instagrampro\Helper\Graphapi;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class Collectdata extends Action
{
    protected $resultJsonFactory;
    protected $helper;
    protected $graphapi;
    protected $_storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Graphapi $graphapi,
        StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->graphapi = $graphapi;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
            $storeId = (int)$this->getRequest()->getParam('store');
            $businessData = $this->graphapi->getAuthenticateData($storeId);

            return $result->setData(
                ['success' => true,
                    'ig_business_profilepic' => $businessData['ig_business_profilepic'],
                    'ig_business_username' => $businessData['ig_business_username']
                ]
            );
        } catch (\Exception $e) {
            return $result->setData(
                ['success' => $e->getMessage()]
            );
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
