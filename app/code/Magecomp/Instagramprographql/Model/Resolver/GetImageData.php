<?php
namespace Magecomp\Instagramprographql\Model\Resolver;
 
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\Exception\AuthenticationException;


class GetImageData implements ResolverInterface
{
    public function __construct( 
        \Magecomp\Instagrampro\Helper\Data $helperData,
        \Magecomp\Instagrampro\Block\Homepage $homepage
    )
    {
        $this->helperData = $helperData;
        $this->homepage = $homepage;
    }
    
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null)
    {
        try{   
            if($args['page']  == NULL){
                $data = ["status" => false, "response" => __("Required parameter is missing")];
                return json_encode($data);
            }
            
            if ($this->helperData->isEnabled()) { 
                if($args['page'] == "Instagram"){
                    if(count($this->homepage->getInstagrampropageGalleryImages(true))){
                        $data = ["status" => true, "response" => json_encode($this->homepage->getInstagrampropageGalleryImages(true))];          
                    }
                    else{
                        $data = ["status" => false, "response" => __("No approved images found")];
                    }
                }      
                else if($args['page'] == "CMS"){
                    if(count($this->homepage->getInstagramproGalleryImages())){
                        $data = ["status" => true, "response" => json_encode($this->homepage->getInstagramproGalleryImages())];          
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
            return $data;
        }
        catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()));
        } 
    }


}