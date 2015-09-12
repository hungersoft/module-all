<?php

namespace HS\GeoipRedirect\Controller\Adminhtml\System\Config\Database;

class Status extends \Magento\MediaStorage\Controller\Adminhtml\System\Config\System\Storage
{
    /**
     * @var \HS\GeoipRedirect\Model\Geoip\Database
     */
    protected $database;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
        

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \HS\GeoipRedirect\Model\Geoip\Database $database
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \HS\GeoipRedirect\Model\Geoip\Database $database) 
    {
        parent::__construct($context);
        $this->database = $database;
    }
    
    protected function _getSession() 
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session');    
    }

    /**
     * Retrieve synchronize process state and it's parameters in json format
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $_realSize = filesize($this->database->getLocalArchivePath());
        $_totalSize = $this->_getSession()->getGeoipDbFileSize();
        echo $_totalSize ? $_realSize / $_totalSize * 100 : 0;
    }        
}
