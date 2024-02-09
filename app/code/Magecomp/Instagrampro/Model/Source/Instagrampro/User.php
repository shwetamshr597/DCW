<?php

namespace Magecomp\Instagrampro\Model\Source\Instagrampro;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class User extends AbstractSource
{
    protected $_configScopeConfigInterface;
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
            $users = array();
            if($this->_configScopeConfigInterface->getValue('instagrampro/aut/ig_business_username', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()) != NULL){
                $users = explode(',', $this->_configScopeConfigInterface->getValue('instagrampro/aut/ig_business_username', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()));
            }
            
            foreach ($users as $user) {
                $user = trim($user);
                if (empty($user)) {
                    continue;
                }
                $this->_options[] = ['label' => $user, 'value' => $user];
            }
            // No show images
            $this->_options[] = ['label' => __('Do not show'), 'value' => 0];
        }

        return $this->_options;
    }
}
