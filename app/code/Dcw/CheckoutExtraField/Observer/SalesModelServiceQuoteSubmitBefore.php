<?php
namespace Dcw\CheckoutExtraField\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');
        $shippingAddressData = $quote->getShippingAddress()->getData();
        if (isset($shippingAddressData['phone_ext'])) {
            $order->getShippingAddress()->setPhoneExt($shippingAddressData['phone_ext']);
        }

        return $this;
    }
}