var config = {
    map: {
        '*': {
          'Magento_NegotiableQuote/template/shipping.html': 
              'Dotcomweavers_OrderRestrictions/template/shipping.html',
          'Magento_NegotiableQuote/template/shipping-address/address-renderer/default.html': 
              'Dotcomweavers_OrderRestrictions/template/shipping-address/address-renderer/default_negotiable_quote.html',
          'Magento_Checkout/template/shipping-address/address-renderer/default.html': 
              'Dotcomweavers_OrderRestrictions/template/shipping-address/address-renderer/default_checkout.html',
          'Magento_PurchaseOrder/template/checkout/shipping-address/address-renderer/default.html': 
              'Dotcomweavers_OrderRestrictions/template/shipping-address/address-renderer/default_purchase_order.html',
        }
  }
};