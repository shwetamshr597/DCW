<?php
namespace Dcw\Notifications\Cron;

class AvailabilityNotificationsSet
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface  $configWriter
    ) {
        
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->timezoneInterface = $timezoneInterface;
        $this->storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_configWriter = $configWriter;
    }
    
    public function execute()
    {
        try {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/AvailabilityNotificationsCron.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(__METHOD__);
            
            //$current_datetime = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
            //$current_date = $this->timezoneInterface->date()->format('Y-m-d');
            $current_hour = $this->timezoneInterface->date()->format('G');
            $current_day_numeric = $this->timezoneInterface->date()->format('w');
            
            $business_hour_start = $this->_scopeConfig->getValue(
                'dcw_notifications_config/notification/business_hour_start',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        
            $business_hour_end = $this->_scopeConfig->getValue(
                'dcw_notifications_config/notification/business_hour_end',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        
            $days_off = $this->_scopeConfig->getValue(
                'dcw_notifications_config/notification/days_off',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        
            if (!empty($days_off)) {
                $days_off_arr = explode(",", $days_off);
            } else {
                $days_off_arr = [];
            }
            
            $logger->info('current_hour='.$current_hour.
              ' | current_day_numeric='.$current_day_numeric.
              ' | business_hour_start='.$business_hour_start.
              ' | business_hour_end='.$business_hour_end.
              ' | days_off_arr='.json_encode($days_off_arr));
            
            $path ='dcw_notifications_config/notification/availability';
            if (($current_hour >= $business_hour_start &&
            $current_hour <= $business_hour_end) &&
            !in_array($current_day_numeric, $days_off_arr)) {
                $logger->info('Value set YES');
                $this->_configWriter->save($path, 1, $scope = $this->_scopeConfig::SCOPE_TYPE_DEFAULT, $scopeId = 0);
            } else {
                $logger->info('Value set No');
                $this->_configWriter->save($path, 0, $scope = $this->_scopeConfig::SCOPE_TYPE_DEFAULT, $scopeId = 0);
            }
            
        } catch (Exception $e) {
            $logger->info($e->getMessage());
        }
    
        return $this;
    }
}
