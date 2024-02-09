<?php
namespace Dcw\FlooringCalculation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->timezoneInterface = $timezoneInterface;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_productloader = $_productloader;
        $this->request = $request;
    }

    public function getExpectedShipDate($product_id)
    {
        
        $_product = $this->_productloader->create()->load($product_id);

        $min_expected_days_ship = $_product->getMinExpectedDaysShip();
        $max_expected_days_ship = $_product->getMaxExpectedDaysShip();

        $weekend = $this->scopeConfig
        ->getValue('general/locale/weekend', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $weekendArr = explode(',', $weekend);

        $current_date = $this->timezoneInterface->date()->format('Y-m-d');

       /* ------------------ expected Days Ship From START ----------------------*/
       
        $d = new \DateTime($current_date);
        $t = $d->getTimestamp();
        for ($i=0; $i< $min_expected_days_ship; $i++) {
             // add 1 day to timestamp
            $addDay = 86400;
    
            // get what day it is next day
            $nextDay = date('w', ($t+$addDay));
            
            if (in_array($nextDay, $weekendArr)) {
                $i--;
            }
    
            // modify timestamp, add 1 day
            $t = $t+$addDay;
    
        }
        $d->setTimestamp($t);
        //echo '<br>'.$d->format('Y-m-d');
        $expectedDaysShipFrom = $d->format('M j');
         /* ------------------ expected Days Ship From END ----------------------*/

         /* ------------------ expected Days Ship To START ----------------------*/
        $d = new \DateTime($current_date);
        $t = $d->getTimestamp();
        for ($i=0; $i< $max_expected_days_ship; $i++) {
            // add 1 day to timestamp
            $addDay = 86400;
   
            // get what day it is next day
            $nextDay = date('w', ($t+$addDay));
           
            if (in_array($nextDay, $weekendArr)) {
                $i--;
            }
   
            // modify timestamp, add 1 day
            $t = $t+$addDay;
   
        }
        $d->setTimestamp($t);
        $expectedDaysShipTo = $d->format('M j');
        /* ------------------ expected Days Ship To END ----------------------*/

        if (empty($min_expected_days_ship) && !empty($max_expected_days_ship)) {
            $ship_between_html = 'Ship by '.$expectedDaysShipTo;
        } elseif (!empty($min_expected_days_ship) && empty($max_expected_days_ship)) {
            $ship_between_html = 'Ship after '.$expectedDaysShipFrom;
        } elseif (!empty($min_expected_days_ship) && !empty($max_expected_days_ship)) {
            $ship_between_html = 'Ships between '.$expectedDaysShipFrom.' - '.$expectedDaysShipTo;
        } else {
            $ship_between_html = '';
        }

        return $ship_between_html;
    }

    public function isCartConfigurePdp()
    {
        $moduleName = $this->request->getModuleName();// checkout
        $controller = $this->request->getControllerName();// cart
        $action     = $this->request->getActionName();// configure
        $route      = $this->request->getRouteName();// checkout
        if (($moduleName == 'checkout') && ($controller == 'cart') &&
        ($action == 'configure') && ($route == 'checkout')) {
            return true;
        } else {
            return false;
        }
    }
}
