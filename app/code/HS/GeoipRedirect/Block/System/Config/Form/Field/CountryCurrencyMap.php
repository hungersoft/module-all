<?php

namespace HS\GeoipRedirect\Block\System\Config\Form\Field;

/**
 * Backend system config array field renderer
 */
class CountryCurrencyMap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;
    
    /**
     * @var string
     */
    protected $_template = 'HS_GeoipRedirect::system/config/form/field/select-array.phtml';
    

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_labelFactory = $labelFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('country', [
            'label' => __('Country'),
            'renderer' => $this->_layout->getBlockSingleton('HS\GeoipRedirect\Block\System\Config\Form\Field\Renderer\Country'),
        ]);
        $this->addColumn('currency', [
            'label' => __('Currency'),
            'renderer' => $this->_layout->getBlockSingleton('HS\GeoipRedirect\Block\System\Config\Form\Field\Renderer\Currency'),
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }
}
