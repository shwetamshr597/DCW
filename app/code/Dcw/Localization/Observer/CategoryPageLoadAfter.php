<?php
namespace Dcw\Localization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\ResultFactory;

class CategoryPageLoadAfter implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlInterface,
        \Dcw\Localization\Helper\Data $localizationHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->urlInterface = $urlInterface;
        $this->localizationHelper = $localizationHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /*$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(__METHOD__);
        $logger->info('isAjax='.$this->request->getParam('isAjax'));*/
        
        try {
            $category = $observer->getEvent()->getCategory();
            //$logger->info('category->getDisplayMode='. $category->getDisplayMode());
            $coast=$this->localizationHelper->getCoast();
            $options=$this->localizationHelper->getShipFromValue();
            $selectedCoast = '';
            $collection = [];
            if($coast!="") {
                if(array_key_exists($coast,$options)){        
                    $selectedCoast=$options[$coast];

                    /* ---- checking any product of that category has 'ships_from' or not --- */
                    $collection = $this->productCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addCategoryFilter($category);
                    $collection->addAttributeToFilter('visibility', 4);
                    $collection->addAttributeToFilter('status', 1);
                    $collection->addAttributeToFilter('ships_from', $selectedCoast);
                }
            }

            $currentUrl = $this->urlInterface->getCurrentUrl();

            $currentUrlArr = explode('?',$currentUrl);
           
            if ( count($collection) > 0 && 
            in_array($category->getDisplayMode(),array('PRODUCTS','PRODUCTS_AND_PAGE')) && 
            empty($this->request->getParam('isAjax')) && empty($currentUrlArr[1]) && 
            !empty($selectedCoast)) {
                //$logger->info('by default selected');
                $selectedCoast=$options[$coast];
                $response = $observer->getControllerAction()->getResponse();
                $url = $currentUrlArr[0].'?ships_from='.$selectedCoast.'&product_list_mode=list';
                $response->setRedirect($url);
                $response->sendResponse();
                exit;
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
   
    }
}