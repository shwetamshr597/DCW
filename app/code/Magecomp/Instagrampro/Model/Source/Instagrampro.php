<?php

namespace Magecomp\Instagrampro\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Instagrampro extends AbstractSource
{
    protected $_configScopeConfigInterface;
    protected $_helperData;
    protected $_storeManager;

    public function __construct(
        ScopeConfigInterface $configScopeConfigInterface,
        StoreManagerInterface $storeManager
    ) {
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_storeManager = $storeManager;
    }

    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option["value"]] = $option["label"];
        }
        return $_options;
    }

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $tags = array();
            if($this->_configScopeConfigInterface->getValue('instagrampro/generalgroup/tags', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()) != NULL){
                $tags = explode(',', $this->_configScopeConfigInterface->getValue('instagrampro/generalgroup/tags', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()));
            }
            

            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (empty($tag)) {
                    continue;
                }
                $this->_options[] = ['label' => $tag, 'value' => base64_encode($tag)];
            }
            $this->_options[] = ['label' => __('Do not show'), 'value' => 0];
        }

        return $this->_options;
    }
}
