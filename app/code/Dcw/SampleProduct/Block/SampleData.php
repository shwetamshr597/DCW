<?php
/**
 * Copyright © DCW, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author DCW
 *
 */

namespace Dcw\SampleProduct\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Model\Currency;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;

class SampleData extends Template
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $scopeConfig;
    
    /**
     * @var ProductFactory
     */
    protected $productloader;
    
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    
    /**
     * @var FormKey
     */
    protected $formKey;
    
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    
    /**
     * @var Currency
     */
    protected $currency;
    
    /**
     * @var Registry
     */
    protected $registry;
    
    /**
     * @var Image
     */
    protected $imagehelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectManagerInterface $objectManager
     * @param ProductFactory $productloader
     * @param FormKey $formKey
     * @param CheckoutSession $checkoutSession
     * @param Currency $currency
     * @param Registry $registry
     * @param Image $imagehelper
     * @param StoreManagerInterface $storeManager
     */
        
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Swatches\Helper\Data $swatchHelper,
        ObjectManagerInterface $objectManager,
        ProductFactory $productloader,
        FormKey $formKey,
        CheckoutSession $checkoutSession,
        Currency $currency,
        Registry $registry,
        Image $imagehelper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->objectManager = $objectManager;
        $this->productloader = $productloader;
        $this->formKey = $formKey;
        $this->checkoutSession = $checkoutSession;
        $this->_currency = $currency;
        $this->_registry = $registry;
        $this->_imagehelper = $imagehelper;
        $this->swatchHelper = $swatchHelper;
        $this->storeManager = $storeManager;
        $this->_json = $json;
        parent::__construct($context);
    }
    
    /**
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Get Hashcode of Visual swatch by option id
     *
     * @param $optionid
     *
     * @return array
     */
    public function getAtributeSwatchHashcode($optionid)
    {
        if ($optionid!="") {
            $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionid]);
            return $hashcodeData[$optionid]['value'];
        }
    }
    
    /**
     * Get Quate product ids
     *
     * @return array
     */
    public function getQuoteProductIds()
    {
        $productids=[];
        $allItems =$this->checkoutSession->getQuote()->getAllItems();
        foreach ($allItems as $item) {
            $productids[] = $item->getProductId();
        }
        return $productids;
    }

    /**
     * Get quote product name
     *
     * @return array
     */
    public function getQuoteProductIdsName()
    {
        $productids=[];
        $optionValue="";
        $allItems =$this->checkoutSession->getQuote()->getAllItems();
        foreach ($allItems as $item) {
            $options = $item->getOptionByCode('additional_options');
            if (isset($options) && !$options=== null) {
                $option_val = $this->_json->unserialize($options->getValue());
                if (isset($option_val) && !$option_val!="") {
                    foreach ($option_val as $op) {
                        if ($op['label'] == "Product Name") {
                            $optionValue = $op['value'];
                        }
                    }
                }
            }
    
            $pid = $item->getProductId();
            $productids[$pid] = $optionValue;
        }
        return $productids;
    }

    public function getQuoteProductIdsColor()
    {
        $productids=[];
        $optionValue="";
        $allItems =$this->checkoutSession->getQuote()->getAllItems();
        foreach ($allItems as $item) {
            $additionalOptions = $item->getOptionByCode('additional_options');
            
            if (!empty($additionalOptions)) {
                $option_val = $this->_json->unserialize($additionalOptions->getValue());
                if (isset($option_val) && $option_val!="") {
                    foreach ($option_val as $op) {
                        if ($op['label'] == "Color") {
                            $optionValue = $op['value'];
                            break;
                        }
                    }
                }
            }
            
            $pid = $item->getProductId();
            //$productids[$pid] = $optionValue;
            $productids[] = $pid.'~'.$optionValue;
        }
        return $productids;
    }

    public function getSampleProductColor($item)
    {
        $optionValue="";
       
        $additionalOptions = $item->getOptionByCode('additional_options');
        
        if (!empty($additionalOptions)) {
            $option_val = $this->_json->unserialize($additionalOptions->getValue());
            if (isset($option_val) && $option_val!="") {
                foreach ($option_val as $op) {
                    if ($op['label'] == "Color") {
                        $optionValue = $op['value'];
                        break;
                    }
                }
            }
        }
        
        return $optionValue;
    }

    public function getCartItemName($item)
    {
        $optionValue= $item->getName();
        $additionalOptions = $item->getOptionByCode('additional_options');
        
        if (!empty($additionalOptions) && $this->isSampleCartItem($item)) {
            $option_val = $this->_json->unserialize($additionalOptions->getValue());
            if (isset($option_val) && $option_val!="") {
                foreach ($option_val as $op) {
                    if ($op['label'] == "Product Name") {
                        $optionValue = $op['value'];
                        break;
                    }
                }
            }
        }
        
        return $optionValue;
    }

    public function isSampleCartItem($item)
    {
        $is_sampleCartItem = 0;
       
        $additionalOptions = $item->getOptionByCode('additional_options');
        
        if (!empty($additionalOptions)) {
            $option_val = $this->_json->unserialize($additionalOptions->getValue());
            if (!empty($option_val['sample_color'])) {
                $is_sampleCartItem = 1;
            }
        }
        
        return $is_sampleCartItem;
    }
    
    /**
     * Get Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    
    /**
     * Get currency symbol for current locale and currency code
     *
     * @param integer $id for load product
     *
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }
    
    /**
     *  Get Product
     *
     * @param string $sku for load product
     *
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function getProductBySku($sku)
    {
        return $this->productloader->create()->loadByAttribute('sku', $sku);
    }
    
    /**
     * Get currency symbol for current locale and currency code
     *
     * @param integer $id for its sample product
     *
     * @return array
     */
    public function getsampleproductsku($id)
    {
        $samplesku=[];
        $product=$this->getLoadProduct($id);
        if ($product) {
            if ($product->getSampleProductSku()) {
                $samplesku[]=$product->getSampleProductSku();
            }
            if ($product->getTypeId()=='configurable') {
                $_children=$product->getTypeInstance()->getUsedProducts($product);
                foreach ($_children as $child) {
                    $cproduct=$this->getLoadProduct($child->getId());
                    if ($cproduct) {
                        if ($cproduct->getSampleProductSku()) {
                            $samplesku[]=$cproduct->getSampleProductSku();
                        }
                    }
           
                }
            }
            
        }
        return $samplesku;
    }
    
    /**
     * Send Media url
     *
     * @return string media url
     */
    public function getMediaDirectoryUrl()
    {
        $media_dir = $this->storeManager->getStore()
                   ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
    
    /**
     * Get product image url
     *
     * @param \Magento\Catalog\Model\ProductFactory $_product
     * @param string $type for image type
     * @return string
     */
    
    public function getProductImageUrl($_product, $type)
    {
        return $this->_imagehelper->init($_product, $type)->getUrl();
    }
    
    /**
     * Get currency product id
     *
     * @return string
     */
    
    public function getCurrentProduct()
    {
        $product=$this->_registry->registry('current_product');
        if ($product) {
            return $product->getId();
        }
        return false;
    }
}
