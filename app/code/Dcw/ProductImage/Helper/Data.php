<?php

namespace Dcw\ProductImage\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;


class Data extends AbstractHelper
{
    
	protected $scopeConfig;
	public function __construct(
		Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    ) 
    {
	    $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
	   
	   public function getMainImageUrl($product)
       {
               $mainimage = "";
			  /* if($product->getProductImage1()!=""){
				  $mainimage =  $product->getProductImage1();
			   } else if($product->getProductImage2()!=""){
				  $mainimage =  $product->getProductImage2(); 
			   } else if($product->getProductImage3()!=""){
				  $mainimage =  $product->getProductImage3(); 
			   } else if($product->getProductImage4()!=""){
				  $mainimage =  $product->getProductImage4(); 
			   } else if($product->getProductImage5()!=""){
				  $mainimage =  $product->getProductImage5(); 
			   } else if($product->getProductImage6()!=""){
				  $mainimage =  $product->getProductImage6(); 
			   } else if($product->getProductImage7()!=""){
				  $mainimage =  $product->getProductImage7(); 
			   } else if($product->getProductImage8()!=""){
				  $mainimage =  $product->getProductImage8(); 
			   } else if($product->getProductImage9()!=""){
				  $mainimage =  $product->getProductImage9(); 
			   } else if($product->getProductImage10()!=""){
				  $mainimage =  $product->getProductImage10(); 
			   }
			  // $imagebaseurl = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_baseurl');
			   if($mainimage!=""){
				$mainimage = $mainimage;   
				   
			   }*/
			   $images = [];
					$images = $product->getMediaGalleryImages();
					$canurl ="";
					foreach($images as $img){
						if($img['custom_image_link']!=""){
							$canurl = $img['custom_image_link'];
							break;
						}
					}
			   return $canurl;
       }
	   
	   public function getGalleryImage($product)
       {
		   $galleryImages =array();
		  // $imagebaseurl = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_baseurl');
			 /* if($product->getProductImage1()!=""){
				  $galleryImages[] =  $product->getProductImage1();
			   } if($product->getProductImage2()!=""){
				  $galleryImages[]=  $product->getProductImage2(); 
			   } if($product->getProductImage3()!=""){
				  $galleryImages[]=  $product->getProductImage3(); 
			   } if($product->getProductImage4()!=""){
				  $galleryImages[]=  $product->getProductImage4(); 
			   } if($product->getProductImage5()!=""){
				  $galleryImages[]=  $product->getProductImage5(); 
			   } if($product->getProductImage6()!=""){
				  $galleryImages[]= $product->getProductImage6(); 
			   } if($product->getProductImage7()!=""){
				  $galleryImages[]=  $product->getProductImage7(); 
			   } if($product->getProductImage8()!=""){
				  $galleryImages[]= $product->getProductImage8(); 
			   } if($product->getProductImage9()!=""){
				  $galleryImages[]= $product->getProductImage9(); 
			   } if($product->getProductImage10()!=""){
				  $galleryImages[]= $product->getProductImage10(); 
			   }
			   
			   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			  */
			  $images = $product->getMediaGalleryImages();
			  foreach($images as $img){
				  if($img['custom_image_link']!=""){
					  $galleryImages[] = $img['custom_image_link'];  
				  }
			  }
          return $galleryImages;
       }
	   
	   public function getConfigValue1($config_path){
      	return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
   	   }
	   
	  public function getMainImageDimension(){
		  $dimesions = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_original');
		   return $dimesions;  
	  }
	  
	  public function getBaseImageDimension(){
		$dimesions = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_base');
		 return $dimesions;
		  
	  }
	  
	  public function getSmallImageDimension(){
		  $dimesions = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_small');
			 return $dimesions;  
	  }
	  
	  public function getThumbnailImageDimension(){ 
		  $dimesions = $this->getConfigValue1('dcw_productimage/product_image_config/default_product_image_thumbnail');
			 return $dimesions;  
	  }
}