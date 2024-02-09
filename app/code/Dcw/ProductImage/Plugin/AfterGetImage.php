<?php

namespace Dcw\ProductImage\Plugin;

use Magento\Catalog\Block\Product\AbstractProduct;

class AfterGetImage
{
    
	protected $imagehelper;
	/**
     * AfterGetImage constructor.
     */
     public function __construct(
        \Dcw\ProductImage\Helper\Data $imagehelper
    ) {
        $this->imagehelper = $imagehelper;
    }

    /**
     * @param AbstractProduct $subject
     * @param $result
     * @param $product
     * @param $imageId
     * @param $attributes
     * @return mixed
     */
    public function afterGetImage(AbstractProduct $subject, $result, $product, $imageId, $attributes) {
		$productId = $result->getProductId(); 
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $image = $this->getExternalImage($product);
		$imagesdimension = $this->imagehelper->getSmallImageDimension();
		try {
            if ($product) {
                        $image = array();
                        $image['image_url'] = $image.'/-B'.$imagesdimension;
                        $image['width'] = $imagesdimension;
                        // $image['height'] = $height;
                        $image['label'] = $product->getName();
                        $image['ratio'] = "1.25";
                        $image['custom_attributes'] = "";
                        $image['resized_image_width'] = "399";
                        $image['resized_image_height'] = "399";
                        $image['product_id'] = $product->getId();
                if ($image) {
                    $result->setData($image);
                }
            }
        } catch (\Exception $e) {
        }
        return $result;
    }

    public function getExternalImage($product) {
        return $image = $this->imagehelper->getMainImageUrl($product);
    }
}