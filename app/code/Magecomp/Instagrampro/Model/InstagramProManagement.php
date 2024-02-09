<?php
namespace Magecomp\Instagrampro\Model;


class InstagramProManagement implements \Magecomp\Instagrampro\Api\InstagramProManagementInterface
{

  public function __construct(
    \Magecomp\Instagrampro\Helper\Data $helperData,
    \Magecomp\Instagrampro\Block\Homepage $homepage,
    \Magento\Catalog\Model\ProductRepository $productRepository,
    \Magecomp\Instagrampro\Helper\Product $helperProduct
  )
  {
      $this->helperData = $helperData;
      $this->homepage = $homepage;
      $this->productRepository = $productRepository;
      $this->helperProduct = $helperProduct;
  }

  public function getConfigData()
  {
    try{
        if ($this->helperData->isEnabled()) {
               
            $responseData = [
                "Extension_Name" => "InstagramPro",
                "Enable" => $this->helperData->isEnabled(),
                "AccessToken" => $this->helperData->getAccessToken(),
                "ShowImageOnHomePage" => $this->helperData->showImagesOnHomePage(),
                "NoOfImageOnHomePage" => $this->helperData->getHomePageLimit(),
                "ShowImageOnProductPage" => $this->helperData->showImagesOnProductPage(),
                "NoOfImageOnProductPage" => $this->helperData->getProductPageLimit(),
                "ShowInMoreViewSection" => $this->helperData->getImageOnProductMoreViewsection(),
                "ShowInProductDetailSection" => $this->helperData->getImageOnProductsection(),
                "UpdateImageBy" => $this->helperData->getUpdateBy(),
                "Hashtags" => $this->helperData->getHashtag(),
                "NoOfImageOnInstagramPage" => $this->helperData->getGalleryPageLimit(),
                "TitleOfInstagramPage" => $this->helperData->getProductDetailTitle(),
                'IsShuffling' => $this->helperData->isShuffling(),
                "FrontendLinkOfInstagramPage" => $this->helperData->getFrontendLink(),
                "ShowInPopup" => $this->helperData->shownpInPopup()
            ];
            $data = ["status" => true, "data" => $responseData];
        } else {
            $data = ["status" => false, "response" => __("Please Enable The Extension")];
        }
        return json_encode($data);

    } catch (\Exception $e) {
        $data = ["status" => false, "response" => __($e->getMessage())];
        return json_encode($data);
    }
  }

  public function getImageData($page){
    try{
        if($page  == NULL){
            $data = ["status" => false, "response" => __("Required parameter is missing")];
            return json_encode($data);
        }
        
        if ($this->helperData->isEnabled()) { 
            if($page == "Instagram"){
                if(count($this->homepage->getInstagrampropageGalleryImages(true))){
                    $data = ["status" => true, "data" => $this->homepage->getInstagrampropageGalleryImages(true)];          
                }
                else{
                    $data = ["status" => false, "response" => __("No approved images found")];
                }
            }      
            else if($page == "CMS"){
                if(count($this->homepage->getInstagramproGalleryImages())){
                    $data = ["status" => true, "data" => $this->homepage->getInstagramproGalleryImages()];          
                }
                else{
                    $data = ["status" => false, "response" => __("No approved images found")];
                }
            }
            else{
                $data = ["status" => false, "response" => __("Value must be either Instagram or CMS")];
            }
        } else {
            $data = ["status" => false, "response" => __("Please Enable The Extension")];
        }
        return json_encode($data);

    } catch (\Exception $e) {
        $data = ["status" => false, "response" => __($e->getMessage())];
        return json_encode($data);
    }
  }

  public function getProductPageImageData($productId){
    try{
        if($productId  == NULL){
            $data = ["status" => false, "response" => __("Required parameter is missing")];
            return json_encode($data);
        }
        
        if ($this->helperData->isEnabled()) {    
            $product = $this->productRepository->getById($productId);
            if($product){
                if(count($this->helperProduct->getInstagramproGalleryImages($product,true))){
                    $data = ["status" => true, "data" => $this->helperProduct->getInstagramproGalleryImages($product,true)];          
                }
                else{
                    $data = ["status" => false, "response" => __("No approved images found")];
                }  
            }
        } else {
            $data = ["status" => false, "response" => __("Please Enable The Extension")];
        }
        return json_encode($data);

    } catch (\Exception $e) {
        $data = ["status" => false, "response" => __($e->getMessage())];
        return json_encode($data);
    }
  }

}

