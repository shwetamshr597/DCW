<?php
namespace Magecomp\Instagramprographql\Model\Resolver;
 
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\Exception\AuthenticationException;


class GetConfigData implements ResolverInterface
{
    public function __construct( 
        \Magecomp\Instagrampro\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;
    }
    
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null)
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
                $data = ["status" => true, "response" => json_encode($responseData)];
            } else {
                $data = ["status" => false, "response" => __("Please Enable The Extension")];
            }
            return $data;
        }
        catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        } 
    }


}