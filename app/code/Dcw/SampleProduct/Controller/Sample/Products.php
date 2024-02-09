<?php
namespace Dcw\SampleProduct\Controller\Sample;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;

class Products extends Action
{
    /**
     * @var FormKey
     */
    protected $formKey;
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var ResultJsonFactory
     */
    private $resultJsonFactory;

    private $serializer;
    
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     * @param CheckoutSession $checkoutSession
     * @param ResultJsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FormKey $formKey,
        Cart $cart,
        Product $product,
        CheckoutSession $checkoutSession,
        ResultJsonFactory $resultJsonFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Dcw\SampleProduct\Block\SampleData $sampleBlock
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->_resultPageFactory = $resultPageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializer = $serializer;
        $this->_productRepository = $productRepository;
        $this->quoteRepository = $quoteRepository;
        $this->_json = $json;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->imageHelper = $imageHelper;
        $this->sampleBlock = $sampleBlock;
        parent::__construct($context);
    }

    /**
     * Sample Product add to cart
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postparms=$this->getRequest()->getParams();
        $is_sample_added = 0;
        if (isset($postparms['action_type']) && isset($postparms['pids'])) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Products.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('text message');
            $id_color = array();
            $sample_in_cart = array();
            $productIds=explode(',', $postparms['pids']);
            $spidsColors = array();
            if (!empty($postparms['spidsColors'])) {
                $spidsColors=explode(',', $postparms['spidsColors']);
            }
            $childIds=explode(',', $postparms['childproid']);
            $logger->info('tchildproid'.$postparms['childproid']);
            $logger->info(print_r($childIds,true));
            /*if($postparms['colors']!="") {
                $logger->info("Iffffff");
                $colors = explode(',', $postparms['colors']);             
                foreach($colors as $color)
                {
                    $color_arr = explode('~', $color);
                    $id_color[$color_arr[0]] = $color_arr[1];
                }
                $logger->info("id color ");
                $logger->info(print_r($id_color,true));
            } else {
                $logger->info("Elssssss");
                $this->messageManager->addError('This product is already available in your cart.');
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('This product is already in your cart.')
                );
            }*/
          
            $logger->info("action_type ".$postparms['action_type']);
            if ($postparms['action_type']==2) {
                $quoteItems = $this->checkoutSession->getQuote()->getItemsCollection();        
                foreach ($quoteItems as $item) {
                    foreach ($productIds as $productId) {
                        if ($item->getProductId()==$productId) {
                            $this->cart->removeItem($item->getId())->save();
                        }         
                    }
                }
                $this->messageManager->addSuccess(__('Sample Product Remove Successfully.'));
            } elseif ($postparms['action_type']==1) {
                $logger->info("else if");
                $quoteItems = $this->checkoutSession->getQuote()->getItemsCollection();
                $count_order_sample_in_cart = 0;
                $system_sample_order_count = $this->scopeConfig->getValue(
                    'dcw_order_sample/order_sample_config/default_max_sample_in_cart',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->storeManager->getStore()->getStoreId()
                );
                
                $system_sample_order_msg = $this->scopeConfig->getValue(
                    'dcw_order_sample/order_sample_config/default_max_sample_in_cart_msg',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->storeManager->getStore()->getStoreId()
                );

                foreach ($quoteItems as $item) {
                   
                    $additionalOptions = $item->getOptionByCode('additional_options');
                    if (!empty($additionalOptions)) {
                        $option_val = $this->_json->unserialize($additionalOptions->getValue());
                        if (isset($option_val) && $option_val!="") {
                            foreach ($option_val as $key => $op) {
                                if ($key == "sample_color" && $op['label'] == "Color") {
                                    $optionValue = $op['value'];
                                    $sample_in_cart[] = $item->getProductId().'~'.$optionValue;
                                    $count_order_sample_in_cart += 1;
                                    break;
                                } 
                            }                     
                        } 
                    }
                   
                }

                $logger->info("system_sample_order_count ".$system_sample_order_count); 
                $logger->info("count_order_sample_in_cart ".$count_order_sample_in_cart);
                                       
                if ($system_sample_order_count == "" || $system_sample_order_count == "0" || $system_sample_order_count > $count_order_sample_in_cart) {

                    if (!empty($spidsColors) && count($spidsColors)>0) {
                        $ConfigUrl="";
                        if ($postparms['confproid']!="") {
                            $configprod = $this->product->load($postparms['confproid']);
                            $ConfigUrl = $configprod->getProductUrl();
                        }

                        $err_is_same_sample_is_exist = 0;
                        $cartItems = $this->checkoutSession->getQuote()->getAllItems();
                        foreach ($cartItems as $cartItem) {
                            $cartSampleItem = $cartItem->getProductId().'~'.$this->sampleBlock->getSampleProductColor($cartItem);
                            if (in_array($cartSampleItem,$spidsColors)) {
                                $err_is_same_sample_is_exist = 1;
                                $this->messageManager->addError('This product is already available in your cart.');
                                break;
                                /*throw new \Magento\Framework\Exception\LocalizedException(
                                    __('This product is already in your cart.')
                                );*/
                            }
                        }

                        if ( $err_is_same_sample_is_exist != 1) {
                            foreach ($spidsColors as $spidColor) {
                                $additionalOptions = array();
                                $params = array();
                                $spidColor_arr = explode('~', $spidColor);
                                $sPid = $spidColor_arr[0];
                                $sColor = '';
                                if(!empty($spidColor_arr[1])) {
                                    $sColor = $spidColor_arr[1];
                                }
                                    
                                if ($system_sample_order_count > $count_order_sample_in_cart) 
                                {
                                    $sProduct = $this->_productRepository->getById($sPid);       
                                    $sku=$sProduct->getSku();                                
                                    $additionalOptions['configurable_product_name'] = [
                                        'label' => 'Product Name',
                                        'value' => "Sample-".$postparms['confproname']
                                    ];
                                    $additionalOptions['configurable_product_id'] = [
                                        'label' => 'Configurable Product Id',
                                        'value' => $postparms['confproid']
                                    ];
                                    $additionalOptions['configurable_product_url'] = [
                                        'label' => 'Configurable Product Url',
                                        'value' => $ConfigUrl
                                    ];
                                    $additionalOptions['sample_color'] = [
                                        'label' => 'Color',
                                        'value' => $sColor
                                    ];

                                    
                                    //$child_product=$this->_productRepository->getById($productId);
                                    $mainImageData = $this->imageHelper->init($sProduct, 'product_thumbnail_image')->getUrl() ;                                         
    
                                    $additionalOptions['configurable_product_image'] = [
                                        'label' => 'Configurable Product Image',
                                        'value' => $mainImageData,
                                        'alt'=>$postparms['confproname']
                                    ];

                                    $params = array(
                                            'product' => $sPid,
                                            'name' => $postparms['confproname'],
                                            'qty' => 1
                                        );

                                    $_sProduct = $this->_productRepository->getById($sPid);
                                    $_sProduct->setName('ppppppp'.$postparms['confproname']);
                                    $_sProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                                    $this->cart->addProduct($_sProduct, $params);                                 
                                    $this->cart->save();                                
                                    $quoteId=(int)$this->checkoutSession->getQuote()->getId();                      
                                    $quoteRepository = $this->quoteRepository; 
                                    $logger->info("Quote Id ".$quoteId);
                                    $quote = $quoteRepository->get($quoteId);                     
                                    $quote->setTotalsCollectedFlag(false);
                                    $quote->setTriggerRecollect(1);
                                    $quote->collectTotals();
                                    $quote->getShippingAddress()->setCollectShippingRates(true);
                                    $quote->save();
                                    $this->cart->save();   
                                    $this->messageManager->addSuccess(__('Sample Product Add Successfully.'));
                                    $is_sample_added = 1;
                                    $count_order_sample_in_cart++;                  
                                } else {
                                    $logger->info("else 1");
                                    $this->messageManager->addError(__($system_sample_order_msg));
                                }                                      
                            }
                        }
                        
                    } else {
                        $this->messageManager->addError(__('No sample product selected'));
                    }
                         
                } else {
                    $logger->info("else 3");
                    $this->messageManager->addError(__($system_sample_order_msg));
                }    
            }
        }
        $resultJson = $this->resultJsonFactory->create();
        //$error = '';

        $output = [
            'is_sample_added' => $is_sample_added
        ];
            
        $result = $resultJson->setData($output);
        return $result;
    }
}
