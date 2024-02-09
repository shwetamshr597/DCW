<?php

namespace Magecomp\Instagrampro\Block\Catalog\Product\View;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Helper\Product;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayUtils;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
    protected $jsonEncoder;

    protected $_helperData;
    protected $_helperProduct;
    protected $_objectManager;
    protected $_collection = [];

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        HelperData $helperData,
        Product $helperProduct,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->_helperData = $helperData;
        $this->_helperProduct = $helperProduct;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $arrayUtils, $jsonEncoder);
    }

    public function aftergetGalleryImagesJson($subject, $result)
    {
        if ($this->Isshowinmoreviewsection()) {
            $result = json_decode($result);
            $position = count($result);
            foreach ($this->getInstagramproGalleryImages() as $_image):
                $position++;
                $result[] = [
                    'thumb' => $_image->getThumbnailUrl(),
                    'img' => $_image->getThumbnailUrl(),
                    'full' => $_image->getThumbnailUrl(),
                    'caption' => $_image->getImageTitle(),
                    'position' => $position,
                    'isMain' => '',
                ];
            endforeach;
            $result = json_encode($result);

        }
        return $result;
    }

    public function Isshowinmoreviewsection()
    {
        return true;
        $helper = $this->_helperData;
        return ($helper->isEnabled() && $helper->showImagesOnProductPage() && $helper->getImageOnProductMoreViewsection() && count($this->getInstagramproGalleryImages()) > 0);
    }

    public function getInstagramproGalleryImages()
    {
        $product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');

        if (!$this->_collection) {
            $this->_collection = $this->_helperProduct->getInstagramproGalleryImages($product);
        }
        return $this->_collection;
    }
}
