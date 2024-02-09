<?php
namespace Dcw\BrandLogo\Block;

/**
 *  BrandLogo Block
 */

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    
    protected $_brandlogoCollectionFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_productloader = $_productloader;
        $this->categoryRepository = $categoryRepository;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    
    public function getBrandLogo($product_id)
    {
        $product = $this->getProductById($product_id);
        $categoryIds = $product->getCategoryIds();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $brand_logo_img_html = '';
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId);
    
            if ($category->getIsBrand() == 1 && !empty($category->getCatThumbnail())) {
                $brand_logo = str_replace('//media', '/media', $baseUrl.$category->getCatThumbnail());
                $brand_logo_img_html = '<img src="'.$brand_logo.'" 
				alt="'.$category->getName().'" title="'.$category->getName().'"/>';
                break;
            }
        }
        return $brand_logo_img_html;
    }

    public function getProductById($product_id)
    {
            $_product = $this->_productloader->create()->load($product_id);
            return $_product;
    }
}
