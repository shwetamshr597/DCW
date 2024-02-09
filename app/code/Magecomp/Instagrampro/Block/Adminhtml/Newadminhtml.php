<?php

namespace Magecomp\Instagrampro\Block\Adminhtml;

use Magecomp\Instagrampro\Helper\Image;
use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;

class Newadminhtml extends Container
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_HASHTAG = 0;

    protected $_helperImage;
    protected $_modelInstagramproimageFactory;

    public function __construct(
        Context $context,
        InstagramproimageFactory $modelInstagramproimageFactory,
        Image $helperImage,
        Repository $assetRepos,
        ImageFactory $helperImageFactory,
        array $data = []
    ) {
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;
        $this->_helperImage = $helperImage;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
        parent::__construct($context, $data);
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    public function getApproveUrl($image)
    {
        return $this->getUrl('*/*/approve/');
    }

    public function getDeleteUrl($image)
    {
        return $this->getUrl('*/*/delete/');
    }

    public function getUpdateType()
    {
        $Storeid = $this->getRequest()->getParam('store');
        $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $Storeid);
        $updateTypeMark = '';
        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                $updateTypeMark = "#";
                break;
            case self::UPDATE_TYPE_USER:
                $updateTypeMark = '@';
                break;
        }
        return $updateTypeMark;
    }

    public function getImages()
    {
        $imgconfig = [];

        $Storeid = $this->getRequest()->getParam('store');
        if ($Storeid == "" && $Storeid == null) {
            $Storeid = 0;
        }
        $updateType = $this->_scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $Storeid);

        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                $imgconfig = $this->_helperImage->getTags($Storeid);
                break;

            case self::UPDATE_TYPE_USER:
                $imgconfig = $this->_helperImage->getUsers($Storeid);
        }

        return $this->_modelInstagramproimageFactory->create()->getCollection()
            ->addFilter('is_approved', 0)
            ->addFilter('is_visible', 1)
            ->addFilter('tag', ['in' => $imgconfig], 'public')
            ->setOrder('instagrampro_image_id', 'DESC');
    }

    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }

    protected function _prepareLayout()
    {
        $Storeid = $this->getRequest()->getParam('store');

        $this->buttonList->add('flush_feed', [
            'label' => __('Delete Rejected Images'),
            'onclick' => "setLocation('{$this->getUrl('*/*/flush',['store' => $Storeid])}')",
            'class' => 'primary',
            'title' => 'This Button will Delete All Your Rejected Images Permanently From Database. This Can\'t be Undo'
        ]);

        $this->buttonList->add('load_more', [
            'label' => __('Load More Images'),
            'onclick' => "setLocation('{$this->getUrl('*/*/update',['store' => $Storeid])}')",
            'class' => 'primary',
            'title' => "Load More Instagram Images"
        ]);

        $this->buttonList->add('add_new', [
            'label' => __('Fetch New Images'),
            'onclick' => "setLocation('{$this->getUrl('*/*/update',['store' => $Storeid])}')",
            'class' => 'primary'
        ]);
        return parent::_prepareLayout();
    }
}
