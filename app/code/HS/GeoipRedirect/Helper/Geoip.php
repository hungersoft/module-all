<?php

namespace HS\GeoipRedirect\Helper;

class Geoip extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $store;
    
    protected $httpHeader;
    
    protected $remoteAddress;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Registry $coreRegistry
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->store = $store;
        $this->httpHeader = $httpHeader;
        $this->remoteAddress = $remoteAddress;
        
        parent::__construct($context);
    }

    /**
     * Retrieve current visitor IP.
     *
     * @return string
     */
    public function getRemoteAddress() 
    {
        return '122.172.184.227'; //$this->remoteAddress->getRemoteAddress(false);
    }    
}