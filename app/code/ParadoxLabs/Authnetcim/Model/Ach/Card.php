<?php
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\Authnetcim\Model\Ach;

/**
 * Authorize.Net CIM ACH card model
 */
class Card extends \ParadoxLabs\Authnetcim\Model\Card
{
    /**
     * Set card payment data from a quote or order payment instance.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    public function importPaymentInfo(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::importPaymentInfo($payment);

        if ($payment instanceof \Magento\Payment\Model\InfoInterface) {
            /** @var \Magento\Payment\Model\Info $payment */

            if (!empty($payment->getData('echeck_account_name'))) {
                $this->setAdditional('echeck_account_name', $payment->getData('echeck_account_name'));
            }

            if (!empty($payment->getData('echeck_bank_name'))) {
                $this->setAdditional('echeck_bank_name', $payment->getData('echeck_bank_name'));
            }

            if (!empty($payment->getData('echeck_account_type'))) {
                $this->setAdditional('echeck_account_type', $payment->getData('echeck_account_type'));
            }

            if (!empty($payment->getData('echeck_routing_no'))) {
                $this->setAdditional(
                    'echeck_routing_number_last4',
                    substr((string)$payment->getData('echeck_routing_no'), -4)
                );
            }

            if (!empty($payment->getData('echeck_account_no'))) {
                $this->setAdditional(
                    'echeck_account_number_last4',
                    substr((string)$payment->getData('echeck_account_no'), -4)
                );
            }
        }

        return $this;
    }

    /**
     * Get card label (formatted number).
     *
     * @param bool $includeType
     * @return string|\Magento\Framework\Phrase
     */
    public function getLabel($includeType = true)
    {
        return sprintf(
            '%s: x-%s',
            $this->getAdditional('echeck_bank_name'),
            $this->getAdditional('echeck_account_number_last4')
        );
    }

    /**
     * On card save, set payment data to the gateway. (Broken out for extensibility)
     *
     * @param \ParadoxLabs\TokenBase\Api\GatewayInterface $gateway
     * @return $this
     */
    protected function setPaymentInfoOnCreate(\ParadoxLabs\TokenBase\Api\GatewayInterface $gateway)
    {
        /** @var \Magento\Payment\Model\Info $info */
        $info = $this->getInfoInstance();

        if ($info->getData('echeck_account_type') != 'businessChecking') {
            $gateway->setParameter('echeckType', 'PPD');
        } else {
            $gateway->setParameter('echeckType', 'CCD');
        }

        $gateway->setParameter('nameOnAccount', $info->getData('echeck_account_name'));
        $gateway->setParameter('bankName', $info->getData('echeck_bank_name'));
        $gateway->setParameter('accountType', $info->getData('echeck_account_type'));
        $gateway->setParameter('routingNumber', $info->getData('echeck_routing_no'));
        $gateway->setParameter('accountNumber', $info->getData('echeck_account_no'));

        return $this;
    }

    /**
     * On card update, set payment data to the gateway. (Broken out for extensibility)
     *
     * @param \ParadoxLabs\TokenBase\Api\GatewayInterface $gateway
     * @return $this
     */
    protected function setPaymentInfoOnUpdate(\ParadoxLabs\TokenBase\Api\GatewayInterface $gateway)
    {
        /** @var \Magento\Payment\Model\Info $info */
        $info = $this->getInfoInstance();

        if ($info->getData('echeck_account_type') != 'businessChecking') {
            $gateway->setParameter('echeckType', 'PPD');
        } else {
            $gateway->setParameter('echeckType', 'CCD');
        }

        $gateway->setParameter('nameOnAccount', $info->getData('echeck_account_name'));
        $gateway->setParameter('bankName', $info->getData('echeck_bank_name'));
        $gateway->setParameter('accountType', $info->getData('echeck_account_type'));

        // Potentially masked routing number
        if (strlen((string)$info->getData('echeck_routing_no')) > 8) {
            $gateway->setParameter('routingNumber', $info->getData('echeck_routing_no'));
        } else {
            $gateway->setParameter('routingNumber', 'XXXX' . $this->getAdditional('echeck_routing_number_last4'));
        }

        // Potentially masked account number
        if (strlen((string)$info->getData('echeck_account_no')) > 8) {
            $gateway->setParameter('accountNumber', $info->getData('echeck_account_no'));
        } else {
            $gateway->setParameter('accountNumber', 'XXXX' . $this->getAdditional('echeck_account_number_last4'));
        }

        return $this;
    }
}
