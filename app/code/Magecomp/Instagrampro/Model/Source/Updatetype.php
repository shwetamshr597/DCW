<?php

namespace Magecomp\Instagrampro\Model\Source;

class Updatetype
{

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Users')],
            ['value' => 0, 'label' => __('Hashtags')],
        ];
    }
}
