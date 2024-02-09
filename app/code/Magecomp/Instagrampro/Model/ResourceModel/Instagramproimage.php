<?php

namespace Magecomp\Instagrampro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Instagramproimage extends AbstractDb
{
    protected $_isPkAutoIncrement = false;
    
    protected function _construct()
    {
        $this->_init("instagrampro_image", "image_id");
    }
}
