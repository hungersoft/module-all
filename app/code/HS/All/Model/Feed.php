<?php

namespace HS\All\Model;

use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Class Feed
 *
 * @package HS\All\Model\Feed
 */
class Feed extends \Magento\AdminNotification\Model\Feed
{
    /**
     * Notifications feed URL.
     */
    const HS_FEED_URL = 'https://hungersoft.com/notifications_feed';

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if ($this->_feedUrl === null) {
            $this->_feedUrl = self::HS_FEED_URL;
        }

        return $this->_feedUrl;
    }
}
