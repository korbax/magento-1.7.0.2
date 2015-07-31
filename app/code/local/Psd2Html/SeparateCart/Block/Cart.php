<?php
/**
 * Class Psd2Html_Autopopulate_Block_Autopopulate
 */

class Psd2Html_SeparateCart_Block_Cart extends Mage_Core_Block_Template
{

    public function getCountFht()
    {
        $tbybList = Mage::getSingleton('checkout/session')->getData('tbyb_list');
        if( count($tbybList) > 0 AND count($tbybList) < 5) return count($tbybList);
        return false;
    }
}