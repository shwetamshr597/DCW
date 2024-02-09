<?php

namespace Magecomp\Instagrampro\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Editimage extends Column
{
    const ROW_EDIT_URL = 'instagrampro/proindex/edit';
    protected $_urlBuilder;
    private $_editUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['image_id'])) {
                    $Storeid = $this->getCurrentStoreID();
                    if ($Storeid != '' && $Storeid > 0) {
                        $this->_editUrl = $this->_editUrl . "/store/" . $Storeid;
                    }
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_editUrl,
                            ['id' => $item['image_id']]
                        ),
                        'label' => __('Edit'),
                    ];
                }
            }
        }

        return $dataSource;
    }

    public function getCurrentStoreID()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $urlInterface = $om->get('Magento\Framework\UrlInterface');
        $cstsession = $om->get('Magento\Backend\Model\Session');

        if (strpos($urlInterface->getCurrentUrl(), 'store') !== false) {
            $urlarray = explode('/', $urlInterface->getCurrentUrl());
            if (in_array('store', $urlarray)) {
                $key = array_search('store', $urlarray);
                $Storeid = $urlarray[$key + 1];
                $cstsession->setMyStore($Storeid);
            }
        } else {
            $cstsession->setMyStore(0);
        }

        return $cstsession->getMyStore();
    }
}
