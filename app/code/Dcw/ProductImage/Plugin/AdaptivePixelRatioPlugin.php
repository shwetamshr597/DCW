<?php
/**
 * Fastly CDN for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Fastly CDN for Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_FASTLY_CDN.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fastly CDN to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fastly
 * @package     Fastly_Cdn
 * @copyright   Copyright (c) 2016 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */
namespace Dcw\ProductImage\Plugin;

use Fastly\Cdn\Model\Config;
use Magento\Catalog\Block\Product\Image;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class AdaptivePixelRatioPlugin for image ration
 *
 */
class AdaptivePixelRatioPlugin extends \Magento\Catalog\Block\Product\Image
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    protected $imagehelper;

    /**
     * AdaptivePixelRatioPlugin constructor.
     *
     * @param Config $config
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Config $config,
        ProductMetadataInterface $productMetadata,
        \Dcw\ProductImage\Helper\Data $imagehelper
    ) {
        $this->config = $config;
        $this->productMetadata = $productMetadata;
        $this->imagehelper = $imagehelper;
    }

    /**
     * Adjust srcset if required
     *
     * @param Image $subject
     */
    public function beforeToHtml(Image $subject)
    {
        if ($this->config->isImageOptimizationPixelRatioEnabled() !== true) {
            return;
        }

        $srcSet = [];
        $pId = $subject->getData('product_id');
        $objectmanager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectmanager->create('Magento\Catalog\Model\Product')->load($pId);
        $image =  $this->imagehelper->getMainImageUrl($product);
        $imagesdimension = $this->imagehelper->getSmallImageDimension(); //->getBaseImageDimension()
        if($image!=""){
            $imageUrl = $image.'/-B'.trim($imagesdimension);
        } else {
            $imageUrl = $subject->getData('image_url');
        }
        // echo  $imageUrl; die();
        // echo "<pre>"; print_r($subject->getData('product')); echo "</pre>"; die();
       // echo $image =  $this->imagehelper->getMainImageUrl($subject->getData('product')); die();
        /* if($image!=""){
            $imageUrl = $image;
        } else {
            $imageUrl = $subject->getData('image_url');
        } */

        // $imageUrl = $subject->getData('image_url');
        
        $pixelRatiosArray = $this->config->getImageOptimizationRatios();
        $glue = "";
        if(!empty($imageUrl)){
            $glue = (strpos($imageUrl ?? '', '?') !== false) ? '&' : '?';
        }

        # Pixel ratios defaults are based on the table from https://mydevice.io/devices/
        # Bulk of devices are 2x however many new devices like Samsung S8, iPhone X etc are 3x and 4x
        foreach ($pixelRatiosArray as $pr) {
            $ratio = 'dpr=' . $pr . ' ' . $pr . 'x';
            if($glue!=""){
                $srcSet[] = $imageUrl . $glue . $ratio;
            }
        }

        $srcSet = implode(',', $srcSet);
        $customAttributes = $subject->getCustomAttributes();
        if (version_compare((string)$this->productMetadata->getVersion(), '2.4', '<')) {
            $customAttributes = !empty($customAttributes) ? [$customAttributes] : [];
            $customAttributes[] = 'srcset="' . $srcSet . '"';
            $subject->setData('custom_attributes', implode(' ', $customAttributes));
        } else {
            $customAttributes = $customAttributes ?: [];
            $customAttributes['srcset'] = $srcSet;
            $subject->setData('custom_attributes', $customAttributes);
        }
    }
}
