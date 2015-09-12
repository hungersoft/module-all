<?php

namespace HS\GeoipRedirect\Model\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class CountryUrlMap extends ArraySerialized
{
    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;

    protected $messageManager;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_design = $design;
        $this->messageManager = $messageManager;
        
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Validate value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * if there is no field value, search value is empty or regular expression is not valid
     */
    public function beforeSave()
    {
        // For value validations
        $exceptions = $this->getValue();
        foreach ($exceptions as $rowKey => $row) {
            if ($rowKey === '__empty') {
                continue;
            }

            // Validate that all values have come
            foreach (['country', 'url'] as $fieldName) {
                if ( ! isset($row[$fieldName])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Exception does not contain field \'%1\'', $fieldName)
                    );
                }
            }
            
            $url = filter_var($row['url'], FILTER_SANITIZE_URL);
            if ( ! filter_var($url, FILTER_VALIDATE_URL)) {
                $this->messageManager->addError(__(
                    '<strong>%1</strong> is not a valid url. The url should have a scheme(http://, https://,...) and host(www.example.com, example.com...). Additionally, path and query can be added but are not mandatory.', $url
                ));
                unset($exceptions[$rowKey]);
                continue;
            }

            $exceptions[$rowKey]['country'] = $row['country'];
            $exceptions[$rowKey]['url'] = $row['url'];
        }
        $this->setValue($exceptions);

        return parent::beforeSave();
    }
}
