<?php

namespace Magecomp\Instagrampro\Helper;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractHelper
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_HASHTAG = 0;

    protected $_modelInstagramproimageFactory;
    protected $_helperData;
    protected $_helperImage;
    protected $_storeManager;

    public function __construct(
        Context $context,
        InstagramproimageFactory $modelInstagramproimageFactory,
        HelperData $helperData,
        Image $helperImage,
        StoreManagerInterface $storeManager
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_helperData = $helperData;
        $this->_helperImage = $helperImage;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getInstagramproGalleryImages($product,$apiCall = false)
    {

        if (!$product->hasData('instagrampro_gallery_images')) {
            $images = [];

            $tags = $this->_getProductTags($product);
            if (count($tags)) {
                $tagsCollection = $this->_modelInstagramproimageFactory->create()
                    ->getCollection()
                    ->setPageSize($this->_helperData->getProductPageLimit())
                    ->addFilter('is_approved', 1)
                    ->addFilter('is_visible', 1)
                    ->addFilter('tag', ['in' => $tags], 'public')
                    ->setOrder('instagrampro_image_id', 'DESC');
                foreach ($tagsCollection as $image) {
                    if($apiCall == true){
                        $images[] = $image->getData();
                    }
                    else{
                        $images[] = $image;
                    }
                    
                }
            }

            $product->setData('instagrampro_gallery_images', $images);
        }
        return $product->getData('instagrampro_gallery_images');
    }

    protected function _getProductTags($product)
    {

        $imgconfig = [];
        $updateType = $this->scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                if($product->getInstagramproSource() != Null){
                    $taglist = explode(',', $product->getInstagramproSource());
                    foreach ($taglist as $tagname) {
                        $imgconfig[] = base64_decode($tagname);
                    }
                }
                break;

            case self::UPDATE_TYPE_USER:
                if($product->getInstagramproSourceUser()!= NULL){
                    $userlist = explode(',', $product->getInstagramproSourceUser());
                    foreach ($userlist as $username) {
                        $imgconfig[] = $username;
                    }
                }
                break;
        }
        return $imgconfig;
    }
}
