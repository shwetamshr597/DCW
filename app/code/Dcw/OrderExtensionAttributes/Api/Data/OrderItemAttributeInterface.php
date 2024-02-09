<?php
namespace Dcw\OrderExtensionAttributes\Api\Data;

/**
 * Interface for saving the checkout comment to the quote for orders of logged in users
 * @api
 */
interface OrderItemAttributeInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    
    /**
     * Incstore Item Shipping
     */
    const INCSTORE_ITEM_SHIPPING = 'incstore_item_shipping';
	
	
    /**
     * Get Incstore Item Shipping
     *
     * @return string
     */
    public function getIncstoreItemShipping();

    /**
     * Set Incstore Item Shipping
     *
     * @param string $value
     * @return $this
     */
    public function setIncstoreItemShipping($value);

}
