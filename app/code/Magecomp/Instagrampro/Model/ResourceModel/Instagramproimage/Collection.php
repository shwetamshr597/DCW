<?php
namespace Magecomp\Instagrampro\Model\ResourceModel\Instagramproimage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'image_id';
    public function _construct()
    {
        $this->_init("Magecomp\Instagrampro\Model\Instagramproimage", "Magecomp\Instagrampro\Model\ResourceModel\Instagramproimage");
    }
}
