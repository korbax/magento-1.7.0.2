<?php

class Psd2Html_SeparateCart_Helper_Data extends Mage_Core_Helper_Abstract
{

    const TYPE_CART_SIMPLE = 'checkout/cart';
    const TYPE_CART_FHT = 'checkout/fht';
    private $count_simple = null;
    private $count_fht = null;


    public function isFHTCart()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
        $path = $url->getPath();
        if(trim($path, '\/') == self::TYPE_CART_FHT) return true;
        return false;
    }

    private function getCalculateProduct()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote_items = $quote->getItemsCollection();

        $this->count_simple = null;
        $this->count_fht = null;

        if(isset($quote_items)) {
            foreach ($quote_items as $item) {
                $additionalOptions = $item->getOptionByCode('additional_options');
                if (isset($additionalOptions)) {
                    $currentItem = unserialize($additionalOptions->getValue());
                    if($currentItem[0]['value'] == 'simple'){
                        $this->count_simple += $item->getQty();
                    }
                    elseif($currentItem[0]['value'] == 'fht') {
                        $this->count_fht += $item->getQty();
                    }
                }
            }
        }
    }

    public function getCountSimple()
    {
        $this->getCalculateProduct();
        if($this->count_simple) return  $this->__('My Cart (%s item)', $this->count_simple);
        return  $this->__('My Cart');
    }

    public function getCountFHT()
    {
        $this->getCalculateProduct();
        if($this->count_fht) return  $this->__('My FHT (%s item)', $this->count_fht);
        return  $this->__('My FHT');
    }
}

