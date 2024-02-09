<?php

namespace Magecomp\Instagrampro\Model;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;


class Instagramauth
{
    const INSTAGRAM_SESSION_DATA_KEY = 'instagrampro_session_data';
    const INSTAGRAM_CONFIG_DATA_KEY = 'magecomp/instagrampro/instagrampro_data';
    protected $_modelSessionFactory;
    protected $_configScopeConfigInterface;

    public function __construct(
        Session $modelSessionFactory,
        ScopeConfigInterface $configScopeConfigInterface
    ) {
        $this->_modelSessionFactory = $modelSessionFactory;
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
    }

    public function isValid()
    {
        $configDataKey = self::INSTAGRAM_CONFIG_DATA_KEY;
        return (!!$this->getUserData() || $this->_configScopeConfigInterface->getValue($configDataKey, ScopeInterface::SCOPE_STORE, 0));
    }

    public function getUserData()
    {
        $session = $this->_modelSessionFactory;
        $info = $session->getData('instagrampro_session_data');
        if (!$info) {
            $configDataKey = self::INSTAGRAM_CONFIG_DATA_KEY;
            $info = $this->serialize->unserialize($this->_configScopeConfigInterface->getValue($configDataKey, ScopeInterface::SCOPE_STORE, 0));
        }
        return $info;
    }

    public function getAccessToken()
    {
        return $this->getUserData()->access_token;
    }
}
