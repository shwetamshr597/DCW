<?php

namespace Magecomp\Instagrampro\Block\Catalog\Product\View;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Helper\Product;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Detail extends Template
{
    protected $_helperData;
    protected $_helperImage;
    protected $_helperProduct;
    protected $_objectManager;
    protected $_collection = [];

    public function __construct(
        Context $context,
        HelperData $helperData,
        Product $helperProduct,
        Image $helperImage,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_helperData = $helperData;
        $this->_helperImage = $helperImage;
        $this->_helperProduct = $helperProduct;
        parent::__construct($context, $data);
    }

    public function showInstagramproImages()
    {
        $helper = $this->_helperData;
        return ($helper->isEnabled() && $helper->showImagesOnProductPage() && count($this->getInstagramproGalleryImages()) > 0);
    }

    public function getInstagramproGalleryImages()
    {
        $product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');

        if (!$this->_collection) {
            $this->_collection = $this->_helperProduct->getInstagramproGalleryImages($product);
        }
        return $this->_collection;
    }

    public function Isshowindetailsection()
    {
        return $this->_helperData->getImageOnProductsection();
    }

    public function showProductInPopup()
    {
        return $this->_helperImage->getPopupConfiguration();
    }
    public function getProductPopupUrl()
    {
        return $this->getUrl('instagrampro/gallery/productpopuphtml');
    }
    public function getPopupUrl()
    {
        return $this->getUrl('instagrampro/gallery/popuphtml');
    }
    public function getPopupChangeProductUrl()
    {
        return $this->getUrl('instagrampro/gallery/popupchangeprod');
    }
}
