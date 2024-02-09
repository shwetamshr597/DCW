<?php

namespace Magecomp\Instagrampro\Model\ResourceModel\Instagramproimage\Grid;

use Magecomp\Instagrampro\Helper\Image;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Search\AggregationInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Collection extends \Magecomp\Instagrampro\Model\ResourceModel\Instagramproimage\Collection implements SearchResultInterface
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_HASHTAG = 0;

    protected $aggregations;

    /**
     * @var Image
     */
    protected $_helperImage;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $curstoreid;
    protected $request;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $configScopeConfigInterface,
        Image $helperImage,
        \Magento\Framework\App\Request\Http $request,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->scopeConfig = $configScopeConfigInterface;
        $this->_helperImage = $helperImage;
        $this->request = $request;
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $urlInterface = $om->get('Magento\Framework\UrlInterface');
        $cstsession = $om->get('Magento\Backend\Model\Session');

            $urlarray = explode('/', $urlInterface->getCurrentUrl());
        if (in_array('store', $urlarray)) {
            $key = array_search('store', $urlarray);
            $storeid = $urlarray[$key + 1];
            $cstsession->setMyStore($storeid);
        } else {
            $cstsession->setMyStore(0);
        }
        $storeid = $cstsession->getMyStore();

        $imgconfig = [];
        $updateType = $this->scopeConfig->getValue('instagrampro/generalgroup/updatetype', ScopeInterface::SCOPE_STORE, $storeid);

        switch ($updateType) {
            case self::UPDATE_TYPE_HASHTAG:
                $imgconfig = $this->_helperImage->getTags($storeid);
                break;
            case self::UPDATE_TYPE_USER:
                $imgconfig = $this->_helperImage->getUsers($storeid);
        }

        $this->addFieldToFilter('is_approved', 1);
        $this->addFieldToFilter('is_visible', 1);
        $this->addFilter('tag', ['in' => $imgconfig], 'public');
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function setItems(array $items = null)
    {
        return $this;
    }
}
