<?php
namespace Magecomp\Instagrampro\Helper;

use Magento\Backend\Model\UrlFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    protected $_modelUrlFactory;
    protected $_storeManager;
    protected $configWriter;

    public function __construct(
        Context $context,
        UrlFactory $modelUrlFactory,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter
    ) {
        $this->_modelUrlFactory = $modelUrlFactory;
        $this->_storeManager = $storeManager;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue('instagrampro/module_options/enabled', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function isShuffling()
    {
        return (bool)$this->scopeConfig->getValue('instagrampro/gallerygroup/shuffle', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getCurrentStoreInfo()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function showImagesOnHomePage()
    {
        return (bool)$this->scopeConfig->getValue('instagrampro/homegroup/homepage', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getHomePageLimit()
    {
        return (int)$this->scopeConfig->getValue('instagrampro/homegroup/homepage_limit', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getProductPageLimit()
    {
        return (int)$this->scopeConfig->getValue('instagrampro/productgroup/product_limit', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getImageOnProductsection()
    {
        if ($this->showImagesOnProductPage()) {
            return (bool)$this->scopeConfig->getValue('instagrampro/productgroup/product_detail', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
        }
    }

    public function showImagesOnProductPage()
    {
        return (bool)$this->scopeConfig->getValue('instagrampro/productgroup/product', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getImageOnProductMoreViewsection()
    {
        if ($this->showImagesOnProductPage()) {
            return (bool)$this->scopeConfig->getValue('instagrampro/productgroup/product_more', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
        }
    }

    public function shownpInPopup()
    {
        return (int)$this->scopeConfig->getValue('instagrampro/popupgroup/shownp', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getGalleryPageLimit()
    {
        return (int)$this->scopeConfig->getValue('instagrampro/gallerygroup/imagecount', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getGallaryPageTitle()
    {
        return $this->scopeConfig->getValue('instagrampro/gallerygroup/galarytitle', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getProductDetailTitle()
    {
        return $this->scopeConfig->getValue('instagrampro/productgroup/proddetailtitle', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getAccessToken()
    {
        return $this->scopeConfig->getValue('instagrampro/module_options/access_token', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function getFrontendLink()
    {
        if ($this->getSeoFriendlyUrl()) {
            return $this->scopeConfig->getValue('instagrampro/gallerygroup/seorurl', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
        } else {
            return "instagrampro/gallery/instalist";
        }
    }

    public function getSeoFriendlyUrl()
    {
        return $this->scopeConfig->getValue('instagrampro/gallerygroup/seorurl', ScopeInterface::SCOPE_STORE, $this->getCurrentStoreInfo());
    }

    public function buildUrl($url, $params)
    {
        $strParams = [];
        foreach ($params as $key => $value) {
            $strParams[] = $key . '=' . $value;
        }
        $buildedUrl = is_null($url) ? '' : $url . '?';
        return $buildedUrl . implode('&', $strParams);
    }

    public function getAdminConfigSectionUrl()
    {
        $url = $this->_modelUrlFactory->create();
        return $url->getUrl('adminhtml/system_config/edit', [
            '_current' => true,
            'section' => 'instagram'
        ]);
    }

    public function saveGlobalConfigValue($path, $value, $storeID)
    {
        $this->configWriter->save($path, $value, $scope = ScopeInterface::SCOPE_STORES, $storeID);
    }

    public function getUpdateBy()
	{
		return $this->scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE,0);
	}
    public function getHashtag()
	{
		return $this->scopeConfig->getValue('instagrampro/generalgroup/tags', ScopeInterface::SCOPE_STORE,0);
	}
}
