<?php

class Psd2Html_SeparateCart_Model_Observer
{
    const CARTDEFAULT = 'checkout_cart_index';
    const CARTDEFAULTVALUE = 'simple';

    const CARTFHT = 'checkout_fht_index';
    const CARTFHTVALUE = 'fht';

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

        $items = Mage::getSingleton('core/session')->getSessionItemsCart();
//        Zend_Debug::dump($items);
//        if(isset($items)){
//            Mage::getSingleton('checkout/session')->clear();
//            Mage::getSingleton('checkout/cart')->truncate();
//            print 'clear';
//            foreach($items as $item){
//                if($item['value'] == $value){
//                    $item1 = $_product = Mage::getModel('catalog/product')->load($item['product_id']);
//                    $additionalOptions = array();
//
//                    $additionalOptions[] = array(
//                        'label' => 'Type cart',
//                        'value' => $value
//                    );
//
//                    Zend_Debug::dump($item);
//
//                    $item1->addCustomOption('additional_options', serialize($additionalOptions));
//
//                    $cart = Mage::getModel('checkout/cart');
//                    $cart->init();
//                    $params = array(
//                        'product' => 1,
//                        'product_id' => 1,
//                        'qty' => $item['qty']
//                    );
//                    $request = new Varien_Object();
//                    $request->setData($params);
////////    $cart->addProduct($_product, $params);
//                    $cart->addProduct($_product, $request);
//                    $cart->save();
//                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
//                }
//            }
//        }
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
    }

    /**
     * 1. Add product to cart FHT from session
     * 2. Clear session
     * @param Varien_Event_Observer $observer
     */
    public function afterCreateOrder(Varien_Event_Observer $observer)
    {
        Mage::log('afterCreateOrder');
    }

    /**
     * Save to session items before create order with part items
     * @param $observer
     */
    public function copyItemsBeforeCreateOrder(Varien_Event_Observer $observer)
    {
        Mage::log('copyItemsBeforeCreateOrder');
//        $quote = Mage::getModel('checkout/cart')->getQuote();
//        $quote_items = $quote->getItemsCollection();
//        $data = array();
//        foreach ($quote_items as $item) {
//
//            $additionalOptions = $item->getOptionByCode('additional_options');
//            $currentItem = unserialize($additionalOptions->getValue());
//            $data[] = array(
//                'product_id' => $additionalOptions['product_id'],
//                'qty' => $item->getQty(),
//                'code' => $additionalOptions['code'],
//                'value' => $currentItem[0]['value'],
//                'old_item_id' => $additionalOptions['item_id']
//            );
////            Zend_Debug::dump($additionalOptions->getData());
//        }
//        Mage::getSingleton('core/session')->setSessionItemsCart($data);
    }

    /**
     * Add to cart product from session
     * @param $observer
     */
    public function addProductToCartAfterCreateOrder($observer)
    {
        Mage::log('addProductToCartAfterCreateOrder');
        $qty = 1;
        $_product = Mage::getModel('catalog/product')->load($id);
        $cart = Mage::getModel('checkout/cart');
        $cart->init();
        $cart->addProduct($_product, array('qty' => $qty));

        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);


        $items = Mage::getSingleton('core/session')->getSessionItemsCart();
        foreach($items as $item){
            $_product = Mage::getModel('catalog/product')->load($item['product_id']);
            $cart = Mage::getModel('checkout/cart');
            $cart->init();
            $cart->addProduct($_product, array('qty' => $item['qty']));

            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        }
    }

    /**
     * Add custom event before load checkout FHT
     * @param $observer
     * @return $this
     */
    public function addEvent(Varien_Event_Observer $observer)
    {
//        print $observer->getEvent()->getControllerAction()->getFullActionName();
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_index') {
            $event_data_array = array();
            Mage::dispatchEvent('load_checkout_onepage', $event_data_array);
        }
    }


}