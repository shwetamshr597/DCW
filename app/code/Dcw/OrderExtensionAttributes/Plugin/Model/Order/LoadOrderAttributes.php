<?php
namespace Dcw\OrderExtensionAttributes\Plugin\Model\Order;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Sales\Model\OrderFactory;
use \Magento\Sales\Api\Data\OrderExtensionFactory;
class LoadOrderAttributes
{
    private $orderFactory;

    private $orderExtensionFactory;

    protected $orderItemExtension;

    public function __construct(
        OrderFactory $orderFactory,
        OrderExtensionFactory $extensionFactory,
        \Magento\Sales\Api\Data\OrderItemExtensionInterfaceFactory $orderItemExtension
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderExtensionFactory = $extensionFactory;
        $this->orderItemExtension = $orderItemExtension;
    }

    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
       
        foreach ($resultOrder->getItems() as $item) 
        {
            $extensionAttributes = $this->orderItemExtension->create();
            $extensionAttributes->setData('incstore_item_shipping', $item->getIncstoreItemShipping());
            $item->setExtensionAttributes($extensionAttributes);
        }
        return $resultOrder;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $orderSearchResult
    ) {
        foreach ($orderSearchResult->getItems() as $order) 
        {
            foreach ($order->getItems() as $item) 
            {
                $extensionAttributes = $this->orderItemExtension->create();
                $extensionAttributes->setData('incstore_item_shipping', $item->getincstoreItemShipping());
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
        return $orderSearchResult;
    }

}
