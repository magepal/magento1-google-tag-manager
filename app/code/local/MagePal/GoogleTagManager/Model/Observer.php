<?php
/**
 * DataLayer
 * Copyright © 2016 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */
class MagePal_GoogleTagManager_Model_Observer
{

    /**
     * Add order information into Google Tag Manager block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setGoogleTagManagerOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('magepal_gtm_datalayer');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
