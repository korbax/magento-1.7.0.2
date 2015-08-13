<?php

class Psd2Html_SeparateCart_Model_Observer
{
    const CARTDEFAULT = 'checkout_cart_index';
    const CARTDEFAULTVALUE = 'simple';

    const CARTFHT = 'checkout_fht_index';
    const CARTFHTVALUE = 'fht';

    const CHECKOUT_ACTION = 'checkout_onepage_index';
    const CHECKOUT_ACTION_FHT = 'checkout_onepagefht_index';

    const CHECKOUT_URL_DEFAULT = 'checkout/onepage/index';
    const CHECKOUT_URL_FHT = 'checkout/onepagefht/index';

    private static $status = true;



    public function addPostData(Varien_Event_Observer $observer)
    {
        Mage::log('111addPostData');
        $action = Mage::app()->getFrontController()->getAction();
        if (is_object($action) AND $action->getFullActionName() == 'checkout_cart_add') {
            if ($action->getRequest()->getParam('typecart')) {

                // ID IS PRESENT, SO LETS ADD IT
                $item = $observer->getProduct();
                $additionalOptions = array();
                $additionalOptions[] = array(
                    'label' => 'Type cart',
                    'value' => $action->getRequest()->getParam('typecart')
                );
                $item->addCustomOption('additional_options', serialize($additionalOptions));
            }
        }
    }

    public function loadPage(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction()->getFullActionName();

        if($action == self::CARTDEFAULT){
            $this->updateCart(self::CARTDEFAULTVALUE);
        }
        elseif($action == self::CARTFHT AND self::$status){
            $this->updateCart(self::CARTFHTVALUE);
        }
    }

    private function updateCart($value)
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote_items = $quote->getItemsCollection();
        $data = array();
        $sessionItemsCart = Mage::getSingleton('core/session')->getSessionItemsCart();
//        if(isset($quote_items) AND !isset($sessionItemsCart)) {
        if(isset($quote_items)) {
            Mage::log('updateCart!!! add to session');
//            Mage::getSingleton('core/session')->setSessionItemsCart();
            foreach ($quote_items as $item) {
                $additionalOptions = $item->getOptionByCode('additional_options');
                if (isset($additionalOptions)) {
                    $currentItem = unserialize($additionalOptions->getValue());
                    $data[] = array(
                        'product_id' => $additionalOptions['product_id'],
                        'qty' => $item->getQty(),
                        'code' => $additionalOptions['code'],
                        'value' => $currentItem[0]['value'],
                        'old_item_id' => $additionalOptions['item_id']
                    );
                }
            }
            Mage::getSingleton('core/session')->setSessionItemsCart($data);
        }
    }

    /**
     * When create order
     * @param Varien_Event_Observer $observer
     */
    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getItem();
        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }

    /**
     * Set price 0 for product fht
     * @param $observer
     */
    public function salesQuoteAddressCollectTotalsBefore($observer)
    {
//        Mage::log('salesQuoteAddressCollectTotalsBefore');
        $quote = $observer->getQuote();
        $quote_items = $quote->getItemsCollection();
        foreach ($quote_items as $item) {
            $additionalOptions = $item->getOptionByCode('additional_options');

            if(isset($additionalOptions)){
                $currentItem = unserialize($additionalOptions->getValue());
                if ($currentItem[0]['value'] == 'fht') {
                    $item->setOriginalCustomPrice(0);
                }
            }
        }
    }

    /**
     * 1. Save FHT product to session
     * 2. Remove all product with FHT
     * @param Varien_Event_Observer $observer
     */
    public function beforeCreateOrder(Varien_Event_Observer $observer)
    {
        Mage::log('beforeCreateOrder');
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote_items = $quote->getItemsCollection();
        $data = array();

        if(isset($quote_items)) {
            Mage::getSingleton('core/session')->setSessionItemsCart(); //clear session
            foreach ($quote_items as $item) {
                $additionalOptions = $item->getOptionByCode('additional_options');
                if (isset($additionalOptions)) {
                    $currentItem = unserialize($additionalOptions->getValue());
                    $data[] = array(
                        'product_id' => $additionalOptions['product_id'],
                        'qty' => $item->getQty(),
                        'code' => $additionalOptions['code'],
                        'value' => $currentItem[0]['value'],
                        'old_item_id' => $additionalOptions['item_id']
                    );
                }
            }
            Mage::getSingleton('core/session')->setSessionItemsCart($data);
        }

        if($data){
            foreach($data as $product){
                if($product['value'] == self::CARTFHTVALUE){
                    $cartHelper = Mage::helper('checkout/cart');
                    $items = $cartHelper->getCart()->getItems();
                    foreach($items as $item){
                        if($item->getItemId() == $product['old_item_id']){
//                        print '<br/>remove=' . $item->getItemId();
                            $cartHelper->getCart()->removeItem($item->getItemId())->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * 1. Save Simple product to session
     * 2. Remove all product with Simple
     * @param Varien_Event_Observer $observer
     */
    public function beforeCreateOrderFht(Varien_Event_Observer $observer)
    {
        Mage::log('beforeCreateOrderFht');
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote_items = $quote->getItemsCollection();
        $data = array();

        if(isset($quote_items)) {
            Mage::getSingleton('core/session')->setSessionItemsCart(); //clear session
            foreach ($quote_items as $item) {
                $additionalOptions = $item->getOptionByCode('additional_options');
                if (isset($additionalOptions)) {
                    $currentItem = unserialize($additionalOptions->getValue());
                    $data[] = array(
                        'product_id' => $additionalOptions['product_id'],
                        'qty' => $item->getQty(),
                        'code' => $additionalOptions['code'],
                        'value' => $currentItem[0]['value'],
                        'old_item_id' => $additionalOptions['item_id']
                    );
                }
            }
            Mage::getSingleton('core/session')->setSessionItemsCart($data);
        }

        if($data){
            foreach($data as $product){
                if($product['value'] == self::CARTDEFAULTVALUE){
                    $cartHelper = Mage::helper('checkout/cart');
                    $items = $cartHelper->getCart()->getItems();
                    foreach($items as $item){
                        if($item->getItemId() == $product['old_item_id']){
                            $cartHelper->getCart()->removeItem($item->getItemId())->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * 1. Add product to cart FHT from session
     * 2. Clear session
     * @param Varien_Event_Observer $observer
     */
    public function afterCreateOrder(Varien_Event_Observer $observer)
    {
        Mage::log('afterCreateOrder');

        $event = $observer->getEvent();
        $order = $event->getOrder();
        $incrementId = $order->getIncrementId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        $optionValue = null;
        foreach ($order->getAllItems() as $item) {
            $options = $item->getProductOptions();
            $customOptions = $options['additional_options'];
            if(!empty($customOptions))
            {
                foreach ($customOptions as $option)
                {
//                    $optionTitle = $option['label'];
//                    $optionId = $option['option_id'];
//                    $optionType = $option['type'];
                    $optionValue = $option['value'];
                }
            }
        }

        $items = Mage::getSingleton('core/session')->getSessionItemsCart();
        if(isset($items)){
            Mage::getSingleton('checkout/session')->clear();
            Mage::getSingleton('checkout/cart')->truncate();
            Mage::log('afterCreateOrder before foreach');
            foreach($items as $item){
                if($item['value'] != $optionValue){
                    $_product = Mage::getModel('catalog/product')->load($item['product_id']);
                    $additionalOptions = array();

                    if($optionValue == self::CARTFHTVALUE){
                        $additionalOptions[] = array(
                            'label' => 'Type cart',
                            'value' => self::CARTDEFAULTVALUE
                        );
                    }
                    else{
                        $additionalOptions[] = array(
                            'label' => 'Type cart',
                            'value' => self::CARTFHTVALUE
                        );
                    }

                    $_product->addCustomOption('additional_options', serialize($additionalOptions));
                    $cart = Mage::getModel('checkout/cart');
                    $cart->init();
                    $params = array(
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty']
                    );
                    $request = new Varien_Object();
                    $request->setData($params);
                    $cart->addProduct($_product, $request);
                    $cart->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                }
            }
            Mage::getSingleton('core/session')->setSessionItemsCart(); //clear session
        }
    }

    public function changeRedirectCartUrlFHT(Varien_Event_Observer $observer)
    {
        Mage::log('changeRedirectCartUrlFHT');
        $params = $observer->getRequest()->getParams();
        if(isset($params) && $params['typecart'] == self::CARTFHTVALUE){
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/fht'));
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }

    public function restoreProductInCart(Varien_Event_Observer $observer)
    {
        Mage::log('restoreProductInCart');
        $typeCart = $observer->getTypecart();
        Mage::log($observer->getTypecart());

        $items = Mage::getSingleton('core/session')->getSessionItemsCart();
        if(isset($items) and isset($typeCart)){
//            Mage::getSingleton('checkout/session')->clear();
//            Mage::getSingleton('checkout/cart')->truncate();
            Mage::log('restoreProductInCart add to cart');

            foreach($items as $item) {
//                Mage::log('Start----------------');
//                Mage::log($item);
//                Mage::log('End----------------');
                if($item['value'] != $typeCart) {
//                    Zend_Debug::dump($item);
//                    Mage::log('$typeCart=' . $typeCart);
//                    Mage::log($item);
                    $_product = Mage::getModel('catalog/product')->load($item['product_id']);

                    $additionalOptions[] = array(
                        'label' => 'Type cart',
                        'value' => $item['value']
                    );

                    $_product->addCustomOption('additional_options', serialize($additionalOptions));
                    $cart = Mage::getModel('checkout/cart');
                    $cart->init();
                    $params = array(
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty']
                    );
                    $request = new Varien_Object();
                    $request->setData($params);
                    $cart->addProduct($_product, $request);
                    $cart->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                }
            }
            Mage::getSingleton('core/session')->setSessionItemsCart(); //clear session
        }
    }

    /**
     * Add custom event before load checkout FHT
     * @param $observer
     * @return $this
     */
    public function addEvent(Varien_Event_Observer $observer)
    {
//        Mage::log('====' . $observer->getEvent()->getControllerAction()->getFullActionName());
        $fullActionName = $observer->getEvent()->getControllerAction()->getFullActionName();
        if ($fullActionName == self::CHECKOUT_ACTION) {
            $event_data_array = array();
            if(!$this->comparePreviousUrl(self::CHECKOUT_URL_DEFAULT)) Mage::dispatchEvent('load_checkout_onepage', $event_data_array);
        }

        if ($fullActionName == self::CHECKOUT_ACTION_FHT) {
            $event_data_array = array();
            if(!$this->comparePreviousUrl(self::CHECKOUT_URL_FHT)) Mage::dispatchEvent('load_checkout_onepage_fht', $event_data_array);
        }

        if($fullActionName != self::CHECKOUT_ACTION_FHT && $fullActionName != self::CHECKOUT_ACTION_FHT) {
            if( $this->comparePreviousUrl(self::CHECKOUT_URL_FHT) ) Mage::dispatchEvent('gone_from_checkout_page', array('typecart' => self::CARTFHTVALUE));
            elseif ( $this->comparePreviousUrl(self::CHECKOUT_URL_DEFAULT) ) Mage::dispatchEvent('gone_from_checkout_page', array('typecart' => self::CARTDEFAULTVALUE));
        }
    }

    /**
     * Compare url, return true if previous and current url the same
     * @param $currentUrl
     * @return bool
     */
    private function comparePreviousUrl($currentUrl)
    {
        if(isset($_SESSION['core']['last_url'])) {
            $url = Mage::getSingleton('core/url')->parseUrl($_SESSION['core']['last_url'], null);
            $path = $url->getPath();
            if (trim($path, '\/') == $currentUrl) return true;
            return false;
        }
        else return false;
    }


}