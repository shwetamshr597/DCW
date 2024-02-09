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

namespace ParadoxLabs\TokenBase\Block\Adminhtml\Config;

/**
 * ApiTest Class
 */
abstract class ApiTest extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $helper;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var \ParadoxLabs\TokenBase\Model\Method\Factory
     */
    protected $methodFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \ParadoxLabs\TokenBase\Model\Method\Factory $methodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \ParadoxLabs\TokenBase\Model\Method\Factory $methodFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->storeFactory = $storeFactory;
        $this->websiteFactory = $websiteFactory;
        $this->methodFactory = $methodFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get store ID from current request scope.
     *
     * @return int
     */
    protected function getStoreId()
    {
        if ($this->storeId === null) {
            if ($this->_request->getParam('store') != '') {
                /** @var \Magento\Store\Model\Store $store */
                $store = $this->storeFactory->create();
                $store->load($this->_request->getParam('store'));

                $this->storeId = (int)$store->getId();
            } elseif ($this->_request->getParam('website') != '') {
                /** @var \Magento\Store\Model\Website $website */
                $website = $this->websiteFactory->create();
                $website->load($this->_request->getParam('website'));

                $this->storeId = $website->getDefaultStore()->getId();
            } else {
                $this->storeId = 0;
            }
        }

        return $this->storeId;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = (string)$this->testApi();

        if (strpos($html, 'success') !== false) {
            $html = '<strong style="color:#0a0;">' . $html . '</strong>';
        } else {
            $html = '<strong style="color:#D40707;">' . $html . '</strong>';
        }

        return $html;
    }

    /**
     * Determine whether the given string contains values outside the standard ASCII charset.
     *
     * @param string $string
     * @return bool
     */
    protected function containsInvalidCharacters($string)
    {
        return (bool)preg_match('/[^ -~]/i', (string)$string);
    }

    /**
     * Method to test the API connection. Should return a string indicating success or error.
     *
     * @return mixed
     */
    abstract protected function testApi();
}
