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

namespace ParadoxLabs\TokenBase\Observer;

/**
 * Custom data field conversion -- quote to order, etc, etc.
 */
class ConvertOrderToQuoteObserver extends ConvertAbstract implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Copy fields from order payment to quote payment
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote  = $observer->getEvent()->getData('quote');

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $observer->getEvent()->getData('order');

        // Do the magic. Yeah, this is it.
        $this->copyData(
            $order->getPayment(),
            $quote->getPayment()
        );
    }
}
