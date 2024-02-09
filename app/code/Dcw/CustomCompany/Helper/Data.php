<?php
namespace Dcw\CustomCompany\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Dcw\CustomCompany\Model\Source\FlooringJobs
     */
    protected $flooringJobs;
    
    /**
     * @var \Dcw\CustomCompany\Model\Source\BusinessOptions
     */
    protected $businessOptions;
    
    /**
     * @param \Dcw\CustomCompany\Model\Source\BusinessOptions $businessOptions
     * @param \Dcw\CustomCompany\Model\Source\FlooringJobs $flooringJobs
     */
    public function __construct(
        \Dcw\CustomCompany\Model\Source\BusinessOptions $businessOptions,
        \Dcw\CustomCompany\Model\Source\FlooringJobs $flooringJobs
    ) {
        $this->businessOptions = $businessOptions;
        $this->flooringJobs = $flooringJobs;
    }
    
     /**
      * Get Role Data.
      *
      * @return DataObject[]
      */
    public function businessoption()
    {
        return $this->businessOptions->toOptionArray();
    }
    
     /**
      * Get Role Data.
      *
      * @return DataObject[]
      */
    public function flooringJobsoption()
    {
         
        return $this->flooringJobs->toOptionArray();
    }
}
