<?php

namespace HS\All\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EXTENSION_REGISTER_URL = 'https://hungersoft.com/register-module.php';

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Store\Model\Store $store
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Store\Model\Store $store
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Install and register module.
     *
     * @param $module
     * @param $version
     */
    public function register($module, $version, $type = 'install')
    {
        if(null === $module || null === $version) {
            throw new Exception('Invalid Module.');
        }

        $curl = new \Magento\Framework\HTTP\Client\Curl();
        $curl->get(self::EXTENSION_REGISTER_URL, array(
            'module' => $module,
            'version' => $version,
            //'site_url' => $this->getAllUrls(),
            'type' => $type,
        ));
    }

    /**
     * Get urls of all the stores in the magento install.
     *
     * @return array
     */
    public function getAllUrls()
    {
        $urls = [];
        $stores = $this->storeManager->getStores(true);
        foreach($stores as $store) {
            $urls[] = $store->getUrl('');
        }

        return $urls;
    }
}