<?php

namespace Magecomp\Instagrampro\Model\Source;

class Popuptype
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Title with Links')],
            ['value' => 1, 'label' => __('Product Blocks')],
        ];
    }
}
