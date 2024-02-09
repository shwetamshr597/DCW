<?php
namespace Dcw\FlooringCalculation\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir $moduleDir,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Dcw\FlooringCalculation\Helper\Data $helperData,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResourceModel,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->moduleDir = $moduleDir;
        $this->swatchHelper = $swatchHelper;
        $this->objectManager = $objectManager;
        $this->helperData = $helperData;
        $this->attributeResourceModel = $attributeResourceModel;
        $this->_productloader = $_productloader;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }
    
    public function flooringCalculationHelper()
    {
        return $this->helperData;
    }

    public function attributeResourceModel()
    {
        return $this->attributeResourceModel;
    }

    public function getCbcTiles($product_id)
    {
        $main_product = $this->_productloader->create()->load($product_id);

        $_products = $this->productCollectionFactory->create();
        $_products->addAttributeToSelect('*')
       // ->addAttributeToFilter('name', ['like' => $main_product->getName().'%'])
        ->addAttributeToFilter('sku', array('in' => array($main_product->getName().'_corner', $main_product->getName().'_border', $main_product->getName().'_center')))
        ->addAttributeToFilter('status', 1);
          
        $cbcItemsArr = [];
        foreach ($_products as $_product) {
            $cbcItemArr = [];
            $cbc_type = str_replace([$main_product->getName(),' ','-'], '', $_product->getName());
            $cbc_type = strtolower($cbc_type);
            $cbcItemArr['cbc_type'] = $cbc_type;
            $cbcItemArr['cbc_product_id'] = $_product->getId();
            $cbcItemArr['cbc_product_name'] = $_product->getName();
            $cbcItemArr['cbc_product_price'] = $_product->getPrice();
            $cbcItemsArr[] = $cbcItemArr;
        }
        return $cbcItemsArr;
    }
}
