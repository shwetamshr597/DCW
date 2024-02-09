<?php
namespace Dcw\DesignerTool\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        //\Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        //\Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir $moduleDir,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Dcw\DesignerTool\Helper\Data $helperData,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        //$this->productRepository = $productRepository;
        $this->_productloader = $_productloader;
        //$this->productRepositoryInterface = $productRepositoryInterface;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->moduleDir = $moduleDir;
        $this->swatchHelper = $swatchHelper;
        $this->objectManager = $objectManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->imageHelper = $imageHelper;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }
    
    public function getProductById($product_id)
    {
            $_product = $this->_productloader->create()->load($product_id);
            return $_product;
    }
        
    public function getConfigValue($store_config_field)
    {
        return $this->scopeConfig->getValue(
            $store_config_field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
    }
        
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    public function moduleViewPath()
    {
        return $this->moduleDir->getDir('Dcw_DesignerTool', \Magento\Framework\Module\Dir::MODULE_VIEW_DIR);
    }

    public function designerToolHelper()
    {
        return $this->helperData;
    }

    public function imageHelper()
    {
        return $this->imageHelper;
    }

    public function getCookieValue($cName)
    {
        return $this->cookieManager->getCookie($cName);
    }

    public function getDefaultPatternTile($product_id, $selectedChildId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $product = $this->getProductById($product_id);
        $selectedChildId = $this->getProductById($selectedChildId);
        $_children = $product->getTypeInstance()->getUsedProducts($product);
        $defaultPatternTileArr = [];
        foreach ($_children as $_child_product) {
            if ($_child_product->getdefaultDesignTile() == 1 &&
            (($_child_product->getExactWidth() == $selectedChildId->getExactWidth()) &&
            ($_child_product->getExactLength() == $selectedChildId->getExactLength()))) {
                $swatchCollection = $this->objectManager
                ->create(\Magento\Swatches\Model\ResourceModel\Swatch\Collection::class);
                if (!empty($_child_product->getColor())) {
                    $optionIdvalue = $_child_product->getColor();
                    $swatchCollection->addStoreFilter($storeId)->addFieldtoFilter('option_id', $optionIdvalue);
                    $item = $swatchCollection->getFirstItem();
                    $defaultPatternTileArr = $item->getData();
                    if (!empty($item->getData('value'))) {
                        $defaultPatternTileArr['swatch_image'] = $item->getData('value');
                    } else {
                        $defaultPatternTileArr['swatch_image']
                        = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                    }
                    $defaultPatternTileArr['color_title']
                    = $_child_product->getAttributeText('color');
                    $defaultPatternTileArr['color_type'] = 'swatch_color_url';
                    $defaultPatternTileArr['product_id']
                    = $_child_product->getId();
                    $defaultPatternTileArr['product_final_price']
                    = $_child_product->getFinalPrice();
                    $defaultPatternTileArr['product_regular_price']
                    = $_child_product->getPrice();

                    break;
                }
            }
        }
        return $defaultPatternTileArr;
    }
        
    public function getColorItems($product_id, $selectedChildId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $product = $this->getProductById($product_id);
        $selectedChildId = $this->getProductById($selectedChildId);
        $_children = $product->getTypeInstance()->getUsedProducts($product);
        $colorItemOptionsArr = [];
        foreach ($_children as $_child_product) {
            if (($_child_product->getExactWidth() == $selectedChildId->getExactWidth()) &&
            ($_child_product->getExactLength() == $selectedChildId->getExactLength())) {
                $swatchCollection = $this->objectManager
                ->create(\Magento\Swatches\Model\ResourceModel\Swatch\Collection::class);
                //$_child_product->getResource()->getAttribute('color')->getFrontend()->getValue($_child_product);
                if (!empty($_child_product->getColor())) {
                    $optionIdvalue = $_child_product->getColor();
                    $swatchCollection->addStoreFilter($storeId)->addFieldtoFilter('option_id', $optionIdvalue);
                    $item = $swatchCollection->getFirstItem();
                    $colorItemOptionsArr[$_child_product->getAttributeText('color')] = $item->getData();
                    if (!empty($item->getData('value'))) {
                        $colorItemOptionsArr[$_child_product->getAttributeText('color')]['swatch_image'] = $item->getData('value');
                    } else {
                        $colorItemOptionsArr[$_child_product->getAttributeText('color')]['swatch_image']
                        = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                    }
                   
                    $colorItemOptionsArr[$_child_product->getAttributeText('color')]['color_title']
                    = $_child_product->getAttributeText('color');
                    $colorItemOptionsArr[$_child_product->getAttributeText('color')]['product_id']
                    = $_child_product->getId();
                    $colorItemOptionsArr[$_child_product->getAttributeText('color')]['product_final_price']
                    = $_child_product->getFinalPrice();
                    $colorItemOptionsArr[$_child_product->getAttributeText('color')]['product_regular_price']
                    = $_child_product->getPrice();
                    //echo'<br>'. $this->swatchHelper->getSwatchAttributeImage('swatch_thumb', $item->getValue());
                    //echo'<br>'. $this->swatchHelper->getSwatchAttributeImage('swatch_image', $item->getValue());
                }
            }

        }
            
        return $colorItemOptionsArr;
    }
    
    public function getEdgingItems()
    {
        $storeId = $this->storeManager->getStore()->getId();
        
        $_products = $this->productCollectionFactory->create();
        $_products->addAttributeToSelect('*')
                    ->addAttributeToFilter('display_edging', 1)
                    ->addAttributeToFilter('status', 1);
          
        $edgingItemOptionsArr = [];
        $edgingFemaleItemOptionsArr = [];
        $edgingMaleItemOptionsArr = [];
        foreach ($_products as $_product) {
            $swatchCollection = $this->objectManager
            ->create(\Magento\Swatches\Model\ResourceModel\Swatch\Collection::class);
            if (!empty($_product->getColor())) {
                $optionIdvalue = $_product->getColor();
                $swatchCollection->addStoreFilter($storeId)->addFieldtoFilter('option_id', $optionIdvalue);
                $item = $swatchCollection->getFirstItem();
                
                if (strpos(strtolower($_product->getName()), 'female') !== false) {
                    $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')] = $item->getData();
                    if (!empty($item->getData('value'))) {
                        $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['swatch_image'] = $item->getData('value');
                    } else {
                        $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['swatch_image']
                        = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                    }
                    $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['color_title']
                    = $_product->getAttributeText('color');
                    $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['female_product_id']
                    = $_product->getId();
                    $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['female_product_final_price']
                    = $_product->getFinalPrice();
                    $edgingFemaleItemOptionsArr[$_product->getAttributeText('color')]['female_product_regular_price']
                    = $_product->getPrice();
                }
                
                if (!empty($_product->getColor()) && (strpos(strtolower($_product->getName()), 'male') !== false &&
                strpos(strtolower($_product->getName()), 'female') === false)) {
                    $edgingMaleItemOptionsArr[$_product->getAttributeText('color')] = $item->getData();
                    if (!empty($item->getData('value'))) {
                        $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['swatch_image'] = $item->getData('value');
                    } else {
                        $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['swatch_image']
                        = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                    }
                    $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['color_title']
                    = $_product->getAttributeText('color');
                    $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['male_product_id']
                    = $_product->getId();
                    $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['male_product_final_price']
                    = $_product->getFinalPrice();
                    $edgingMaleItemOptionsArr[$_product->getAttributeText('color')]['male_product_regular_price']
                    = $_product->getPrice();
                }
                //$edgingItemOptionsArr[$item->getOptionId()]['product_name'] = strtolower($_product->getName());
            }
        }
        
        $edgingItemOptionsArr = [];
        foreach ($edgingFemaleItemOptionsArr as $color_key => $edgingFemaleItemOptions) {
            foreach ($edgingFemaleItemOptions as $option => $edgingFemaleItemOption) {
                $edgingItemOptionsArr[$color_key][$option] = $edgingFemaleItemOption;
            }
        }
        foreach ($edgingMaleItemOptionsArr as $color_key => $edgingMaleItemOptions) {
            foreach ($edgingMaleItemOptions as $option => $edgingMaleItemOption) {
                $edgingItemOptionsArr[$color_key][$option] = $edgingMaleItemOption;
            }
        }
        
        return $edgingItemOptionsArr;
    }
    
    public function getCornerItems()
    {
        $storeId = $this->storeManager->getStore()->getId();
        
        $_products = $this->productCollectionFactory->create();
        $_products->addAttributeToSelect('*')
                    ->addAttributeToFilter('display_corner', 1)
                    ->addAttributeToFilter('status', 1);
          
        $cornerItemOptionsArr = [];
        foreach ($_products as $_product) {
            $swatchCollection = $this->objectManager
            ->create(\Magento\Swatches\Model\ResourceModel\Swatch\Collection::class);
            if (!empty($_product->getColor())) {
                $optionIdvalue = $_product->getColor();
                $swatchCollection->addStoreFilter($storeId)->addFieldtoFilter('option_id', $optionIdvalue);
                $item = $swatchCollection->getFirstItem();
                
                $cornerItemOptionsArr[$_product->getAttributeText('color')] = $item->getData();
                if (!empty($item->getData('value'))) {
                    $cornerItemOptionsArr[$_product->getAttributeText('color')]['swatch_image'] = $item->getData('value');
                } else {
                    $cornerItemOptionsArr[$_product->getAttributeText('color')]['swatch_image']
                    = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                }
                $cornerItemOptionsArr[$_product->getAttributeText('color')]['color_title']
                = $_product->getAttributeText('color');
                $cornerItemOptionsArr[$_product->getAttributeText('color')]['product_id'] = $_product->getId();
                $cornerItemOptionsArr[$_product->getAttributeText('color')]['product_final_price']
                = $_product->getFinalPrice();
                $cornerItemOptionsArr[$_product->getAttributeText('color')]['product_regular_price']
                = $_product->getPrice();
            }
        }
        return $cornerItemOptionsArr;
    }
}
