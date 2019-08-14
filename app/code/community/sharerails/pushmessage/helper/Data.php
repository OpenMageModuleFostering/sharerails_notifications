<?php
/**
 * Created by PhpStorm.
 * User: xinzheng
 * Date: 5/08/2014
 * Time: 8:17 PM
 */

class Sharerails_PushMessage_Helper_Data extends Mage_Core_Helper_Data {
    const XML_BASE_CORE = 'sr_pushmessage/apiconfig/';
//    const XML_BASE_PANEL = 'sharerails/panel/';

    /**
     * @return bool
     */
    public function isEnabled ()
    {
        return Mage::getStoreConfigFlag(self::XML_BASE_CORE . 'enabled');
    }

    /**
     * Get a config param.
     *
     * @param string $field
     * @return string
     */
    public function getCoreConfig ($field)
    {
        return Mage::getStoreConfig(self::XML_BASE_CORE . $field);
    }


}