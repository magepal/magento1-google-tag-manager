<?php
/**
 * DataLayer
 * Copyright © 2016 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */
class MagePal_GoogleTagManager_Block_Tm extends Mage_Core_Block_Template
{

    /**
     * Google Tag Manager Helper
     *
     * @var MagePal_TagManager_Helper_Data
     */
    protected $_gtmHelper = null;

    /**
     * Cookie Helper
     *
     * @var Mage_Core_Helper_Cookie
     */
    protected $_cookieHelper = null;

    /**
     * Cookie Helper
     *
     * @var MagePal_TagManager_Model_DataLayer
     */
    protected $_dataLayerModel = null;


    protected $_customVariables = array();

    protected $_orderCollection = null;


    public function __construct() {

        $this->_cookieHelper = Mage::helper('core/cookie');
        $this->_gtmHelper = Mage::helper('googletagmanager');
        $this->_dataLayerModel = Mage::getModel('googletagmanager/dataLayer');

        $this->addVariable('ecommerce', array('currencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode()));
    }

    /**
     * Render information about specified orders and their items
     *
     * @return void|string
     */
    protected function getOrdersTrackingCode()
    {
        $collection = $this->getOrderCollection();

        if(!$collection){
            return;
        }

        $result = array();

        foreach ($collection as $order) {

            foreach ($order->getAllVisibleItems() as $item) {
                $product[] = array(
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $item->getBasePrice(),
                    'quantity' => $item->getQtyOrdered()
                );
            }

            $transaction = array(
                'transactionId' => $order->getIncrementId(),
                'transactionAffiliation' => Mage::app()->getStore()->getFrontendName(),
                'transactionTotal' => $order->getBaseGrandTotal(),
                'transactionTax' => $order->getBaseTaxAmount(),
                'transactionShipping' => $order->getBaseShippingAmount(),
                'discountCode' => $order->getCouponCode(),
                'discountPrice' => $order->getDiscountAmount(),
                'transactionProducts' => $product
            );


            $result[] = sprintf("dataLayer.push(%s);", json_encode($transaction, JSON_NUMERIC_CHECK));
        }

        return implode("\n", $result) . "\n";
    }

    /**
     * Render tag manager script
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->_cookieHelper->isUserNotAllowSaveCookie() || !$this->_gtmHelper->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Return data layer json
     *
     * @return json
     */
    public function getGtmTrackingCode() {

        Mage::dispatchEvent('magepal_data_layer', array('data_layer' => $this));

        $result = array();
        $result[] = sprintf("dataLayer.push(%s);\n", json_encode($this->_dataLayerModel->getVariables()));

        if(!empty($this->_customVariables) && is_array($this->_customVariables)){

            foreach($this->_customVariables as $custom){
                $result[] = sprintf("dataLayer.push(%s);\n", json_encode($custom));
            }
        }

        return implode("\n", $result) . "\n";
    }

    public function getQuote(){
        return $this->_dataLayerModel->getQuote();
    }

    public function addVariable($name, $value) {
        $this->_dataLayerModel->addVariable($name, $value);

        return $this;
    }

    public function addCustomVariable($name, $value = null) {
       if(is_array($name)){
          $this->_customVariables[] = $name;
       }
       else{
           $this->_customVariables[] = array($name => $value);
       }

        return $this;
    }

    public function formatPrice($price){
        return $this->_dataLayerModel->formatPrice($price);
    }

    public function getOrderCollection(){
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $this->_orderCollection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('entity_id', array('in' => $orderIds));

        return $this->_orderCollection;
    }

}
