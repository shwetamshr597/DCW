<?php
namespace Dcw\ShoppingCart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class UpdateCartItems implements ObserverInterface
{
    
	private $serializer;
	protected $_productloader;
	protected $dataHelper;

    public function __construct(
        SerializerInterface $serializer,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Dcw\FlooringCalculation\Helper\Data $dataHelper
    )
    {
		$this->serializer = $serializer;
		$this->_productloader = $_productloader;
		$this->dataHelper = $dataHelper;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $items = $observer->getCart()->getQuote()->getItems();
		
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/updates.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
				
        foreach ($items as $item) {
				$prodata = [];
				$qtyweight = "";
				if ($item->getProduct()) {
						
						if ($additionalOption = $item->getOptionByCode('additional_options')) {
								$additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
						}
					if (!empty($additionalOptions)) {
							foreach($additionalOptions as $key=>$option){
								if($key == "ship_to_text"){
									$additionalOptions['ship_to_text'] = [
										'label' => '',
										'value' => $option['value']
									];
								} else if($key == "weight" && $item->getWeight()!=""){
									$qtyweight = intval($item->getWeight())*$item->getQty();
									$additionalOptions['weight'] = [
										'label' => 'Weight',
										'value' => $qtyweight.' lbs'
									];
									
								} else if($key == "customrool_length"){
									$additionalOptions['customrool_length'] = [
										'label' => 'Custom Roll Length',
										'value' => $option['value']
									];
								}
							}
					$item->addOption([
								'code' => 'additional_options',
								'value' => $this->serializer->serialize($additionalOptions),
								'product_id' => $item->getProduct()->getId()
							]);

				}
			}   
		}   

	}
}
