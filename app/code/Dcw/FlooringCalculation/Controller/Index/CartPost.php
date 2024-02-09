<?php
namespace Dcw\FlooringCalculation\Controller\Index;

use \Magento\Framework\Controller\ResultFactory;

class CartPost extends \Magento\Framework\App\Action\Action
{
    protected $formKey;
    protected $cart;
    protected $product;
	private $checkoutSession;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Checkout\Model\SessionFactory $checkoutSession
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->serializer = $serializer;
		$this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }
    public function execute()
    { 



        $resultJson = $this->_resultJsonFactory->create();

        $main_product_id = $this->getRequest()->getParam('main_product_id');
        $main_product = $this->_productFactory->create()->load($main_product_id);
        $selected_option_child_id = $this->getRequest()->getParam('selected_option_child_id');
        $selected_option_child = $this->_productFactory->create()->load($selected_option_child_id);
		
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cartpost.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
		if(!$customerSession->isLoggedIn() && ($main_product->getFreeTshirt() == 1 || $selected_option_child->getFreeTshirt() == 1)) {
		   $this->messageManager->addError(__('Free T-Shirt product not available to add to cart!'));
           $response = $resultJson->setData(['success' => 0]);
		   return $response;
		}
		
        $product_floor_type = $this->getRequest()->getParam('product_floor_type');
        $is_product_added = 0;
		// $logger->info($product_floor_type);
        if ($product_floor_type == 'Trailer Rolls') {
			if ($this->getRequest()->getParam('customrool_length') &&
				$this->getRequest()->getParam('customrool_length')> 0) {
                    try {
						
                    $main_product_id = $this->getRequest()->getParam('main_product_id');
                    $params = $this->getRequest()->getParams();
					
					$additionalOptions = [];
                    
                    $additionalOptions['product_option'] = [
                        'label' => 'Custom Roll Length',
                        'value' => $this->getRequest()->getParam('customrool_length')
                    ];
					$quote = $this->cart->getQuote();
					$items = $quote->getAllVisibleItems();
					
					
					if (count($items) > 0) {
						$itemId = "";
						$finalquoteqty = 0;
						$maxsaleqty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/item_options/max_sale_qty');
						foreach ($items as $item) {
							if ($simple_product = $item->getOptionByCode('simple_product')) {
									$simple_productvalue = $simple_product->getValue();
								if($simple_productvalue == $selected_option_child_id){
									$finalquoteqty = $this->getRequest()->getParam('qty')+ $item->getQty();
									if($finalquoteqty < $maxsaleqty){
										$itemId = $item->getItemId();
										$quote->removeItem($itemId)->save();
									}
								}
							}
						}
					}
					
						$this->cart->addProduct($main_product, $params);
						$this->cart->save();
						$is_product_added = 1;

                     } catch (\Exception $e) {
						$this->messageManager->addError($e->getMessage());
						return  $response = $resultJson->setData(['success' => 0,'message'=>$e->getMessage()]);
					}
				}
				if ($is_product_added == 1) {
                    $this->messageManager->addSuccess(__($main_product->getName().' Added Successfully.'));
                    $response = $resultJson->setData(['success' => 1]);
                } else {
                    $this->messageManager->addError(__('Cannot Add Product to cart.'));
                    $response = $resultJson->setData(['success' => 0]);
                }


                } else  if ($product_floor_type == 'Carpet Tiles' || $product_floor_type == 'Tire Price') {
        
                    try {
                    
                    $main_product_id = $this->getRequest()->getParam('main_product_id');
					$params = [];
					$params['product'] = $main_product->getId();
					$params['qty'] = $this->getRequest()->getParam('qty');
					$options = [];
					$additionalOptions = [];
					$productAttributeOptions = $main_product->getTypeInstance(true)->getConfigurableAttributesAsArray($main_product);

					foreach ($productAttributeOptions as $option) {
						$options[$option['attribute_id']] = $selected_option_child->getData($option['attribute_code']);
					}
					$params['super_attribute'] = $options;
					
                  
					$quote = $this->cart->getQuote();
				    $items = $quote->getAllVisibleItems();
				
					$maxsaleqty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/item_options/max_sale_qty');
					
					if (count($items) > 0) {
						$itemId = "";
						$finalquoteqty = 0;
						foreach ($items as $item) {
							if ($simple_product = $item->getOptionByCode('simple_product')) {
									$simple_productvalue = $simple_product->getValue();
								if($simple_productvalue == $selected_option_child_id){
									$finalquoteqty = $this->getRequest()->getParam('qty')+ $item->getQty();
									if($finalquoteqty < $maxsaleqty){
										$itemId = $item->getItemId();
										$quote->removeItem($itemId)->save();
									}
								}
							}
						}
					}
					$this->cart->addProduct($main_product, $params);
                   /* if (!$this->checkoutSession->create()->getQuote()->hasProductId($main_product_id)) {
						$this->cart->addProduct($main_product, $params);
				   } */
                    $this->cart->save();
                    $is_product_added = 1;

                    } catch (\Exception $e) {
						$this->messageManager->addError($e->getMessage());
						return  $response = $resultJson->setData(['success' => 0,'message'=>$e->getMessage()]);
					}
				if ($is_product_added == 1) {
                    $this->messageManager->addSuccess(__($main_product->getName().' Added Successfully.'));
                    $response = $resultJson->setData(['success' => 1]);
                } else {
                    $this->messageManager->addError(__('Cannot Add Product to cart.'));
                    $response = $resultJson->setData(['success' => 0]);
                }


                } else if ($product_floor_type == 'CBC Tiles') {
        /*=============== product_floor_type -> CBC Tiles START ======================*/
            try {
                /* ----------------- corner product ----------------------- */
                if ($this->getRequest()->getParam('corner_product_id') &&
                $this->getRequest()->getParam('corner_product_id')> 0) {
                    $additionalOptions = [];
                    $corner_product_id = $this->getRequest()->getParam('corner_product_id');
                    $params = [
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $corner_product_id,
                        'qty'  => $this->getRequest()->getParam('corner_tiles_qty'),
                    ];

                    $additionalOptions['flooring_color'] = [
                        'label' => 'Color',
                        'value' => $selected_option_child->getAttributeText('color')
                    ];

                    $additionalOptions['configurable_product_url'] = [
                        'label' => 'Configurable Product Url',
                        'value' => $main_product->getProductUrl()
                    ];
					
                    $corner_product = $this->_productFactory->create()->load($corner_product_id);
                    //$corner_product->setPrice(round($this->getRequest()->getParam('price_per_tile'), 2));
                    $corner_product->addCustomOption(
                        'additional_options',
                        $this->serializer->serialize($additionalOptions)
                    );
					
				   $quote = $this->cart->getQuote();
				   $items = $quote->getAllVisibleItems();
				
					
					if (count($items) > 0) {
						$itemId = "";
						$finalquoteqty = 0;
						$maxsaleqty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/item_options/max_sale_qty');
						foreach ($items as $item) {
							if ($simple_product = $item->getOptionByCode('simple_product')) {
									$simple_productvalue = $simple_product->getValue();
								if($simple_productvalue == $selected_option_child_id){
									$finalquoteqty = $this->getRequest()->getParam('qty')+ $item->getQty();
									if($finalquoteqty < $maxsaleqty){
										$itemId = $item->getItemId();
										$quote->removeItem($itemId)->save();
									}
								}
							}
						}
					}
					
						$this->cart->addProduct($corner_product, $params);
						$is_product_added = 1;
                }
                /* -------------------- corner product -------------------- */

                /* ------------------- border product -------------------- */
                if ($this->getRequest()->getParam('border_product_id') &&
                $this->getRequest()->getParam('border_tiles_qty')> 0) {
                    $additionalOptions = [];
                    $border_product_id = $this->getRequest()->getParam('border_product_id');
                    $params = [
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $border_product_id,
                        'qty'  => $this->getRequest()->getParam('border_tiles_qty')
                    ];

                    $additionalOptions['flooring_color'] = [
                        'label' => 'Color',
                        'value' => $selected_option_child->getAttributeText('color')
                    ];
					
                    $additionalOptions['configurable_product_url'] = [
                        'label' => 'Configurable Product Url',
                        'value' => $main_product->getProductUrl()
                    ];

                    $border_product = $this->_productFactory->create()->load($border_product_id);
                    //$border_product->setPrice(round($this->getRequest()->getParam('price_per_tile'), 2));
                    $border_product->addCustomOption(
                        'additional_options',
                        $this->serializer->serialize($additionalOptions)
                    );
					
				   $quote = $this->cart->getQuote();
				   $items = $quote->getAllVisibleItems();
				
					
					if (count($items) > 0) {
						$itemId = "";
						$finalquoteqty = 0;
						$maxsaleqty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/item_options/max_sale_qty');
						foreach ($items as $item) {
							if ($simple_product = $item->getOptionByCode('simple_product')) {
									$simple_productvalue = $simple_product->getValue();
								if($simple_productvalue == $selected_option_child_id){
									$finalquoteqty = $this->getRequest()->getParam('qty')+ $item->getQty();
									if($finalquoteqty < $maxsaleqty){
										$itemId = $item->getItemId();
										$quote->removeItem($itemId)->save();
									}
								}
							}
						}
					}
					
					$this->cart->addProduct($border_product, $params);
                    $is_product_added = 1;
                }
                /* ----------------------- border product ------------------------- */

                /* -------------------------- center product ------------------------- */
                if ($this->getRequest()->getParam('center_product_id') &&
                $this->getRequest()->getParam('center_tiles_qty')> 0) {
                    $additionalOptions = [];
                    $center_product_id = $this->getRequest()->getParam('center_product_id');
                    $params = [
                        'form_key' => $this->formKey->getFormKey(),
                        'product' => $center_product_id,
                        'qty'  => $this->getRequest()->getParam('center_tiles_qty')
                    ];

                    $additionalOptions['flooring_color'] = [
                        'label' => 'Color',
                        'value' => $selected_option_child->getAttributeText('color')
                    ];
					
					$additionalOptions['configurable_product_url'] = [
                        'label' => 'Configurable Product Url',
                        'value' => $main_product->getProductUrl()
                    ];
					
                    $center_product = $this->_productFactory->create()->load($center_product_id);
                    //$center_product->setPrice(round($this->getRequest()->getParam('price_per_tile'), 2));
                    $center_product->addCustomOption(
                        'additional_options',
                        $this->serializer->serialize($additionalOptions)
                    );
					
				   $quote = $this->cart->getQuote();
				   $items = $quote->getAllVisibleItems();
				
					
					if (count($items) > 0) {
						$itemId = "";
						$finalquoteqty = 0;
						$maxsaleqty = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('cataloginventory/item_options/max_sale_qty');
						foreach ($items as $item) {
							if ($simple_product = $item->getOptionByCode('simple_product')) {
									$simple_productvalue = $simple_product->getValue();
								if($simple_productvalue == $selected_option_child_id){
									$finalquoteqty = $this->getRequest()->getParam('qty')+ $item->getQty();
									if($finalquoteqty < $maxsaleqty){
										$itemId = $item->getItemId();
										$quote->removeItem($itemId)->save();
									}
								}
							}
						}
					}
					
					$this->cart->addProduct($center_product, $params);
					$is_product_added = 1;
                }
                /* --------------------------- center product --------------------------- */
                $this->cart->save();
                //$this->cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
                //$this->cart->getQuote()->save();
                if ($is_product_added == 1) {
                    $this->messageManager->addSuccess(__($main_product->getName().' Added Successfully.'));
                    $response = $resultJson->setData(['success' => 1]);
                } else {
                    $this->messageManager->addError(__('Cannot add product to cart.'));
                    $response = $resultJson->setData(['success' => 0]);
                }
                
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $response = $resultJson->setData(['success' => 0,'message'=>$e->getMessage()]);
            }
        /*================ product_floor_type -> CBC Tiles END ======================*/
        } else {
            $this->messageManager->addError(__('Cannot add product to cart.'));
            $response = $resultJson->setData(['success' => 0]);
        }
        return $response;
    }
}
