<?php
namespace Dcw\IncstoreShipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;

class OrderPlacedAfterObserver implements ObserverInterface
{
   
	protected $_orderItems;
	protected $_curl;
	protected $scopeConfig;
   
    public function __construct(
		\Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItems,
		\Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		Curl $curl
    ) {
		$this->_orderItems = $orderItems;
		$this->_stockItemRepository = $stockItemRepository;
		$this->resource = $resource;
		$this->connection = $resource->getConnection();
		$this->addressCollection = $addressCollection;
		$this->_curl = $curl;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
	    $order = $observer->getEvent()->getOrder();
	    $orderId = $order->getId();
		$shipaddress = $this->getShippingAddress($order);
		$apidata = $this->getItemApi($shipaddress->getPostcode(), $order);
		if (count($apidata) > 0) {
			foreach ($apidata as $data){

                //:::::::starting of order item level data store::::::::

                $shipping_metadata = [];
                $shipping_metadata['shippingQuoteRequestId']= $data['requestId'];
                $shipping_metadata['shippingQuoteId']= $data['quoteId'];
                $shipping_metadata['shippingQuoteCharge']= $data['charge'];
                $shipping_metadata['shippingQuoteCost']= $data['cost'];
                $shipping_metadata['shippingQuoteCarrier']= $data['carrier'];
                $shipping_metadata['shippingQuoteShipFromManufacturerId']= $data['shipFromLocation']['manufacturerId'];
                $shipping_metadata['shippingQuoteShipFromLocationId']= $data['shipFromLocation']['locationId'];
                $shipping_metadata['shippingQuoteShipFromManufacturerName']= $data['shipFromLocation']['manufacturerName'];
                $shipping_metadata['shippingQuoteShipFromStreetAddress']= $data['shipFromLocation']['address']['streetAddress'];
                $shipping_metadata['shippingQuoteShipFromCity']= $data['shipFromLocation']['address']['city'];
                $shipping_metadata['shippingQuoteShipFromState']= $data['shipFromLocation']['address']['state'];
                $shipping_metadata['shippingQuoteShipFromZipCode']= $data['shipFromLocation']['address']['zipCode'];
                $shipping_metadata['shippingQuoteShipFromCountry']= $data['shipFromLocation']['address']['country'];
                
                $shipping_metadata_json = json_encode($shipping_metadata);
                $itemid = $data['lineItem']['skuNumber'];
                $this->updateItemShippingMetadata($shipping_metadata_json,$itemid,$order);

                //:::::::End of order item level data store:::::::::::::
				
				$itemid = $data['lineItem']['skuNumber'];
				//$logger->info("itemid-". $itemid);
				$charge = $data['charge'];
				//$logger->info("charge-". $charge);
					 if($charge!=""){
						 $orderItemObj = $this->_orderItems->create()->addFieldToFilter('item_id', $itemid)->getFirstItem();
						 $orderItemObj->setIncstoreItemShipping($charge);
						 $orderItemObj->save();
					 }
			}
		}
        
    }  
//:::::::starting of order item level data store v2::::::::

    public function updateItemShippingMetadata($data,$itemid,$order){
        foreach ($order->getAllItems() as $item) {
            $item_level_id = $item->getId();
            if($item_level_id==$itemid){
                $item->setShippingMetadata($data);
                $item->save();
            }
        }
    }
//:::::::End of order item level data store v2:::::::::::::

	public function getItemApi($postcode, $order)
    {
        
		$params = ['shipToZipCode' => $postcode, 'DeliveryOptions' => new \ArrayObject()];
        $items = $order->getAllItems();
        foreach ($items as $item) {
			$itemid = $item->getItemId();
			$orderItemObj = $this->_orderItems->create()->addFieldToFilter('item_id', $itemid)->getFirstItem();
			
            if($item->getWeight()) {
				$params['lineItems'][] = ['Id' => $item->getProductId(), 'SkuNumber' => $item->getId(), 'TotalWeight' => $item->getWeight() * $item->getQtyOrdered()];
			
            }
        }
        $apiurl = 'https://xj5ir7jfutraetvdgw6gchxege0wfwfm.lambda-url.us-east-1.on.aws/';
        $apikey = $this->scopeConfig->getValue('dcw_shipping_api/incstoreshipping_config/api_key');
        $apiResponse = $this->apiCaller($apiurl, json_encode($params), $apikey);
		//$logger->info('RES! '. print_r($apiResponse, true));
        if (count($apiResponse) > 0) {
            return $apiResponse;
        }
        return false;
    }
	
	public function getApiPrice($apiResponse, $itemId){
		
		if (count($apiResponse) > 0) {
            foreach ($apiResponse as $res) {
				$charge = 0;
				if($res['lineItem']['id'] == $itemId){
					return $res['charge'];
				} else {
					return $charge;
				}
            }
            return $charge;
        }
        return false;
		
		
	}
	
    /* get Shipping address data of specific order */
    public function getShippingAddress($order) {
       //  $order = $this->getOrderData($orderId);
        /* check order is not virtual */
        if(!$order->getIsVirtual()) {
            $orderShippingId = $order->getShippingAddressId();
            $address = $this->addressCollection->create()->addFieldToFilter('entity_id',array($orderShippingId))->getFirstItem();
            return $address;
        }
        return null;
    }
	
	public function apiCaller($url, $params, $apikey)
    {
        $headers = ["Content-Type" => "application/json", "x-api-key" => $apikey];
        $this->_curl->setHeaders($headers);
        $this->_curl->post($url, $params);
        $response = (array)json_decode($this->_curl->getBody(), true);
        if (array_key_exists('errors', $response)) {
            // see errors in shipping.log file
            return [];
        }
        return $response;
    }
}
