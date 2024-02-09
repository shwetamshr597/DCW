/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
	'jquery',
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver',
	'Magento_Checkout/js/model/quote'
], function ($, ko, checkoutDataResolver, quote) {
    'use strict';

    var shippingRates = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),

        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);
			
			if (quote.shippingAddress()){
				console.log(quote.shippingAddress());
				if($("#checkout-dcw-address").length){
					if($("#shipping-new-address-form").length){ 
						if($('#shipping-new-address-form').find('input[name="postcode"]').val()){
							$('#checkout-dcw-address').trigger('click');
						}
					}
					}
			}
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            return shippingRates;
        }
    };
});
