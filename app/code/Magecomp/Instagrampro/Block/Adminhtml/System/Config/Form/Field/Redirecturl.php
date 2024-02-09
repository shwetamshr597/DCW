<?php

namespace Magecomp\Instagrampro\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Redirecturl extends Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magecomp_Instagrampro::instagrampro/config/form/field/redirecturl.phtml');
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
