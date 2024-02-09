<?php

namespace Dcw\ShoppingCart\Plugin\Checkout;

class LayoutProcessorPlugin
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    )
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] = [
            'component' => 'Magento_Ui/js/form/components/group',
           // 'label' => __('Address Label'), //sreet address main label
            'required' => false, // set false if removed sreet address label
            'dataScope' => 'shippingAddress.street',
            'provider' => 'checkoutProvider',
            'sortOrder' => 50,
            'type' => 'group',
            'additionalClasses' => 'street',
            'children' => [   // here add children as per your requirement
                [
                    'label' => __('Street Address'),
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '0',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                ],
                [
                    'label' => __('Suite/Apt'),
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '1',
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => false, "min_text_len‌​gth" => 1, "max_text_length" => 255],
                ],
                
            ]
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['label'] = __('Select State or province.');
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['label'] = __('Zip Code');
// Change the sortOrder of the company field to 1
$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
['children']['shippingAddress']['children']['shipping-address-fieldset']
['children']['company']['sortOrder'] = 40;

// Change the sortOrder of the street field to 2
$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
['children']['shippingAddress']['children']['shipping-address-fieldset']
['children']['street']['sortOrder'] = 48;

// Change the sortOrder of the street field to 2
$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
['children']['shippingAddress']['children']['shipping-address-fieldset']
['children']['city']['sortOrder'] = 51;
        return $jsLayout;
    }
}