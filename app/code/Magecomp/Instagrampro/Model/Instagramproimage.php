<?php

namespace Magecomp\Instagrampro\Model;

use Magento\Framework\Model\AbstractModel;

class Instagramproimage extends AbstractModel implements InstagramproimageInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'instagramproimage_image_id';

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    protected function _construct()
    {
        $this->_init("Magecomp\Instagrampro\Model\ResourceModel\Instagramproimage");
    }
}
