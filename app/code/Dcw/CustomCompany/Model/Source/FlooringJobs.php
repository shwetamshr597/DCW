<?php
namespace Dcw\CustomCompany\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FlooringJobs implements OptionSourceInterface
{

    /**
     * Return Option.
     *
     * @return Array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Please select an item')],
            ['value' => 1, 'label' => __('1-3')],
            ['value' => 2, 'label' => __('4-10')],
            ['value' => 3, 'label' => __('10-25')],
            ['value' => 4, 'label' => __('25+')],
            // Add more options as needed
        ];
    }
    
     /**
      * Option key and value.
      *
      * @return Array[]
      */
    public function optionvalueinkey()
    {
        $alloption=$this->toOptionArray();
        $valuesinkey=[];
        foreach ($alloption as $option) {
            if ($option['value']!='') {
                $valuesinkey[$option['value']]=$option['label']->__toString();
            }
        }
        return $valuesinkey;
    }
}
