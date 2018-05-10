<?php

/**
 * DataLayer
 * Copyright © 2016 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */
class MagePal_GoogleTagManager_Model_DataLayer extends Mage_Core_Model_Abstract {
    
    /**
     * @var Quote|null
     */
    protected $_quote = null;
    
    /**
     * Datalayer Variables
     * @var array
     */
    protected $_variables = array();

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    
    /**
     * @var string
     */
    protected $_fullActionName;


    /**
     * @param MessageInterface $message
     * @param null $parameters
     */
    public function __construct() {

        $this->_customerSession = Mage::getSingleton('customer/session');
        
        $this->fullActionName = Mage::app()->getFrontController()->getAction() ? Mage::app()->getFrontController()->getAction()->getFullActionName() : 'Unknown';;
        
        $this->addVariable('page_handle', $this->fullActionName);
        $this->addVariable('route', 'other');

        if($this->fullActionName === 'cms_index_index'){
            $this->addVariable('route', 'home_page');
        }

        if($this->fullActionName === 'cms_page_view'){
            $this->addVariable('route', 'cms_page');
        }

        $domain = Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB); // Maybe add $_SERVER['HTTP_HOST'];
        $this->addVariable('domain', $domain);

        $domain = 'unknown'; // Maybe add $_SERVER['HTTP_HOST'];
        if ($_SERVER['HTTP_HOST']) {
            $domain = $_SERVER['HTTP_HOST'];
        } else if (Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB)) {
            $domain = Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB);
        }
        $domain = preg_replace("(^https?://)", "", $domain);
        $domain = preg_replace("(\/)", "", $domain);
        $this->addVariable('domain', $domain);

        $this->setCustomerDataLayer();
        $this->setProductDataLayer();
        $this->setCategoryDataLayer();
        $this->setCartDataLayer();
          
    }

    /**
     * Return Data Layer Variables
     *
     * @return array
     */
    public function getVariables() {
        return $this->_variables;
    }

    /**
     * Add Variables
     * @param string $name
     * @param mix $value
     * @return MagePal\GoogleTagManager\Model\DataLayer
     */
    public function addVariable($name, $value) {

        if (!empty($name)) {
            $this->_variables[$name] = $value;
        }

        return $this;
    }

    
    /**
     * Set category Data Layer
     */
    protected function setCategoryDataLayer() {
        if($this->fullActionName === 'catalog_category_view'
           && $_category = Mage::registry('current_category')
        ) {
                $category = array();
                $category['id'] = $_category->getId();
                $category['name'] = $_category->getName();
                
                $this->addVariable('category', $category);
                $this->addVariable('route', 'category');
        }

        return $this;
    }
    
    
    /**
     * Set product Data Layer
     */
    protected function setProductDataLayer() {
        if($this->fullActionName === 'catalog_product_view'
           && $_product = Mage::registry('current_product')
        ) {
            $this->addVariable('route', 'product');

            $product = array();
            $product['id'] = $_product->getId();
            $product['sku'] = $_product->getSku();
            $product['name'] = $_product->getName();
            $product['price'] = $_product->getPrice();
            $this->addVariable('product', $product);
        }

        return $this;
    }

    /**
     * Set Customer Data Layer
     */
    protected function setCustomerDataLayer() {
        $customer = array();
        if ($this->_customerSession->isLoggedIn()) {
            $customer['isLoggedIn'] = true;
            if ($this->sendPersonal()) {
                $customer['id'] = $this->_customerSession->getCustomerId();
                $customer['groupId'] = $this->_customerSession->getCustomerGroupId();
            }
            //$customer['groupCode'] = ;
        } else {
            $customer['isLoggedIn'] = false;
        }
        
        $this->addVariable('customer', $customer);

        return $this;
    }
    
    
    /**
     * Set cart Data Layer
     */
    protected function setCartDataLayer() {
        if($this->fullActionName === 'checkout_index_index' || $this->fullActionName === 'checkout_cart_index'){
            $this->addVariable('route', 'cart');
        }

        if($this->fullActionName === 'checkout_onepage_index'){
            $this->addVariable('route', 'checkout');
        }

        if($this->fullActionName === 'checkout_onepage_success'){
            $this->addVariable('route', 'checkout_success');
        }

        $quote = $this->getQuote();
        $cart = array();

        $cart['hasItems'] = false;
        
        if ($quote->getItemsCount()) {
            $items = array();
            
            // set items
            foreach($quote->getAllVisibleItems() as $item){
                $items[] = array(
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $this->formatPrice($item->getRowTotalInclTax()),
                    'quantity' => $item->getQty()
                );
            }
            
            if(count($items) > 0){
                $cart['hasItems'] = true;
                $cart['items'] = $items; 
            }
            $cart['total'] = $this->formatPrice($quote->getGrandTotal());
            $cart['itemCount'] = $quote->getItemsCount();
            
            
            //set coupon code
            $coupon = $quote->getCouponCode();
            
            $cart['hasCoupons'] = $coupon ? true : false;

            if($coupon){
                $cart['couponCode'] = $coupon;
            }
        }
        
        $this->addVariable('cart', $cart);
        
        return $this;
    }
    
    
    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = Mage::getSingleton('checkout/cart')->getQuote();
        }
        return $this->_quote;
    }
    
    public function formatPrice($price){
        return Mage::getModel('directory/currency')->format(
            $price, 
            array('display'=>Zend_Currency::NO_SYMBOL), 
            false
        );
    }

}
