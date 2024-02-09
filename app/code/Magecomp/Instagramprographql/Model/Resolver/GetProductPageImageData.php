<?php
namespace Magecomp\Instagramprographql\Model\Resolver;
 
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\Exception\AuthenticationException;


class GetProductPageImageData implements ResolverInterface
{
    public function __construct( 
        \Magecomp\Instagrampro\Helper\Data $helperData,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magecomp\Instagrampro\Helper\Product $helperProduct
    )
    {
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        $this->helperProduct = $helperProduct;
    }
    
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null)
    {
        try{   
            if($args['productId']  == NULL){
                $data = ["status" => false, "response" => __("Required parameter is missing")];
                return $data;
            }
            
            if ($this->helperData->isEnabled()) {    
                $product = $this->productRepository->getById($args['productId']);
                if($product){
                    if(count($this->helperProduct->getInstagramproGalleryImages($product,true))){
                        $data = ["status" => true, "response" => json_encode($this->helperProduct->getInstagramproGalleryImages($product,true))];          
                    }
                    else{
                        $data = ["status" => false, "response" => __("No approved images found")];
                    }  
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