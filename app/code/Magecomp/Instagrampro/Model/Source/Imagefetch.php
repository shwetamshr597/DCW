<?php

namespace Magecomp\Instagrampro\Model\Source;

class Imagefetch
{
    public function toOptionArray()
    {
        return [
            ['value' => 5, 'label' => __('5')],
            ['value' => 10, 'label' => __('10')],
            ['value' => 20, 'label' => __('20')],
            ['value' => 30, 'label' => __('30')],
            ['value' => 40, 'label' => __('40')],
            ['value' => 50, 'label' => __('50')],

        ];
    }
}
