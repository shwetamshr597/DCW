<?php
namespace Dcw\SampleProduct\Block;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{   
    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        $itemId = parent::getItem()->getItemId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $url="";
        //This return something
        $items = $cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if($itemId == $item->getItemId())
            {
                $customOptions = $item->getOptionByCode('additional_options');
                if (isset($customOptions)) {
                    $row = $customOptions->getData();
                    $custom_options = json_decode($row['value'],1);   
                    if(array_key_exists("configurable_product_url",$custom_options)){
                        $url=$custom_options['configurable_product_url']['value'];
                    } else {
                        if ($this->_productUrl !== null) {
                            return $this->_productUrl;
                        }
                        if ($this->getItem()->getRedirectUrl()) {
                            return $this->getItem()->getRedirectUrl();
                        }
                        $product = $this->getProduct();
                        $option = $this->getItem()->getOptionByCode('product_type');
                        if ($option) {
                            $product = $option->getProduct();
                        }    
                        $url= $product->getUrlModel()->getUrl($product);
                    }           
                }  else {
                    if ($this->_productUrl !== null) {
                        return $this->_productUrl;
                    }
                    if ($this->getItem()->getRedirectUrl()) {
                        return $this->getItem()->getRedirectUrl();
                    }
            
                    $product = $this->getProduct();
                    $option = $this->getItem()->getOptionByCode('product_type');
                    if ($option) {
                        $product = $option->getProduct();
                    }

                    $url= $product->getUrlModel()->getUrl($product);;
            
                }                 
            }  
        }  
       
        return $url;
    }  
    public function getProductName()
    {
        $itemId = parent::getItem()->getItemId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $name="";
        //This return something
        $items = $cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            if($itemId == $item->getItemId())
            {
                $customOptions = $item->getOptionByCode('additional_options');
                if (isset($customOptions)) {
                    $row = $customOptions->getData();
                    $custom_options = json_decode($row['value'],1);   
                    if(array_key_exists("configurable_product_name",$custom_options)){
                        $name=$custom_options['configurable_product_name']['value'];
                    } else {
                        $name=$item->getName();
                    }    
                }  else {
                    $name=$item->getName();
                }                 
            }  
        }  

        return $name;
    }       
}