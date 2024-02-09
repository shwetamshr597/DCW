<?php

namespace Magecomp\Instagrampro\Block;

use Magecomp\Instagrampro\Helper\Data as HelperData;
use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Homepage extends Template
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_HASHTAG = 0;

    protected $_helperData;
    protected $_helperImage;
    protected $_modelInstagramproimageFactory;

    public function __construct(
        Context $context,
        HelperData $helperData,
        Image $helperImage,
        InstagramproimageFactory $modelInstagramproimageFactory,
        Repository $assetRepos,
        ImageFactory $helperImageFactory,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_helperImage = $helperImage;
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {

        return parent::_prepareLayout();
    }

    public function showInstagramproImages()
    {
        $helper = $this->_helperData;
        return ($helper->isEnabled() && $helper->showImagesOnHomePage());
    }

    public function getInstagramproGalleryImages()
    {
        $images = [];
        $imgconfig = $this->getCurrentImageListConfig();
        if (count($imgconfig)) {
            $imagesCollection = $this->_modelInstagramproimageFactory->create()
                ->getCollection()
                ->setPageSize($this->_helperData->getHomePageLimit())
                ->addFilter('is_approved', 1)
                ->addFilter('is_visible', 1)
                ->addFilter('tag', ['in' => $imgconfig], 'public')
                ->setOrder('instagrampro_image_id', 'DESC');
            return $imagesCollection->getData();
        }
        return $images;
    }

    public function getCurrentImageListConfig()
    {
        $curlist = [];
        $StoreId = $this->_storeManager->getStore()->getId();
        if ($StoreId == '' && $StoreId == null) {
            $StoreId = 0;
        }
        $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, 0);
        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                $curlist = $this->_helperImage->getTags($StoreId);
                break;

            case self::UPDATE_TYPE_USER:
                $curlist = $this->_helperImage->getUsers(0);
        }
        return $curlist;
    }

    public function getInstagrampropageGalleryImages($apicall = false)
    {
        $images = [];
        $imgconfig = $this->getCurrentImageListConfig();
        $helper = $this->_helperData;
        if (count($imgconfig) && $helper->isEnabled()) {
            $imagesCollection = $this->_modelInstagramproimageFactory->create()
                ->getCollection()
                ->setPageSize($this->_helperData->getGalleryPageLimit())
                ->addFilter('is_approved', 1)
                ->addFilter('is_visible', 1)
                ->addFilter('tag', ['in' => $imgconfig], 'public')
                ->setOrder('instagrampro_image_id', 'DESC');

            $imagesCollection->getSelect()->order(new \Zend_Db_Expr('RAND()'));
            foreach ($imagesCollection as $image) {
                if($apicall == true){
                    $images[] = $image->getData();
                }
                else{
                    $images[] = $image;
                }
               
            }
        }


        return $images;
    }

    public function getPopupUrl()
    {
        return $this->getUrl('instagrampro/gallery/popuphtml');
    }
    public function getGalleryPopupUrl()
    {
        return $this->getUrl('instagrampro/gallery/gallerypopuphtml');
    }

    public function getPopupChangeProductUrl()
    {
        return $this->getUrl('instagrampro/gallery/popupchangeprod');
    }

    public function showProductInPopup()
    {
        return $this->_helperImage->getPopupConfiguration();
    }


    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
}
