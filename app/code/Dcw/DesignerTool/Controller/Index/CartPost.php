<?php
namespace Dcw\DesignerTool\Controller\Index;

use \Magento\Framework\Controller\ResultFactory;

class CartPost extends \Magento\Framework\App\Action\Action
{
    protected $formKey;
    protected $cart;
    protected $product;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultJson = $this->_resultJsonFactory->create();

        $cart_items = $this->getRequest()->getParam('cart_items');
        if (empty($cart_items)) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/checkout/cart');
            return $resultRedirect;
        }

        $cart_items_arr = json_decode($cart_items, true);

        //$additionalOptions = array();
        //$info_buyRequest = array();
        try {
            foreach ($cart_items_arr as $cart_item) {
                $productId =$cart_item['product_id'];
                $params = [
                    'form_key' => $this->formKey->getFormKey(),
                    'product' => $productId,
                    'qty'  => $cart_item['qty']
                ];

                /*$additionalOptions[] = [
                    'label' => 'Details',
                    'value' => $price_break_text,
                ];
                $additionalOptions[] = [
                    'label' => 'Reference',
                    'value' => $item->getSku(),
                ];*/

                $product = $this->_productFactory->create()->load($productId);
                //$product->setPrice($customPrice);
                //$product->addCustomOption('additional_options', serialize($additionalOptions));
                $this->cart->addProduct($product, $params);

                //$this->cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
                //$this->cart->getQuote()->save();
            }
            $this->cart->save();
            $this->cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
            $this->cart->getQuote()->save();

            $response = $resultJson->setData(['success' => 1]);

        } catch (\Exception $e) {
            $response = $resultJson->setData(['success' => 0,'message'=>$e->getMessage()]);
        }

        return $response;
    }
}
