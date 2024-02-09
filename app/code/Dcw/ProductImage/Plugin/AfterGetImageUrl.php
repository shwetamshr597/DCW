<?php

namespace Dcw\ProductImage\Plugin;

use Magento\Catalog\Block\Product\Image;
use Magento\Framework\View\DesignInterface;

class AfterGetImageUrl
{
     protected $imagehelper;
     protected $design;
	 protected $image;
	 protected $_productFactory;
	/**
     * AfterGetImage constructor.
     */
     public function __construct(
        \Dcw\ProductImage\Helper\Data $imagehelper,
        DesignInterface $design,
		\Magento\Catalog\Helper\Image $image,
		\Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->imagehelper = $imagehelper;
        $this->design = $design;
		$this->image = $image;
		$this->_productFactory = $productFactory;
    }

    /**
     * @param Image $image
     * @param $method
     * @return array|null
     */
    public function after__call(Image $image, $result, $method)
    {
        try {
            if ($method == 'getImageUrl' && $image->getProductId() > 0) {
				$theme = $this->design->getDesignTheme();
                $themeName = $theme->getCode();
				if($themeName == "Dcw/hyvamobile"){
					$imagesdimension = $this->imagehelper->getThumbnailImageDimension();
				} else {
					$imagesdimension = $this->imagehelper->getSmallImageDimension();
				}
                
				if(!empty($image)){
					
					$product = $this->_productFactory->create()->load($image->getProductId());
					 $imagenew = $this->imagehelper->getMainImageUrl($product);
					if($imagenew!=""){
						 $result = $imagenew.'/-B'.$imagesdimension;
					} else {
						$result = $this->image->getDefaultPlaceholderUrl('thumbnail');
					}
					
					
				}
                
            }
        } catch (\Exception $e) {
        }
        return $result;
    }

}