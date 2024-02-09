<?php

namespace Magecomp\Instagrampro\Controller\Gallery;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product as ProductLoader;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Popupchangeprod extends Action
{
    protected $_modelInstagramproimageFactory;
    protected $_productloader;
    protected $productimghelper;

    public function __construct(
        Context $context,
        Product $helperProduct,
        ProductLoader $productloader,
        Image $producthelper
    ) {
        $this->_productloader = $productloader;
        $this->productimghelper = $producthelper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $get = $this->getRequest()->getParams();

            $product = $this->_productloader->load($get['id']);
            $imgUrl = $this->productimghelper->init($product, 'category_page_list')->constrainOnly(false)->keepAspectRatio(false)->keepFrame(false)->resize(220, 220)->getUrl();
            $data = [
                'prodname' => $product->getName(),
                'produrl' => $product->getProductUrl(),
                'prodimg' => $imgUrl,
                'proddesc' => ($product->getShortDescription()) ? htmlentities(substr($product->getShortDescription(), 0, 150)) : ""
            ];

            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
