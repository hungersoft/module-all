<?php

namespace HS\GeoipRedirect\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_ENABLED = 'geoip_redirect/general/enabled';
    const CONFIG_APPLY_REDIRECT_TO = 'geoip_redirect/restriction/apply_to';
    const CONFIG_REDIRECT_ACTION_URL = 'geoip_redirect/restriction/url';
    const CONFIG_IGNORE_USER_AGENTS = 'geoip_redirect/restriction/ignore_user_agent';
    const CONFIG_IGNORE_IP = 'geoip_redirect/restriction/ignore_ip';
    const CONFIG_ENABLE_COUNTRY_TO_STORE_VIEW = 'geoip_redirect/store_view_redirect/enabled';
    const CONFIG_STORE_VIEW_COUNTRY = 'geoip_redirect/store_view_redirect/country';    
    const CONFIG_ENABLE_COUNTRY_TO_URL = 'geoip_redirect/url_redirect/enabled';
    const CONFIG_COUNTRY_URL_MAP = 'geoip_redirect/url_redirect/country_url_map';
    const CONFIG_ENABLE_COUNTRY_TO_CURRENCY = 'geoip_redirect/currency_switch/enabled';
    const CONFIG_COUNTRY_CURRENCY_MAP = 'geoip_redirect/currency_switch/country_currency_map';
    const CONFIG_GLOBAL_REDIRECT_URL = 'geoip_redirect/global_redirect/url';
    
    const REDIRECT_ALL_URL = 'all_urls';
    const REDIRECT_SPECIFIED_URL = 'specified_urls';
    const REDIRECT_NOT_SPECIFIED_URL = 'not_specified_urls';
    
    const RESTRICTION_TYPE_IP = 'ip';
    const RESTRICTION_TYPE_URL = 'url';
    const RESTRICTION_TYPE_USER_AGENT = 'user_agent';

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId;
        
    protected $store;
    
    protected $httpHeader;
    
    protected $geoipHelper;
    
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Registry $coreRegistry
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Header $httpHeader,
        Geoip $geoipHelper
    ) {
        $this->storeManager = $storeManager;
        $this->httpHeader = $httpHeader;
        $this->geoipHelper = $geoipHelper;
        
        parent::__construct($context);
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }
    
    public function getStore($id = null) 
    {
        return $this->storeManager->getStore($id);    
    }
    
    public function getStores() 
    {
        return $this->storeManager->getStores();    
    }
    
    /**
     * Retrieve current visitor IP.
     *
     * @return string
     */
    public function getRemoteAddress() 
    {
        return $this->geoipHelper->getRemoteAddress();
    }
    
    /**
     * Retrieve current url.
     *
     * @return string
     */
    public function getCurrentUrl() 
    {
        return $this->getStore()->getCurrentUrl(true);
    }

    /**
     * Retrieve current User Agent.
     *
     * @return string
     */
    public function getHttpUserAgent() 
    {
        return $this->httpHeader->getHttpUserAgent();
    }
        
    /**
     * Retrieve url restriction type.
     *
     * @return string
     */
    public function getApplyRedirectionTo() 
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_APPLY_REDIRECT_TO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );   
    }
    
    /**
     * Retrieve urls to allow or block from redirecting.
     *
     * @return string
     */
    public function getRedirectActionUrl() 
    {
        $serializedData = $this->scopeConfig->getValue(
            self::CONFIG_REDIRECT_ACTION_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $urls = unserialize($serializedData);
        if( ! $serializedData || empty($urls)) {
            return false;
        }
        
        return $urls;
    }    
    
    /**
     * Retrieve user agents to ignore.
     *
     * @return string
     */
    public function getIgnoreUserAgent() 
    {
        $serializedData = $this->scopeConfig->getValue(
            self::CONFIG_IGNORE_USER_AGENTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $userAgents = unserialize($serializedData);
        if( ! $serializedData || empty($userAgents)) {
            return false;
        }
        
        return $userAgents;
    }
    
    /**
     * Retrieve user agents to ignore.
     *
     * @return string
     */
    public function getIgnoreIp() 
    {
        $serializedData = $this->scopeConfig->getValue(
            self::CONFIG_IGNORE_IP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $ips = unserialize($serializedData);
        if( ! $serializedData || empty($ips)) {
            return false;
        }
        
        return $ips;
    }
    
    /**
     * Retrieve allowed countries.
     *
     * @return string
     */
    public function getAllowedCountries($store = null) 
    {
        $countries =  $this->scopeConfig->getValue(
            self::CONFIG_STORE_VIEW_COUNTRY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );  
        
        $countries = explode(',', $countries);
        if( ! is_array($countries)) {
            return array(trim($countries));
        }
        
        return array_map('trim', $countries);
    }
    
    /**
     * Retrieve country to url map.
     *
     * @return string
     */
    public function getCountryUrlMap() 
    {
        $serializedData = $this->scopeConfig->getValue(
            self::CONFIG_COUNTRY_URL_MAP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $mappedData = unserialize($serializedData);
        if( ! $serializedData || empty($mappedData)) {
            return false;
        }
        
        return $mappedData;
    }
    
    public function getCurrentCountryMappedUrl($country) 
    {
        $map = $this->getCountryUrlMap();
        $url = $this->searchMap($map, 'country', $country, 'url');
        if( ! $url) {
            return false;
        }
        
        return $url;
    }
    
    public function searchMap($map, $searchKey, $val, $returnKey) 
    {
        foreach ($map as $key => $row) {
            if($row[$searchKey] != $val) {
                continue;
            }
            
            return $row[$returnKey];
        }
    }
    
    /**
     * Retrieve country to url map.
     *
     * @return string
     */
    public function getCountryCurrencyMap() 
    {
        $serializedData = $this->scopeConfig->getValue(
            self::CONFIG_COUNTRY_CURRENCY_MAP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $mappedData = unserialize($serializedData);
        if( ! $serializedData || empty($mappedData)) {
            return false;
        }
        
        return $mappedData;
    }
    
    public function getCurrentCountryMappedCurrency($country) 
    {
        $map = $this->getCountryCurrencyMap();
        $currency = $this->searchMap($map, 'country', $country, 'currency');
        if( ! $currency) {
            return false;
        }
        
        return $currency;
    }
    
    public function getGlobalRedirectUrl() 
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_GLOBAL_REDIRECT_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the store is configured to redirect using GeoIP.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }
    
    /**
     * Check if the store is configured to redirect to store view.
     *
     * @return bool
     */
    public function isEnabledCountryToStoreView()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLE_COUNTRY_TO_STORE_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }
    
    /**
     * Check if the store is configured to redirect to url.
     *
     * @return bool
     */
    public function isEnabledCountryToUrl()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLE_COUNTRY_TO_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }
    
    /**
     * Check if the store is configured to switch currency.
     *
     * @return bool
     */
    public function isEnabledCountryToCurrency()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_ENABLE_COUNTRY_TO_CURRENCY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }
    
    /**
     * Check if the restriction passed.
     *
     * @param string
     * @param string
     *
     * @return bool
     */
    public function isAllowed($type, $value) 
    {
        $functionName = '';
        switch ($type) {
            case self::RESTRICTION_TYPE_IP:
                $functionName = 'getIgnoreIp';
                break;
            
            case self::RESTRICTION_TYPE_URL:
                $functionName = 'getRedirectActionUrl';
                break;
            
            case self::RESTRICTION_TYPE_USER_AGENT:
                $functionName = 'getIgnoreUserAgent';
                break;
            
            default:
                return false;
        }
        
        $values = $this->$functionName();
        if( ! $values) {
            return true;
        }
        
        return ! $this->in_array_r($value, $values);
    }  
    
    public function in_array_r($needle, $haystack, $strict = false) 
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) 
                || (is_array($item) && $this->in_array_r($needle, $item, $strict))
            ) {
                return true;
            }
        }
    
        return false;
    }      
}