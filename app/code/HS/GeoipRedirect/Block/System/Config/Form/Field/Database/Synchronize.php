<?php

namespace HS\GeoipRedirect\Block\System\Config\Form\Field\Database;

class Synchronize extends \Magento\MediaStorage\Block\System\Config\System\Storage\Media\Synchronize
{
    /**
     * @var string
     */
    protected $_template = 'HS_GeoipRedirect::system/config/form/field/database/synchronize.phtml';

    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxSyncUrl()
    {
        return $this->getUrl('hs_geoip_redirect/system_config_database/synchronize');
    }
    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxStatusUpdateUrl()
    {
        return $this->getUrl('hs_geoip_redirect/system_config_database/status');
    }
    
    /**
     * Generate synchronize button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'synchronize_button',
                'label' => __('Synchronize'),
            ]
        );
    
        return $button->toHtml();
    }
}