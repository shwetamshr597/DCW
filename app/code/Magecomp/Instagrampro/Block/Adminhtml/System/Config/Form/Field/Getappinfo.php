<?php

namespace Magecomp\Instagrampro\Block\Adminhtml\System\Config\Form\Field;

use Magecomp\Instagrampro\Helper\Graphapi;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Getappinfo extends Field
{
    protected $_template = 'Magecomp_Instagrampro::instagrampro/config/form/field/getappinfo.phtml';
    protected $urlInterface;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        UrlInterface $urlInterface,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('instagrampro/auth/collectdata');
    }

    public function isIGConfigure()
    {
        return $this->scopeConfig->getValue(Graphapi::GRAPH_API_CONFIG_IG_BUSINESS_USERNAME, ScopeInterface::SCOPE_STORE);
    }

    public function getIGProfilePicForCurrentStore()
    {
        return $this->scopeConfig->getValue(Graphapi::GRAPH_API_CONFIG_IG_BUSINESS_PROFILEPIC, ScopeInterface::SCOPE_STORES, $this->getCurrentStoreId());
    }

    public function getCurrentStoreId()
    {

        $storeId = 0;
        $urlarray = explode('/', $this->urlInterface->getCurrentUrl());
        if (in_array('store', $urlarray)) {
            $key = array_search('store', $urlarray);
            $storeId = $urlarray[$key + 1];

        }
        return $storeId;
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'btnid',
                'label' => __('Authenticate APP'),
            ]
        );

        return $button->toHtml();
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
