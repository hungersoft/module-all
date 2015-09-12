<?php

namespace HS\GeoipRedirect\Block\System\Config\Form\Field;

/**
 * Backend system config array field renderer
 */
class Regexceptions extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
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
        $this->addColumn('search', ['label' => __('Value')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add \Exception');
        parent::_construct();
    }
}
