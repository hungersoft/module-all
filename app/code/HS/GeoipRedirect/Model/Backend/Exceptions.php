<?php

namespace HS\GeoipRedirect\Model\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Exceptions extends ArraySerialized
{
    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;

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
        array $data = []
    ) {
        $this->_design = $design;
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
            foreach (['search'] as $fieldName) {
                if ( ! isset($row[$fieldName])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Exception does not contain field \'%1\'', $fieldName)
                    );
                }
            }

            // Empty string (match all) is not supported, because it means setting a default theme. Remove such entries.
            if ( ! strlen($row['search'])) {
                unset($exceptions[$rowKey]);
                continue;
            }

            // Compose regular exception pattern
            $exceptions[$rowKey]['regexp'] = $row['search'];
        }
        $this->setValue($exceptions);

        return parent::beforeSave();
    }
}
