<?php

namespace Dcw\Company\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Dcw\Company\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;

class ProcessOrder implements ObserverInterface
{
    protected $order;
    public function __construct(
        OrderInterface $order,
        Data $helperData
    ) {
         $this->order = $order;
         $this->helperData = $helperData;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $lastOrder = $this->order->load($orderId);
        $itemCollection = $lastOrder->getItemsCollection();
        $customerId = $lastOrder->getCustomerId();
        foreach ($itemCollection as $key => $item) {
            //$sku=$item->getProduct()->getSku();
            //if($sku=="FREE-TSHIRT") {
            if ($item->getProduct()->getFreeTshirt() == 1) {
                $tshirtStatus= $this->helperData->getTshirtAvailedValue();
                $this->helperData->updateCustomerData($customerId, $tshirtStatus);
            }
        }
    }
}
