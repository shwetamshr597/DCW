<?php
namespace Dcw\Company\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseFactory;

class RestrictCheckoutObserver implements ObserverInterface
{
protected $checkoutSession;
protected $messageManager;
/**
     * @var \Magento\Framework\UrlInterface
     */
private $url;

/**
     * @var \Magento\Framework\App\ResponseFactory
     */
private $responseFactory;

public function __construct (CheckoutSession $checkoutSession, MessageManager $messageManager, UrlInterface $url, ResponseFactory $responseFactory)
{
$this->checkoutSession = $checkoutSession;
$this->messageManager = $messageManager;
$this->url = $url;
$this->responseFactory = $responseFactory;
}

public function execute (\Magento\Framework\Event\Observer $observer)
{ 
$quote = $this->checkoutSession->getQuote ();
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
foreach ($quote->getAllVisibleItems () as $item) {
$product = $objectManager->get('\Magento\Catalog\Model\ProductRepository')->get($item->getSku());
if ($product->getFreeTshirt() == 1 &&  $product->getAttributeText('size') == 'NA') { 
// Throw an exception and redirect to cart page
//throw new \Magento\Framework\Exception\LocalizedException (__('You cannot proceed to checkout with this product. Please EDIT Free T-Shirt product in cart to add T-Shirt Size !'));
$this->messageManager->addErrorMessage (__('Please EDIT Free T-Shirt product in cart to add T-Shirt Size !'));
//return $this->redirectFactory->create()->setPath('checkout/cart');
$redirectionUrl = $this->url->getUrl('checkout/cart');
$this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();

return $this;
}
}
}
}