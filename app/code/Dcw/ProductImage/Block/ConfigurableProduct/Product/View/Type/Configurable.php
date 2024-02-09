<?php
namespace Dcw\ProductImage\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

class Configurable
{
protected $jsonEncoder;
protected $jsonDecoder;
protected $imagehelper;

public function __construct(
EncoderInterface $jsonEncoder,
DecoderInterface $jsonDecoder,
\Dcw\ProductImage\Helper\Data $imagehelper
) {
$this->jsonDecoder = $jsonDecoder;
$this->jsonEncoder = $jsonEncoder;
$this->imagehelper = $imagehelper;
}

public function afterGetJsonConfig(
\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
$config
) {
$config = $this->jsonDecoder->decode($config);
				$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/bulk_data.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                

if(isset($config['images'])){
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$imageHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Catalog\Helper\Image::class);
	$imagesdimensionsmall = $this->imagehelper->getSmallImageDimension();
	$imagesdimensionmedium = $this->imagehelper->getThumbnailImageDimension();
	$imagesdimensionlarge = $this->imagehelper->getBaseImageDimension();
	$result = $imageHelper->getDefaultPlaceholderUrl('thumbnail');
	foreach ($config['images'] as $key=>$value){
		$product = $objectManager->create('Magento\Catalog\Model\Product')->load($key);
		$image = $this->imagehelper->getMainImageUrl($product);
		$small      = $image.'/-B'.$imagesdimensionsmall;
		$medium     = $image.'/-B'.$imagesdimensionlarge;
		$large      = $image.'/-B'.$imagesdimensionlarge;
		if($image!=""){
			foreach($value as $newval){
				$config['images'][$key][0]['thumb'] = $small;
				$config['images'][$key][0]['img'] = $medium;
				$config['images'][$key][0]['full']= $large;
				$config['images'][$key][0]['caption']= '';
				$config['images'][$key][0]['position']= 1;
				$config['images'][$key][0]['isMain']= 1;
				$config['images'][$key][0]['type']= 1;
				$config['images'][$key][0]['videoUrl']= '';
			}
		} else {
			foreach($value as $newval){
				$config['images'][$key][0]['thumb'] = $result;
				$config['images'][$key][0]['img'] = $result;
				$config['images'][$key][0]['full']= $result;
				$config['images'][$key][0]['caption']= '';
				$config['images'][$key][0]['position']= 1;
				$config['images'][$key][0]['isMain']= 1;
				$config['images'][$key][0]['type']= 1;
				$config['images'][$key][0]['videoUrl']= '';
			}
		}
		
	}
	
	
}
/* foreach ($subject->getAllowProducts() as $product) {
            $config['images'][$product->getId()]['thumb'] = 'https://d2q5qaygbbwnnw.cloudfront.net/image/230444301506/image_soo4ppss9l0gp79kilosv28a13/-B800';
            $config['images'][$product->getId()]['img'] = 'https://d2q5qaygbbwnnw.cloudfront.net/image/230444301506/image_soo4ppss9l0gp79kilosv28a13/-B800';
            $config['images'][$product->getId()]['full']= 'https://d2q5qaygbbwnnw.cloudfront.net/image/230444301506/image_soo4ppss9l0gp79kilosv28a13/-B800';
			$config['images'][$product->getId()] ['caption']= '';
			$config['images'][$product->getId()] ['position']= 1;
			$config['images'][$product->getId()] ['isMain']= 'https://d2q5qaygbbwnnw.cloudfront.net/image/230444301506/image_soo4ppss9l0gp79kilosv28a13/-B800';
			$config['images'][$product->getId()] ['type']= 1;
			$config['images'][$product->getId()] ['videoUrl']= '';
} */
//$logger->info(print_r($config, true));

// your custom logic here
return $this->jsonEncoder->encode($config);
}
}