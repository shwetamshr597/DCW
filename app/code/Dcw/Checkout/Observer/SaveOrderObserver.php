<?php
namespace Dcw\Checkout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveOrderObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setData('loading_dock', $quote->getLoadingDock());
		$order->setData('residential_address', $quote->getResidentialAddress());

        return $this;
    }
}