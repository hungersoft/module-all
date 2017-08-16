<?php

namespace HS\All\Observer;

use Magento\Framework\Event\ObserverInterface;

class Feed implements ObserverInterface
{
    /**
     * @var \Magento\AdminNotification\Model\FeedFactory
     */
    private $feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendAuthSession;

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
        $this->feedFactory = $feedFactory;
        $this->backendAuthSession = $backendAuthSession;
    }

    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->backendAuthSession->isLoggedIn()) {
            $feedModel = $this->feedFactory->create();
            /** @var $feedModel \HS\All\Model\Feed **/
            $feedModel->checkUpdate();
        }
    }
}
