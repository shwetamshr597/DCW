<?php

namespace Magecomp\Instagrampro\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractHelper
{
    protected $_modelConfigFactory;
    protected $_storeManager;

    public function __construct(
        Context $context,
        WriterInterface $modelConfigFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_modelConfigFactory = $modelConfigFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getFetchImageCount($StoreId)
    {
        return $this->scopeConfig->getValue('instagrampro/generalgroup/imagefatch', ScopeInterface::SCOPE_STORE, $StoreId);
    }

    public function getTags($storeid)
    {
        $conTags = $this->scopeConfig->getValue('instagrampro/generalgroup/tags', ScopeInterface::SCOPE_STORE, $storeid);
        $tags = array();
        if($conTags != NULL){
            $tags = explode(',', $conTags);
        }
        return $tags;
        
    }

    public function getTagsURL($hashid)
    {
        return 'https://www.instagram.com/explore/tags/' . $hashid . '/?__a=1';
    }

    public function getVideoURL($videocode)
    {
        return 'https://www.instagram.com/p/' . $videocode . '/?__a=1';
    }

    public function getUsers($storeid)
    {
        $conUsers = $this->scopeConfig->getValue('instagrampro/aut/ig_business_username', ScopeInterface::SCOPE_STORE, $storeid);
        $user = array();
        if($conUsers != NULL){
            $users = explode(',', $conUsers);
        }
 
        return $users;
    }

    public function getUsersURL($userid)
    {
        return 'https://www.instagram.com/' . $userid . '/?__a=1';
    }

    public function getPopupConfiguration()
    {
        return $this->scopeConfig->getValue('instagrampro/popupgroup/displayproduct', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
    }
}
