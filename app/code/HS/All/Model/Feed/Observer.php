<?php
namespace HS\All\Model\Feed;

/**
 * Class Observer
 *
 * @package HS\All\Model
 */
class Observer
{
    /**
     * @var \Magento\AdminNotification\Model\FeedFactory
     */
    protected $_feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Observer constructor.
     *
     * @param \HS\All\Model\FeedFactory           $feedFactory
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \HS\All\Model\FeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function preDispatch(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->_feedFactory->create();
            /* @var $feedModel \HS\All\Model\Feed */
            $feedModel->checkUpdate();
        }
    }
}
