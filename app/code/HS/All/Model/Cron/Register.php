<?php

namespace HS\All\Model\Cron;

class Register
{
    /**
     * @var \HS\All\Helper\Data
     */
    protected $helper;


    /**
     * @param \HS\All\Helper\Data $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \HS\All\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Register extension.
     */
    public function register()
    {
        $this->helper->register('HS_All', '1.0.0', 'confirm');
    }
}
