<?php

namespace HS\GeoipRedirect\Controller\Adminhtml\System\Config\Database;

class Synchronize extends \Magento\MediaStorage\Controller\Adminhtml\System\Config\System\Storage
{
    protected $_database;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \HS\GeoipRedirect\Model\Geoip\Database $database) 
    {
        parent::__construct($context);
        $this->_database = $database;
    }

    /**
     * Synchronize geoip database
     *
     * @return void
     */
    public function execute()
    {
        $this->_database->update();
    }
}
