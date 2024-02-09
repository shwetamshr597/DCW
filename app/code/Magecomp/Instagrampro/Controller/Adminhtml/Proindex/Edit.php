<?php

namespace Magecomp\Instagrampro\Controller\Adminhtml\Proindex;

use Magecomp\Instagrampro\Model\InstagramproimageFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends AbstractProindex
{
    protected $_modelInstagramproimageFactory;
    protected $_coreRegistry = null;
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        InstagramproimageFactory $modelInstagramproimageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_modelInstagramproimageFactory = $modelInstagramproimageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_modelInstagramproimageFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This image no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }


        $this->_coreRegistry->register('instagrampro_data', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Image') : __('New Image'),
            $id ? __('Edit Image') : __('New Image')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Image'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getOrderStatus() : __('New Image'));

        return $resultPage;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magecomp_Instagrampro::instagrampro')
            ->addBreadcrumb(__('Instagrampro'), __('Instagrampro'))
            ->addBreadcrumb(__('Manage Image'), __('Manage Image'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magecomp_Instagrampro::instagrampro');
    }
}
