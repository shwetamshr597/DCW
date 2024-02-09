<?php
declare(strict_types=1);

namespace Hyva\MagentoDataServices\Plugin;

use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;

class AddDataServicesCartSectionData
{
    private CheckoutSession $checkoutSession;

    public function __construct(
        CheckoutSession $checkoutSession
    ) {

        $this->checkoutSession = $checkoutSession;
    }
    public function afterGetSectionData(Cart $subject, array $result): array
    {
        $quote = $this->checkoutSession->getQuote();
        $totals = $quote->getTotals();
        $subtotal = $totals['subtotal'];

        $result['dsCartId'] = $quote->getId();
        $result['subtotalAmountExclTax'] = (float)$subtotal->getValueExclTax();
        return $result;
    }
}
