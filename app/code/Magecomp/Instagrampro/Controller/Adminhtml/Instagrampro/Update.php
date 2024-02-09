<?php
namespace Magecomp\Instagrampro\Controller\Adminhtml\Instagrampro;

use Magecomp\Instagrampro\Helper\Image\User;
use Magecomp\Instagrampro\Helper\Image\Hashtag;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Update extends \Magento\Backend\App\Action
{
    const UPDATE_TYPE_USER  = 1;
    const UPDATE_TYPE_HASHTAG   = 0;

    protected $_configScopeConfigInterface;
    protected $_helperImage;
    protected $_imageUser;
    protected $_imageHashtag;


    public function __construct(
        Context $context,
        ScopeConfigInterface $configScopeConfigInterface,
        User $imageUser,
        Hashtag $imageHashtag
    ) {
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_imageUser = $imageUser;
        $this->_imageHashtag = $imageHashtag;
        parent::__construct($context);
    }

    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId == "" && $storeId == null) {
            $storeId = 0;
        }
        $updateType = $this->_configScopeConfigInterface->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $storeId);
        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                $this->_imageHashtag->update($storeId);
                break;

            case self::UPDATE_TYPE_USER:
                $this->_imageUser->update($storeId);
                break;
        }
        $this->_redirect('instagrampro/instagrampro/new', ['store' => $storeId]);
    }
}
