<?php
namespace Dcw\FlooringCalculation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CustomPrice implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product_floor_type = $this->request->getParam('product_floor_type');
        /*============== product_floor_type -> CBC Tiles START ======================*/
        if (!empty($product_floor_type) && $product_floor_type == 'CBC Tiles') {
            $price_per_tile = $this->request->getParam('price_per_tile');
            $price_per_tile = round($price_per_tile, 2);
            $item = $observer->getEvent()->getData('quote_item');
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $price = $price_per_tile; //set your price here
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
        /*============== product_floor_type -> CBC Tiles STOP ======================*/

        /*============== product_floor_type -> CBC Tiles START ======================*/
        elseif (!empty($product_floor_type) && $product_floor_type == 'Trailer Rolls') {
            $trailer_rolls_price = $this->request->getParam('trailer_rolls_price');
            $trailer_rolls_price = round($trailer_rolls_price, 2);
            $item = $observer->getEvent()->getData('quote_item');
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $price = $trailer_rolls_price; //set your price here
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        } 
        /*============== product_floor_type -> CBC Tiles STOP ======================*/
        else
        {
			$custom_price = $this->request->getParam('custom_price');
			$qty = $this->request->getParam('qty');
			/*============== PDP Custom Price ======================*/
			if ($custom_price!="") {
				$item = $observer->getEvent()->getData('quote_item');
				$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
				$price = $custom_price; //set your price here
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);
			}
        }
    }
}
