<?php

namespace Hyva\MagentoDataServices\ViewModel;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;

class CheckoutSuccessContextProvider implements ArgumentInterface
{
    private Json $json;
    private  CheckoutSession $checkoutSession;
    private ProductHelper $productHelper;
    private CheckoutHelper $checkoutHelper;

    public function __construct(
        CheckoutSession $checkoutSession,
        ProductHelper $productHelper,
        CheckoutHelper $checkoutHelper,
        Json $json

    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productHelper = $productHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->json = $json;
    }

    public function getLastOrderCartContextData(): string
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $items = $this->getOrderItems($order);

        $context = [
            'id' => $this->checkoutSession->getLastRealOrder()->getQuoteId(),
            'totalQuantity' => count($items),
            'prices' => [
                'subtotalExcludingTax' => [
                    'value' => (float)$order->getBaseSubtotal()
                ],
                'subtotalIncludingTax' => [
                    'value' => (float)$order->getSubtotalInclTax()
                ]
            ],
            'possibleOnepageCheckout' => $this->checkoutHelper->canOnepageCheckout(),
            'giftMessageSelected' => (bool)$order->getGiftMessageId(),
            'giftWrappingSelected' => false,
            'items' => $items
        ];

        return $this->json->serialize($context);
    }

    private function getOrderItems(Order $order) : array
    {
        $context = [];
        $items = $order->getAllVisibleItems();

        foreach ($items as $item) {
            /** @var OrderItem $item */
            $context[] = [
                'id' => $item->getItemId(),
                'formattedPrice' => (float)$item->getPriceInclTax(),
                'quantity' => (int)$item->getQtyOrdered(),
                'prices' => [
                    'price' => [
                        'value' => (float)$item->getPriceInclTax()
                    ]
                ],
                'product' => $this->getItemProductData($item)
            ];
        }

        return $context;
    }

    private function priceAsFloat($price): ?float
    {
        return is_null($price) ? null : (float) $price;
    }

    private function getItemProductData(OrderItem $item) : array
    {
        $product = $item->getProduct();
        return [
            'productType' => $item->getProductType(),
            'productId' => $product->getId(),
            'name' => $item->getName(),
            'sku' => $item->getSku(),
            'mainImageUrl' => $this->productHelper->getImageUrl($product),
            'pricing' => [
                'regularPrice' => $this->priceAsFloat($product->getPrice()),
                'minimalPrice' => $this->priceAsFloat($product->getMinimalPrice()),
                'specialPrice' => $this->priceAsFloat($product->getSpecialPrice())
            ],
        ];
    }
}
