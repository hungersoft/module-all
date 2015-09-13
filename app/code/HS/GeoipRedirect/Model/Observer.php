<?php

namespace HS\GeoipRedirect\Model;

use Magento\Framework\Controller\ResultFactory;

class Observer
{    
    protected $request;
    
    protected $action;
    
    protected $helper;
    
    protected $allowedCountries;
    
    protected $geoip;
    
    protected $session;
        
    protected $canRedirect;
    
    protected $eventManager;
            
    public function __construct(
        \Magento\Framework\App\Action\Action $action,
        \Magento\Framework\App\RequestInterface $request,
        \HS\GeoipRedirect\Helper\Data $helper,
        \HS\GeoipRedirect\Model\Geoip\Country $geoip,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->action = $action;
        $this->request = $request;
        $this->helper = $helper;
        $this->geoip = $geoip;
        $this->session = $session;
        $this->eventManager = $eventManager;
        
        $this->geoip->addAllowedCountry(
            $this->helper->getAllowedCountries()
        );
    }
    
    public function setCanRedirect($canRedirect) 
    {
        $this->canRedirect = $canRedirect;
        return $this;
    }
    
    public function getCanRedirect() 
    {
        return $this->canRedirect;    
    }
    
    protected function getAllowedCountries() 
    {
        if( ! $this->allowedCountries) {
            $this->allowedCountries = $this->helper->getAllowedCountries();
        }
        
        return $this->allowedCountries;
    }
        
    protected function checkRestrictions() 
    {
        $url = $this->helper->getCurrentUrl();
        $ip = $this->helper->getRemoteAddress();
        $userAgent = $this->helper->getHttpUserAgent();
        
        switch ($this->helper->getApplyRedirectionTo()) {
            case \HS\GeoipRedirect\Helper\Data::REDIRECT_ALL_URL:
                break;
            
            case \HS\GeoipRedirect\Helper\Data::REDIRECT_SPECIFIED_URL:
                if($this->helper->isAllowed(
                    \HS\GeoipRedirect\Helper\Data::RESTRICTION_TYPE_URL, $url
                )) {
                    return false;
                }
                break;
            
            case \HS\GeoipRedirect\Helper\Data::REDIRECT_NOT_SPECIFIED_URL:
                if( ! $this->helper->isAllowed(
                    \HS\GeoipRedirect\Helper\Data::RESTRICTION_TYPE_URL, $url
                )) {
                    return false;
                }
                break;    
        }
        
        if( ! $this->helper->isAllowed(
            \HS\GeoipRedirect\Helper\Data::RESTRICTION_TYPE_IP, $ip
        )) {
            return false;
        }
        
        if( ! $this->helper->isAllowed(
            \HS\GeoipRedirect\Helper\Data::RESTRICTION_TYPE_USER_AGENT, $userAgent
        )) {
            return false;
        }        
        
        return true;
    }
    
    public function geoipRedirect($observer)
    {
        if( ! $this->helper->isEnabled()) {
            return $this;
        }
        
        $controller = $observer->getControllerAction();
        $country = $this->geoip->getCountry();
        
        if($this->session->getIsGeoipRedirected()
            && $this->helper->isRedirectOnce()) {
            return $this;
        }
        
        $this->setCanRedirect(true);
        $this->eventManager->dispatch('hs_geoip_redirect_process_before', ['redirect' => $this]);
        if( ! $this->getCanRedirect()) {
            return $this;
        }
        
        // Check url, ip and user agent restriction.
        if( ! $this->checkRestrictions()) {
            return $this;    
        }
        
        // Check if country to store view enabled and 
        // If country is allowed for then set the store as redirected.
        if ($this->helper->isEnabledCountryToStoreView() 
            && $this->geoip->isCountryAllowed($country)
        ) {
            $this->session->setIsGeoipRedirected(true);
            return;
        }
        
        // Check if country to url enabled and
        // If current country exists in the map, redirect to url.
        if ($this->helper->isEnabledCountryToUrl() 
            && ($url = $this->helper->getCurrentCountryMappedUrl($country))
        ) {
            $this->session->setIsGeoipRedirected(true);
            $controller->getResponse()->setRedirect($url)->sendResponse();
            exit;
        }
        
        // Check if country to currency enabled and
        // If current country exists in the map, redirect to currency url.
        if ($this->helper->isEnabledCountryToCurrency()
            && ($currency = $this->helper->getCurrentCountryMappedCurrency($country))
        ) {
            $this->session->setIsGeoipRedirected(true);
            $this->helper->getStore()->setCurrentCurrencyCode($currency);
            $controller->getResponse()->setRedirect($this->helper->getStore()->getBaseUrl());
            exit;
        }
        
        // Redirect to store for the current country if applicable or redirect to global url.
        if ($this->helper->isEnabledCountryToStoreView()) {
            foreach ($this->helper->getStores() as $store) {
                if( ! $store->getIsActive()) {
                    continue;
                }
                            
                $allowedCountries = $this->helper->getAllowedCountries($store);
                if( ! in_array($country, $allowedCountries)) {
                    continue;
                }
                
                $this->session->setIsGeoipRedirected(true);
                $url = $store->getCurrentUrl();
                $controller->getResponse()->setRedirect($url)->sendResponse();
                exit;
            } 
        }
        
        $url = $this->helper->getGlobalRedirectUrl();
        if( ! $url) {
            return $this;
        }        
        $controller->getResponse()->setRedirect($url)->sendResponse();
        exit;          
    }
}
