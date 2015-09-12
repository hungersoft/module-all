<?php

namespace HS\GeoipRedirect\Block\System\Config\Form\Field\Renderer;

class Country extends \Magento\Framework\View\Element\AbstractBlock
{
    protected $_model = null;
        
    public function __construct() 
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_model = $objectManager->get('\Magento\Config\Model\Config\Source\Locale\Country');
    }
    
    public function getCountries() 
    {
        return $this->_model->toOptionArray();
    }

    public function toHtml() 
    {        
        $options = $this->_model->toOptionArray();
        $html = sprintf('<select name="%s">', $this->getInputName());
        foreach ($options as $option) {
            $html .= sprintf('<option value="%s">%s</option>', $option['value'], $option['label']);
        }
        $html .= '</select>';
        
        return $html;
    }
}