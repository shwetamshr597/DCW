<?php

            namespace Dcw\ProductImage\Plugin;


            use Magento\Catalog\Api\ProductRepositoryInterface;
            use Magento\Catalog\Model\Product;
            use Magento\Quote\Model\Quote\Item;
            use \Magento\Catalog\Helper\Image;

            class DefaultItem
            {

                protected $productRepo;
                protected $imageHelper;
				protected $dcwimagehelper;
				protected $listProductBlock;
                public function __construct(ProductRepositoryInterface $productRepository,\Magento\Catalog\Helper\Image $imageHelper, \Dcw\ProductImage\Helper\Data $dcwimagehelper, \Magento\Catalog\Block\Product\ListProduct $listProductBlock)
                {
                    $this->productRepo = $productRepository;
                    $this->imageHelper = $imageHelper;
					$this->dcwimagehelper = $dcwimagehelper;
					$this->listProductBlock = $listProductBlock;
                }
                public function aroundGetItemData($subject, \Closure $proceed, Item $item)
                {
                    $data = $proceed($item);
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $productType = $item->getProductType();
                    if($productType=='configurable'){
                        $data['simple_product_id'] = $item->getProduct()->getCustomOption('simple_product')->getProductId();
                        $productId = $data['simple_product_id'];
                    } else {
                        $productId = $item->getProduct()->getId();
                    }
                    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    $image = $this->dcwimagehelper->getMainImageUrl($product);

                    try {
                        if ($image!="") {
                            $imagesdimensionmedium = $this->dcwimagehelper->getSmallImageDimension();
                            $image = $image.'/-B'.$imagesdimensionmedium;
                            $data['product_image']['src'] = $image;
                        } else {
                            $imageDcwHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Catalog\Helper\Image::class);
                            $placeHolderImage = $imageDcwHelper->getDefaultPlaceholderUrl('thumbnail');
                            $data['product_image']['src'] = $placeHolderImage;
                        }
                    } catch (\Exception $e) {
                    }
                    
                    return $data; 
                }
            }