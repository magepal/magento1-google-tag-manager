<?php
/**
 * DataLayer
 * Copyright © 2016 MagePal. All rights reserved.
 * See COPYING.txt for license details.
 */
class MagePal_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE        = 'googletagmanager/general/active';
    const XML_PATH_ACCOUNT       = 'googletagmanager/general/account';


    /**
     * Whether GTM is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }

    /**
     * Get GTM account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
    }


}
