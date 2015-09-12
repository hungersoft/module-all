<?php

namespace HS\GeoipRedirect\Model\Geoip;

class Country
{
    private $country;
    
    private $allowedCountries = array();
    
    /**
     * @var \HS\GeoipRedirect\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \HS\GeoipRedirect\Model\Geoip\Wrapper
     */
    protected $geoip;
    
    protected $database;
    
    /**
     * @param \HS\GeoipRedirect\Helper\Data $helper
     * @param \HS\GeoipRedirect\Model\Geoip\Wrapper $geoip
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \HS\GeoipRedirect\Helper\Geoip $helper,        
        \HS\GeoipRedirect\Model\Geoip\Wrapper $geoip,
        \HS\GeoipRedirect\Model\Geoip\Database $database
    ) {        
        $this->helper = $helper;
        $this->geoip = $geoip;
        $this->database = $database;
        
        $this->country = $this->getCountryByIp($this->helper->getRemoteAddress());
    }
    
    /**
     * Get country by the ip provided.
     *
     * @param string
     * @return string
     */
    public function getCountryByIp($ip)
    {
        if ( ! $this->geoip->open($this->database->getFilePath(), 0)) {
            return null;
        }
        
        $country = $this->geoip->getCountryCodeByAddr($ip);
        $this->geoip->close();        
        
        return $country;
    }
    
    /**
     * Get currently detected country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * Check if country is in the allowed list of countries.
     *
     * @param string
     * @return boolean
     */
    public function isCountryAllowed($country = '')
    {
        $country = $country ? $country : $this->country;
        if (count($this->allowedCountries) && $country) {
            return in_array($country, $this->allowedCountries);
        }
        
        return true;
    }
    
    /**
     * Check if country is the default country.
     *
     * @param string
     * @return boolean
     */
    public function isDefaultCountry($country = '')
    {
        $country = $country ? $country : $this->country;
        if ( ! empty($this->defaultCountry) && $country) {
            return ($this->defaultCountry == $country);
        }
        
        return false;
    }
    
    /**
     * Add countrie(s) to the allowed list.
     *
     * @param string | array
     * @return HS\GeoipRedirect\Model\Geoip\Country
     */
    public function addAllowedCountry($countries)
    {
        $countries = is_array($countries) ? $countries : array($countries);
        $this->allowedCountries = array_merge($this->allowedCountries, $countries);
        return $this;
    }
}