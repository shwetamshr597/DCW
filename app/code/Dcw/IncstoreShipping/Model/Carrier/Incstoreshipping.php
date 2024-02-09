<?php
/**
 * @author Nishant Kumar<nishantk@dotcomweavers.com>
 */

declare(strict_types=1);

namespace Dcw\IncstoreShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\HTTP\Client\Curl;

class Incstoreshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var code
     */

    protected $_code = 'incstoreshipping';

    /**
     * @var RateResultFactory
     */

    protected $_rateResultFactory;

    /**
     * @var RateMethodFactory
     */

    protected $_rateMethodFactory;

    /**
     * @var Curl
     */
    
    protected $_curl;

    /**
     * @var Cart
     */

    protected $_cart;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Checkout\Model\Cart $cart,
        Curl $curl,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_cart = $cart;
        $this->_curl = $curl;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active') || empty($request->getDestPostcode())) {
            return false;
        }
        
        $shippingPrice = $this->getApiPrice($request->getDestPostcode());

        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * getApiPrice
     * @param $postcode
     * @return mixed
     */
    public function getApiPrice($postcode)
    {
        $params = ['shipToZipCode' => $postcode, 'DeliveryOptions' => new \ArrayObject()];
        $items = $this->_cart->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if($item->getWeight()) {
                $params['lineItems'][] = ['Id' => $item->getProductId(), 'SkuNumber' => $item->getId(), 'TotalWeight' => $item->getWeight() * $item->getQty()];
            }
        }
        //print_r($params); die;
        $apiurl = 'https://xj5ir7jfutraetvdgw6gchxege0wfwfm.lambda-url.us-east-1.on.aws/';
        $apikey = $this->scopeConfig->getValue('dcw_shipping_api/incstoreshipping_config/api_key');
        $apiResponse = $this->apiCaller($apiurl, json_encode($params), $apikey);
		$lineitem = $this->getItemApiPrice($postcode, 34);
		$this->_logger->info(print_r($lineitem, true));
        if (count($apiResponse) > 0) {
            $charge = 0;
            foreach ($apiResponse as $res) {
                $charge+=$res['charge'];
            }
            return $charge;
        }
        return false;
    }
	
	
	/**
     * getItemApiPrice
     * @param $postcode
	 * @param $itemId
     * @return mixed
     */
    public function getItemApiPrice($postcode, $itemId)
    {
        $params = ['shipToZipCode' => $postcode, 'DeliveryOptions' => new \ArrayObject()];
        $items = $this->_cart->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if($item->getWeight()) {
                $params['lineItems'][] = ['Id' => $item->getProductId(), 'SkuNumber' => $item->getId(), 'TotalWeight' => $item->getWeight() * $item->getQty()];
            }
        }
        //print_r($params); die;
        $apiurl = 'https://xj5ir7jfutraetvdgw6gchxege0wfwfm.lambda-url.us-east-1.on.aws/';
        $apikey = $this->scopeConfig->getValue('dcw_shipping_api/incstoreshipping_config/api_key');
        $apiResponse = $this->apiCaller($apiurl, json_encode($params), $apikey);
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


    /**
     * apiCaller
     * @param $url
     * @param $params
     * @param $apikey
     * @return array
     */
    public function apiCaller($url, $params, $apikey)
    {
        $headers = ["Content-Type" => "application/json", "x-api-key" => $apikey];
        $this->_curl->setHeaders($headers);
        $this->_curl->post($url, $params);
        $this->_logger->info('Request: '. $params);
        $response = (array)json_decode($this->_curl->getBody(), true);
        $this->_logger->info('Response ', $response);
        if (array_key_exists('errors', $response)) {
            // see errors in shipping.log file
            $this->_logger->info(json_encode($response['errors']));
            return [];
        }
        return $response;
    }
}
