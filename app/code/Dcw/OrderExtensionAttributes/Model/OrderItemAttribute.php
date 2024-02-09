<?php
namespace Dcw\OrderExtensionAttributes\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Api\AbstractSimpleObject;

class OrderItemAttribute extends AbstractSimpleObject implements \Dcw\OrderExtensionAttributes\Api\Data\OrderItemAttributeInterface
{
	  /**
     * Get getIncstoreItemShipping
     *
     * @return string
     */
    public function getIncstoreItemShipping()
    {
        return $this->_get(self::INCSTORE_ITEM_SHIPPING);
    }

    /**
     * Set Incstore Shipping
     *
     * @param string $value
     */
    public function setIncstoreItemShipping($value)
    {
        return $this->setData(self::INCSTORE_ITEM_SHIPPING, $value);
    }
	
}
