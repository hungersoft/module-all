<?php

namespace HS\GeoipRedirect\Block\System\Config\Form\Field\Database;

class Status extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_database;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \HS\GeoipRedirect\Model\Geoip\Database $database) 
    {
        $this->_database = $database;
        parent::__construct($context);
    }

    /**
     * Remove scope label
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    
    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<div id="sync_update_date">%s</div>';
        if ($date = $this->_database->getLastUpdateDate()) {
            return sprintf($html, (new \DateTime())->setTimestamp($date)->format('F dS, Y h:i:sA T'));
        }
        return sprintf($html, '-');
    }
}