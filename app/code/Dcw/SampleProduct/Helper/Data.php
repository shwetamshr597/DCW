<?php
namespace Dcw\SampleProduct\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
      * @var \Magento\Framework\View\LayoutFactory
      */
    protected $layoutFactory;
    
    /**
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
 
    public function __construct(\Magento\Framework\View\LayoutFactory $layoutFactory)
    {
        $this->layoutFactory = $layoutFactory;
    }
    
     /**
      * Send Media url
      *
      * @param integer $productid for simple product html
      * @return html
      */
    public function getTemplate($productid)
    {
        $layout = $this->layoutFactory->create();
        $blockOption = $layout->createBlock(\Dcw\SampleProduct\Block\SampleData::class)
        ->setProductId($productid)->setTemplate("Dcw_SampleProduct::productdetails.phtml")
        ->setData('cacheable', false);
        return $blockOption->toHtml();
    }
}
