<?php
/**
 * OrderRestrictions Plugin.
 * @category  Dotcomweavers
 * @package   Dotcomweavers_OrderRestrictions
 * @author    Dotcomweavers
 * @copyright Copyright (c) Dotcomweavers
 */

namespace Dotcomweavers\OrderRestrictions\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Dotcomweavers\OrderRestrictions\Model\ResourceModel\Rule\CollectionFactory as RulesCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class PlaceOrderValidationCheck for check rules
 */
class OrderManagement
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var RulesCollectionFactory
     */
    private $rulesCollectionFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Enable admin login as customer flag config path
     */
    public const XML_PATH_CUSTOMER_FLAG = 'orderrestrictions/general/enable_admin_login_as_customer';

    /**
     * @param StoreManagerInterface $storeManagerInterface
     * @param RulesCollectionFactory $rulesCollectionFactory
     * @param Session $customerSession
     * @param QuoteFactory $quoteFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        RulesCollectionFactory $rulesCollectionFactory,
        Session $customerSession,
        QuoteFactory $quoteFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->rulesCollectionFactory = $rulesCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Plugin before order place
     *
     * @param OrderManagementInterface $subject
     * @param OrderInterface           $order
     *
     * @return OrderInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePlace(
        OrderManagementInterface $subject,
        OrderInterface $order
    ): array {

        $quoteId = $order->getQuoteId();
        if ($quoteId) {

            if ($this->_customerSession->isLoggedIn()):
                $cdata = $this->_customerSession->getData();
                $customerFlag = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_FLAG);
                if (isset($cdata['logged_as_customer_admind_id']) && $customerFlag) {
                    return [$order];
                }
            endif;

            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllItems();

            $rules = $this->getRules();

            $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            $address->setData('total_qty',$quote->getItemsQty());
            foreach ($rules as $rule) {
                $ruleResult = $rule->getConditions()->validate($address, $items);
                if ($ruleResult == '1') {
                    throw new LocalizedException(__($rule->getMessage()));
                }
            }
        }
        return [$order];
    }

    /**
     * For get matching rules
     *
     * @return mixed
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Log_Exception
     */
    public function getRules()
    {
        $currentStore = $this->_storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();

        $rulesCollection = $this->rulesCollectionFactory->create();
        $rulesCollection->addFieldToFilter('stores', [["finset"=>'0'], ["finset"=>$currentStoreId]]);
        $rulesCollection->addFieldToFilter('customer_groups', ["finset"=>explode(",", $this->getGroupId())]);
        $rulesCollection->addFieldToFilter('enabled', 1);

        return $rulesCollection;
    }

    /**
     * For get customer group id
     *
     * @return int
     */
    public function getGroupId()
    {
        if ($this->_customerSession->isLoggedIn()):
            return $this->_customerSession->getCustomer()->getGroupId();
        endif;

        return 0;
    }
}
