<?php

namespace Magecomp\Instagrampro\Cron;

use Magecomp\Instagrampro\Helper\Image\Hashtag;
use Magecomp\Instagrampro\Helper\Image\User;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Updatefeed
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_HASHTAG = 0;

    protected $_imageUser;
    protected $_imageHashtag;
    protected $_scopeConfig;
    protected $_storeManager;

    public function __construct(
        Context $context,
        ScopeConfigInterface $configScopeConfigInterface,
        User $imageUser,
        Hashtag $imageHashtag,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $configScopeConfigInterface;
        $this->_imageUser = $imageUser;
        $this->_imageHashtag = $imageHashtag;
        $this->_storeManager = $storeManager;
    }

    public function updatelikes()
    {

        foreach ($this->_storeManager->getStores(true) as $curstore) {
            $StoreId = $curstore->getStoreId();

            $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $StoreId);

            switch ($updateType) {
                case self::UPDATE_TYPE_HASHTAG:
                    $this->_imageHashtag->update($StoreId);
                    break;
                case self::UPDATE_TYPE_USER:
                    $this->_imageUser->updateUserLies($StoreId);
                    break;
            }
        }
    }

    public function updateimages()
    {
        $autoFetchImage = $this->_scopeConfig->getValue('instagrampro/cronsetup/autofetch', ScopeInterface::SCOPE_STORE);
        if ($autoFetchImage) {
            foreach ($this->_storeManager->getStores(true) as $curstore) {
                $StoreId = $curstore->getStoreId();
                $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $StoreId);
                switch ($updateType) {
                    case self::UPDATE_TYPE_HASHTAG:
                        $this->_imageHashtag->update($StoreId);
                        break;
                    case self::UPDATE_TYPE_USER:
                        $this->_imageUser->update($StoreId);
                        break;
                }
            }
        }
    }

    public function updatelink()
    {
            
        foreach ($this->_storeManager->getStores(true) as $curstore) {
            $storeId = $curstore->getStoreId();
            $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $storeId);
            switch ($updateType) {
                case self::UPDATE_TYPE_USER:
                    $this->_imageUser->updatemediaurl($storeId);
                    break;
                case self::UPDATE_TYPE_HASHTAG:
                    $this->_imageUser->updatemediaurl($storeId);
                    break;
            }
        }
    }
}
