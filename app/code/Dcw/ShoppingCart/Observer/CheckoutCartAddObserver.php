<?php
namespace Dcw\ShoppingCart\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Checkout\Model\Cart;
class CheckoutCartAddObserver implements ObserverInterface
{
    protected $request;
    private $serializer;
	protected $_productloader;
	protected $dataHelper;
	protected $_cart;

    public function __construct(
        RequestInterface $request,
        SerializerInterface $serializer,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Dcw\FlooringCalculation\Helper\Data $dataHelper,
		Cart $cart
    )
    {
        $this->request = $request;
        $this->serializer = $serializer;
		$this->_productloader = $_productloader;
		$this->dataHelper = $dataHelper;
		$this->_cart = $cart;
    }

    public function execute(EventObserver $observer)
    {
        // $item = $observer->getQuoteItem();
		$item = $observer->getEvent()->getData('quote_item');
        $post = $this->request->getPost();
		$prodata = [];
		
		
		
		// $logger->info(json_encode($post));
        
        if($item->getProductType() == "configurable"){
			
			$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/additionalopts.log');
			$logger = new \Zend_Log();
			$logger->addWriter($writer);
			$additionalOptions = array();
			
			if ($additionalOption = $item->getOptionByCode('additional_options')) {
				$additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
			}
			
			
			 
			 if(isset($post['customrool_length']) && $post['customrool_length'] > 0){
				 
				$additionalOptions['customrool_length'] = [
						'label' => 'Custom Roll Length',
						'value' => $post['customrool_length']
					]; 
				 
			 }
			 if(isset($post['selected_option_child_id']) && $post['selected_option_child_id']!=''){
				$prodata = $this->_productloader->create()->load($post['selected_option_child_id']);
				
			 } elseif(isset($post['product']) && $post['product']!=''){
				$prodata = $this->_productloader->create()->load($post['product']);
				
			 }
			 
			 
			 if(!empty($prodata) && $prodata->getData('weight')!=""){
				 $qtyweight = intval($prodata->getData('weight'))*$item->getQty();
				 
				 $additionalOptions['weight'] = [
						'label' => 'Weight',
						'value' => $qtyweight.' lbs'
					];
			 }
			 
			if($post['ship_to_text']!=""){
				
				$additionalOptions['ship_to_text'] = [
							'label' => '',
							'value' => $post['ship_to_text']
						];
			}

			if (count($additionalOptions) > 0) {
				$item->addOption(array(
					'product_id' => $item->getProductId(),
					'code' => 'additional_options',
					'value' => $this->serializer->serialize($additionalOptions)
				));
			}
			
		}
			$pdplinedata = [];
				if(isset($post['pricePerSqft'])){
					$pdplinedata['PricePerSqft']= $post['pricePerSqft'] ?? 0;
				}
				if(isset($post['qty'])){
					
					$pdplinedata['LineItemUnitQuantity']= $post['qty'] ?? 0;
				}
				if(isset($post['custom_price'])){
					$pdplinedata['UnitPrice']= $post['custom_price'] ?? 0;
				}
				if(isset($post['custom_price'])){
					$pdplinedata['LineItemPrice']= ((float)$post['custom_price']*(int)$post['qty']) ?? 0;
				}
				if(isset($post['tileSize'])){
					$pdplinedata['TileSize']= $post['tileSize'] ?? 0;
				}
				
				if(isset($post['covers'])){
					$pdplinedata['Covers']= $post['covers'] ?? 0;
				}
				
				if(isset($post['squareFootage'])){
					$pdplinedata['SquareFootage']= $post['squareFootage'] ?? 0;
				}
				
				if(isset($post['length'])){
					$pdplinedata['Length']= $post['length'] ?? 0;
				}
				if(isset($post['Width'])){
					$pdplinedata['Width']= $post['width'] ?? 0;
				}
				if(isset($post['PricePerLinearFoot'])){
					$pdplinedata['PricePerLinearFoot']= $post['PricePerLinearFoot'] ?? 0;
				}
				if(isset($post['RoomWidth'])){
					$pdplinedata['RoomWidth']= $post['RoomWidth'] ?? 0;
				}
				if(isset($post['RoomLength'])){
					$pdplinedata['RoomLength']= $post['RoomLength'] ?? 0;
				}
				
				if(isset($post['RollType'])){
					$pdplinedata['RollType']= $post['RollType'] ?? 0;
				}
				
				if(isset($post['QuantityRange'])){
					$pdplinedata['QuantityRange']= $post['QuantityRange'] ?? 0;
				}
				
				if(isset($post['RollSize'])){
					$pdplinedata['RollSize']= $post['RollSize'] ?? 0;
				}
				
				if(isset($post['TrailerRollType'])){
					$pdplinedata['TrailerRollType']= $post['TrailerRollType'] ?? 0;
				}
				
				if(isset($post['CustomLength'])){
					$pdplinedata['CustomLength']= $post['CustomLength'] ?? 0;
				}
				if(isset($post['CBCShopBy'])){
					$pdplinedata['CBCShopBy']= $post['CBCShopBy'] ?? 0;
				}
				
				if(isset($post['CBCTileSize'])){
					$pdplinedata['CBCTileSize']= $post['CBCTileSize'] ?? 0;
				}
				if(isset($post['CBCShopBy'])){
					$pdplinedata['CBCShopBy']= $post['CBCShopBy'] ?? 0;
				}
				if(isset($post['border_tiles_qty'])){
					$pdplinedata['BorderTileQty']= $post['border_tiles_qty'] ?? 0;
				}
				if(isset($post['center_tiles_qty'])){
					$pdplinedata['CenterTileQty']= $post['center_tiles_qty'] ?? 0;
				}
				if(isset($post['corner_tiles_qty'])){
					$pdplinedata['CornerTileQty']= $post['corner_tiles_qty'] ?? 0;
				}
				if(!empty($pdplinedata)){
					$pdplinedata_json = json_encode($pdplinedata);	
					$item->setData('pdp_line_item', $pdplinedata_json);
				}
			if(isset($post['qty']) && $post['qty']!=""){
				$item->setData('qty', $post['qty']);
				$this->_cart->save();
			}
			
    }
}