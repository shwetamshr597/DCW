<?php
namespace Dcw\FlooringCalculation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Model\Currency;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Image;

class Quickship extends Template
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $scopeConfig;
    
    /**
     * @var ProductFactory
     */
    protected $_productloader;
    
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
    protected $_currency;
    
    /**
     * @var Registry
     */
    protected $_registry;
    
    /**
     * @var Image
     */
    protected $_imagehelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectManagerInterface $objectManager
     * @param ProductFactory $_productloader
     * @param FormKey $formKey
     * @param CheckoutSession $checkoutSession
     * @param Currency $currency
     * @param Registry $registry
     * @param Image $imagehelper
     */
        
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Swatches\Helper\Data $swatchHelper,
        ObjectManagerInterface $objectManager,
        ProductFactory $_productloader,
        FormKey $formKey,
        CheckoutSession $checkoutSession,
        Currency $currency,
        Registry $registry,
        Image $imagehelper,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Swatches\Helper\Media $swatchHelperMedia,
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->objectManager = $objectManager;
        $this->_productloader = $_productloader;
        $this->formKey = $formKey;
        $this->checkoutSession = $checkoutSession;
        $this->_currency = $currency;
        $this->_registry = $registry;
        $this->_imagehelper = $imagehelper;
        $this->swatchHelper = $swatchHelper;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->swatchHelperMedia = $swatchHelperMedia;
        parent::__construct($context);
    }

    /* Get Hashcode of Visual swatch by option id */
    public function getAtributeSwatchHashcode($optionid) {
        $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionid]);
        return $hashcodeData[$optionid]['value'];
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
     *
     * @param integer $id for load product
     * @return \Magento\Catalog\Model\ProductFactory
     */
     
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    
    /**
     *  Get Product
     *
     * @param string $sku for load product
     * @return \Magento\Catalog\Model\ProductFactory
     */
    
    public function getProductBySku($sku)
    {
        return $this->_productloader->create()->loadByAttribute('sku', $sku);
    }
    
    /**
     *
     * @param integer $id for its quickship product
     * @return array
     */
    
    public function getQuickShipProduct($id)
    {
        $quickshipids[]=array();
        $allowedQuickShipColorIds = array();
        $quickShipOptionsArr = array();
        $product=$this->getLoadProduct($id);
        if ($product) {
            if ($product->getQuickShipForAssociatedProduct()) {
                $quickshipids[]=$product->getQuickShipForAssociatedProduct();
            }
            if ($product->getTypeId()=='configurable') {
                $_children=$product->getTypeInstance()->getUsedProducts($product);
                foreach ($_children as $child) {
                    $cproduct=$this->getLoadProduct($child->getId());
                    if ($cproduct) {
                        if ($cproduct->getQuickShipForAssociatedProduct() && $cproduct->getColor()) {
                            $allowedQuickShipColorIds[] = $cproduct->getColor();
                        }
                        
                    }
        
                }
            }

            /*$colors = $this->productAttributeRepository->get('color')->getOptions();
            foreach ($colors as $color) {
                if (in_array($color->getValue(),$allowedQuickShipColorIds)) {
                    $swatchCollection = $this->objectManager->create(\Magento\Swatches\Model\ResourceModel\Swatch\Collection::class);
                    $swatchCollection->addFieldtoFilter('option_id', $color->getValue());
                    $item = $swatchCollection->getFirstItem();
                    $quickShipOptionsArr[$color->getValue()] = $item->getData();
                }
            }*/
        } 
        return $allowedQuickShipColorIds;
    }
     
    
    /**
     * Send Media url
     *
     * @return string media url
     */
    public function getMediaDirectoryUrl()
    {
            
        $media_dir = $this->objectManager->get(Magento\Store\Model\StoreManagerInterface::class)
        ->getStore()
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
