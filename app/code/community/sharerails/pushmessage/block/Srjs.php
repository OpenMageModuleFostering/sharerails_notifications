<?php
/**
 * Created by PhpStorm.
 * User: xinzheng
 * Date: 9/08/2014
 * Time: 11:36 PM
 */

class Sharerails_PushMessage_Block_Srjs extends Mage_Core_Block_Template {


    /**
     * @return string
     */
    protected  function getStorename(){
        $helper = Mage::helper('pushmessage');
        return $helper->getCoreConfig('storename');
    }

    /**
     * @return string
     */
    protected  function getSecret(){
        $helper = Mage::helper('pushmessage');
        return $helper->getCoreConfig('secret');
    }

    /**
     * @return string
     */
    protected  function getClientId(){
        $helper = Mage::helper('pushmessage');
        return $helper->getCoreConfig('clientid');
    }

}