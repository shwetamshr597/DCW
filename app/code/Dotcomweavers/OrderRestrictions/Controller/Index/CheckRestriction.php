<?php
namespace Dotcomweavers\OrderRestrictions\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Dotcomweavers\OrderRestrictions\Model\ResourceModel\Rule\CollectionFactory as RulesCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


class CheckRestriction extends \Magento\Framework\App\Action\Action
{
    protected $_storeManagerInterface;
    protected $rulesCollectionFactory;
    protected $_customerSession;
    protected $quoteFactory;
    protected $scopeConfig;
    protected $_resultJsonFactory;
    protected $resource;
    protected $connection;
    protected $cart;
    protected $addressRepository;
    protected $regionFactory;
    public const XML_PATH_CUSTOMER_FLAG = 'orderrestrictions/general/enable_admin_login_as_customer';

	public function __construct(
		Context $context,
        StoreManagerInterface $storeManagerInterface,
        RulesCollectionFactory $rulesCollectionFactory,
        Session $customerSession,
        QuoteFactory $quoteFactory,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Directory\Model\RegionFactory $regionFactory
	) {
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->rulesCollectionFactory = $rulesCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->cart = $cart;
        $this->addressRepository = $addressRepository;
        $this->regionFactory = $regionFactory;
		parent::__construct($context);
    }
	
	public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(__METHOD__);

        $quoteId = $this->cart->getQuote()->getId();

        $post = $this->getRequest()->getPostValue();

        //print_r($post);

        $ruleResult = '';
        $ruleResultMsg = '';
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
            
            //$logger->info('getShippingAddress ='.print_r($quote->getShippingAddress()->getData(),true));

            if ($this->_customerSession->isLoggedIn()) {
                $address->setData('email',$this->_customerSession->getCustomer()->getEmail());

                if (!empty($post['customer_address_id']) && ($post['customer_address_id'] == 'new-customer-address' || $post['customer_address_id'] == 'undefined')) {
                    $address->setData('firstname',$post['firstname']);
                    $address->setData('lastname',$post['lastname']);
                    $address->setData('street',implode("\r\n",$post['street']));
                    $address->setData('company',$post['company']);
                    $address->setData('country_id',$post['country_id']);
                    $address->setData('region_id',$post['region_id']);
                    $region = $this->regionFactory->create()->load($post['region_id']);
                    $address->setData('region',$region->getName());
                    $address->setData('city',$post['city']);
                    $address->setData('postcode',$post['postcode']);
                    $address->setData('telephone',$post['telephone']);
                
                } elseif (!empty($post['customer_address_id'])) {
                    $customer_address_id = str_replace('customer-address','',$post['customer_address_id']);
                    $customerAddressData = $this->addressRepository->getById($customer_address_id);
    
                    $address->setData('firstname',$customerAddressData->getFirstname());
                    $address->setData('lastname',$customerAddressData->getLastname());
                    $address->setData('street',$customerAddressData->getStreet());
                    $address->setData('company',$customerAddressData->getCompany());
                    $address->setData('country_id',$customerAddressData->getCountryId());
                    $address->setData('region_id',$customerAddressData->getRegionId());
                    $address->setData('region',$customerAddressData->getRegion());
                    $address->setData('city',$customerAddressData->getCity());
                    $address->setData('postcode',$customerAddressData->getPostcode());
                    $address->setData('telephone',$customerAddressData->getTelephone());
                }
            } else {
                $address->setData('email', $post['username']);

                $quote->setData('customer_email', $post['username']);

                $quote->getBillingAddress()->setData('email', $post['username']);
                $quote->getBillingAddress()->setData('firstname', $post['firstname']);
                $quote->getBillingAddress()->setData('lastname', $post['lastname']);

                $address->setData('firstname',$post['firstname']);
                $address->setData('lastname',$post['lastname']);
                $address->setData('street',implode("\r\n",$post['street']));
                $address->setData('company',$post['company']);
                $address->setData('country_id',$post['country_id']);
                $address->setData('region_id',$post['region_id']);
                $region = $this->regionFactory->create()->load($post['region_id']);
                $address->setData('region',$region->getName());
                $address->setData('city',$post['city']);
                $address->setData('postcode',$post['postcode']);
                $address->setData('telephone',$post['telephone']);
            }
            

            $address->setData('total_qty',$quote->getItemsQty());
            //$logger->info('address data ='.print_r($address->getData(),true));
            $logger->info('===================================================================');
            foreach ($rules as $rule) {
                $ruleResult = $rule->getConditions()->validate($address, $items);
                if ($ruleResult == '1') {
                    $ruleResultMsg = $rule->getMessage();
                    break;
                }
            }
        }



        $output = [
            'rule_result' => $ruleResult,
            'message' => $ruleResultMsg,
        ];
    
        $resultJson = $this->_resultJsonFactory->create();
        $result = $resultJson->setData($output);
        
        return $result;
	}


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
