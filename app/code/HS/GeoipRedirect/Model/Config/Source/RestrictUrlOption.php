<?php

namespace HS\GeoipRedirect\Model\Config\Source;

class RestrictUrlOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \HS\GeoipRedirect\Helper\Data::REDIRECT_ALL_URL, 'label' => __('All URLs')], 
            ['value' => \HS\GeoipRedirect\Helper\Data::REDIRECT_SPECIFIED_URL, 'label' => __('Specified URLs')],
            ['value' => \HS\GeoipRedirect\Helper\Data::REDIRECT_NOT_SPECIFIED_URL, 'label' => __('Not Specified URLs')],
        ];
    }
}
