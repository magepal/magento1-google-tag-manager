<?php

/**
 * DataLayer
 * Copyright © 2016 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Google Tag Manager Page Block
 */
class MagePal_GoogleTagManager_Block_GtmCode extends Mage_Core_Block_Template
{  

    /**
     * Google Tag Manager data
     *
     * @var MagePal_GoogleTagManager_Helper_Data
     */
    protected $_gtmHelper = null;

    /**
     * Cookie Helper
     *
     * @var Mage_Core_Helper_Cookie
     */
    protected $_cookieHelper = null;

    
    public function __construct() {
        $this->_cookieHelper = Mage::helper('core/cookie');
        $this->_gtmHelper = Mage::helper('googletagmanager');
    }

    /**
     * Get Account Id
     *
     * @return string
     */
    public function getAccountId() {
        return $this->_gtmHelper->getAccountId();
    }

    /**
     * Get Custom Code
     *
     * @return string
     */
    public function getCustomCode() {
        return $this->_gtmHelper->getCustomCode();
    }

    /**
     * Allow to send personal?
     *
     * @return string
     */
    public function sendPersonal() {
        return $this->_gtmHelper->sendPersonal();
    }

    /**
     * Render tag manager JS
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->_cookieHelper->isUserNotAllowSaveCookie() || !$this->_gtmHelper->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

}
