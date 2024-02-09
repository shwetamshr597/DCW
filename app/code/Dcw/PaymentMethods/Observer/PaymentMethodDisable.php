<?php

namespace Dcw\PaymentMethods\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodDisable implements ObserverInterface
{
    public function __construct(
        \Dcw\PaymentMethods\Block\Index $dcwPaymentBlock,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->dcwPaymentBlock = $dcwPaymentBlock;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (empty($this->dcwPaymentBlock->getAdminIdLoggedAsCustomer()) ||
        $this->dcwPaymentBlock->getAdminIdLoggedAsCustomer() == 0) {
            if ($observer->getEvent()->getMethodInstance()->getCode()=="authnetcim_ach" ||
            $observer->getEvent()->getMethodInstance()->getCode()=="wirepayment" ||
            $observer->getEvent()->getMethodInstance()->getCode()=="purchaseorder") {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false);
            }
        }
    }
}
