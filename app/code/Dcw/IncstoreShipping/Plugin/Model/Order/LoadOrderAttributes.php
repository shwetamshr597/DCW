<?php
namespace Dcw\IncstoreShipping\Plugin\Model\Order;

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
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shippingmetadata.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info('after get meta data value 2');
                
       
        foreach ($resultOrder->getItems() as $item) 
        {
            $extensionAttributes = $item->getExtensionAttributes();

            // Preserve existing extension attribute values
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderItemExtension->create();
            }
            
            $data_array = $item->getShippingMetadata();
            $pdpLineItem = $item->getPdpLineItem();
            $pdplineitemObject = json_decode($pdpLineItem ?? '');
            $shippingMetadataObject = json_decode($data_array ?? '');
            
            // print_r($ass_array); exit;
            
            $extensionAttributes->setData('shippingQuoteRequestId', $shippingMetadataObject->shippingQuoteRequestId ?? '0');
            $extensionAttributes->setData('shippingQuoteId', $shippingMetadataObject->shippingQuoteId ?? '0');
            $extensionAttributes->setData('shippingQuoteCharge', $shippingMetadataObject->shippingQuoteCharge ?? '0');
            $extensionAttributes->setData('shippingQuoteCost', $shippingMetadataObject->shippingQuoteCost ?? '0');
            $extensionAttributes->setData('shippingQuoteCarrier', $shippingMetadataObject->shippingQuoteCarrier ?? ' ');
            $extensionAttributes->setData('shippingQuoteShipFromManufacturerId', $shippingMetadataObject->shippingQuoteShipFromManufacturerId ?? '0');
            $extensionAttributes->setData('shippingQuoteShipFromLocationId', $shippingMetadataObject->shippingQuoteShipFromLocationId ?? '0');
            $extensionAttributes->setData('shippingQuoteShipFromManufacturerName', $shippingMetadataObject->shippingQuoteShipFromManufacturerName ?? ' ');
            $extensionAttributes->setData('shippingQuoteShipFromStreetAddress', $shippingMetadataObject->shippingQuoteShipFromStreetAddress ?? ' ');
            $extensionAttributes->setData('shippingQuoteShipFromCity', $shippingMetadataObject->shippingQuoteShipFromCity ?? ' ');
            $extensionAttributes->setData('shippingQuoteShipFromState', $shippingMetadataObject->shippingQuoteShipFromState ?? ' ');
            $extensionAttributes->setData('shippingQuoteShipFromZipCode', $shippingMetadataObject->shippingQuoteShipFromZipCode ?? '0');
            $extensionAttributes->setData('shippingQuoteShipFromCountry', $shippingMetadataObject->shippingQuoteShipFromCountry ?? ' ');

            //PDP Calculation Attributes
            $extensionAttributes->setData('pricePerSqft', $pdplineitemObject->PricePerSqft ?? 0);
            $extensionAttributes->setData('LineItemUnitQuantity', $pdplineitemObject->LineItemUnitQuantity ?? 0);
            $extensionAttributes->setData('UnitPrice', $pdplineitemObject->UnitPrice ?? 0);
            $extensionAttributes->setData('LineItemPrice', $pdplineitemObject->LineItemPrice ?? 0);
            $extensionAttributes->setData('TileSize', $pdplineitemObject->TileSize ?? ' ');
            $extensionAttributes->setData('Covers', $pdplineitemObject->Covers ?? 0);
            $extensionAttributes->setData('SquareFootage', $pdplineitemObject->SquareFootage ?? 0);
            $extensionAttributes->setData('Length', $pdplineitemObject->Length ?? 0);
            $extensionAttributes->setData('Width', $pdplineitemObject->Width ?? 0);
            $extensionAttributes->setData('PricePerLinearFoot', $pdplineitemObject->PricePerLinearFoot ?? 0);
            $extensionAttributes->setData('RoomWidth', $pdplineitemObject->RoomWidth ?? 0);
            $extensionAttributes->setData('RoomLength', $pdplineitemObject->RoomLength ?? 0);
            $extensionAttributes->setData('RollType', $pdplineitemObject->RollType ?? ' ');
            $extensionAttributes->setData('QuantityRange', $pdplineitemObject->QuantityRange ?? 0);
            $extensionAttributes->setData('RollSize', $pdplineitemObject->RollSize ?? 0);
            $extensionAttributes->setData('TrailerRollType', $pdplineitemObject->TrailerRollType ?? ' ');
            $extensionAttributes->setData('CustomLength', $pdplineitemObject->CustomLength ?? 0);
            $extensionAttributes->setData('CbcShopBy', $pdplineitemObject->CBCShopBy ?? ' ');
            $extensionAttributes->setData('CbcTileSize', $pdplineitemObject->CBCTileSize ?? ' ');
            $extensionAttributes->setData('BorderTileQty', $pdplineitemObject->BorderTileQty ?? 0);
            $extensionAttributes->setData('CenterTileQty', $pdplineitemObject->CenterTileQty ?? 0);
            $extensionAttributes->setData('CornerTileQty', $pdplineitemObject->CornerTileQty ?? 0);
            // End
            $item->setExtensionAttributes($extensionAttributes);
                
            $logger->info("metadataSetExtension done ");
            
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
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shippingmetadata.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info('meta data value 3');

                $extensionAttributes = $this->orderItemExtension->create(); $logger->info('meta data value 4 '.$item->getShippingMetadata());
                $extensionAttributes->setData('shipping_metadata', $item->getshippingMetadata());
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
        return $orderSearchResult;
    }

}
