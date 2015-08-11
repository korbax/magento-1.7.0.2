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
     * Update/Clear button click on cart
     * @param Varien_Event_Observer $observer
     */
    public function cartUpdate(Varien_Event_Observer $observer)
    {
//        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
//        Mage::log('cartUpdate===--------------------------------');
//
////        $action = $observer->getControllerAction()->getFullActionName();
//
//        $post = Mage::app()->getRequest()->getPost('update_cart_action');
//        Mage::log($post);
//        if ($post == 'empty_cart') {
//            $quote = Mage::getModel('checkout/cart')->getQuote();
//            $quote_items = $quote->getItemsCollection();
//            $data = array();
//            $sessionItemsCart = Mage::getSingleton('core/session')->getSessionItemsCart();
//            if(isset($quote_items)) {
//
////            Mage::getSingleton('core/session')->setSessionItemsCart();
//                foreach ($quote_items as $item) {
//                    $additionalOptions = $item->getOptionByCode('additional_options');
//                    if (isset($additionalOptions)) {
//                        $currentItem = unserialize($additionalOptions->getValue());
//                        $data[] = array(
//                            'product_id' => $additionalOptions['product_id'],
//                            'qty' => $item->getQty(),
//                            'code' => $additionalOptions['code'],
//                            'value' => $currentItem[0]['value'],
//                            'old_item_id' => $additionalOptions['item_id']
//                        );
//                    }
//                }
//                if(isset($currentItem)){
//                    $items = Mage::getSingleton('core/session')->getSessionItemsCart();
//
//                    if(isset($items)){
//
//                        foreach($items as $item){
//                            if($item['value'] == $currentItem[0]['value']){
//                                //item remove
//                                Mage::log('cartUpdate"""""""""""""""""""""""""""""');
//                                Mage::log($item['product_id'] . '===' . $item['value']);
//                                Mage::log('cartUpdate"""""""""""""""""""""""""""""');
////                                unset($item[1]);
////                                $item->unsetSessionItemsCart('1');
////                                Mage::getSingleton('core/session')->unsSessionItemsCart(1);
//                            }
//                        }
//                    }
//                }
//                Mage::log($items);
////                Mage::log($data);
////                Mage::log('cartUpdate"""""""""""""""""""""""""""""');
////                Mage::getSingleton('core/session')->setSessionItemsCart($data);
//            }
//        }
//        Mage::log('cartUpdate===------------------------------------' . $action);
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

    public function updateItem(Varien_Event_Observer $observer)
    {
        Mage::log('updateItem!!!');

//        $quote = Mage::getModel('checkout/cart')->getQuote();
//        $quote_items = $quote->getItemsCollection();
//        $data = array();
//        $sessionItemsCart = Mage::getSingleton('core/session')->getSessionItemsCart();
//        if(isset($quote_items) AND !isset($sessionItemsCart)){
//            Mage::log('updateItem!!! add to session');
////            Mage::getSingleton('core/session')->setSessionItemsCart();
//            foreach ($quote_items as $item) {
//                $additionalOptions = $item->getOptionByCode('additional_options');
//                if(isset($additionalOptions)){
//                    $currentItem = unserialize($additionalOptions->getValue());
//                    $data[] = array(
//                        'product_id' => $additionalOptions['product_id'],
//                        'qty' => $item->getQty(),
//                        'code' => $additionalOptions['code'],
//                        'value' => $currentItem[0]['value'],
//                        'old_item_id' => $additionalOptions['item_id']
//                    );
//                }
//            }
//            Mage::getSingleton('core/session')->setSessionItemsCart($data);
//        }
//
//        $action = Mage::app()->getFrontController()->getAction();
//        $items = Mage::getSingleton('core/session')->getSessionItemsCart();
//        if(isset($items)){
////            Mage::getSingleton('checkout/session')->clear();
//            foreach($items as $item){
//                $item1 = $_product = Mage::getModel('catalog/product')->load($item['product_id']);
//                $additionalOptions = array();
//
//                if($action->getFullActionName() == self::CARTDEFAULT){
//                    $additionalOptions[] = array(
//                        'label' => 'Type cart',
//                        'value' => self::CARTDEFAULTVALUE
//                    );
//                    print 'SIMPLE';
//                }
//                elseif($action->getFullActionName() == self::CARTFHT){
//                    $additionalOptions[] = array(
//                        'label' => 'Type cart',
//                        'value' => self::CARTFHTVALUE
//                    );
//                    print 'FHT';
//                }
////                Zend_Debug::dump($item);
//
//                $item1->addCustomOption('additional_options', serialize($additionalOptions));
//
//                $cart = Mage::getModel('checkout/cart');
//                $cart->init();
//                $params = array(
//                    'product' => 1,
//                    'product_id' => 1,
//                    'qty' => $item['qty']
//                );
//                $request = new Varien_Object();
//                $request->setData($params);
////////    $cart->addProduct($_product, $params);
////                $cart->addProduct($_product, $request);
////                $cart->save();
////                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
//            }
//        }


//**************************************************************************************

//        $action = Mage::app()->getFrontController()->getAction();
//        $item = $observer->getQuoteItem();
//        $product = $item->getProduct();
//        $productOptions = $product->getTypeInstance(true)->getOrderOptions($product);
//        Mage::log('updateItem');
//        Mage::log($action->getFullActionName());
//
//
//        $options = array();
//        if ($optionIds = $observer->getQuoteItem()->getOptionByCode('additional_options')) {
//            $options = array();
//            foreach (explode(',', $optionIds->getValue()) as $optionId) {
//                Mage::log('updateItem*******************');
////                 foreach($observer->getProduct()->getCustomOptions() as $item){
////                     Mage::log($item->getData());
////                 }
//
//                Mage::log('updateItem*******************');
////                     if ($option = $this->getProduct()->getOptionById($optionId)) {
////                     if ($option = $observer->getProduct()->getOptionById($optionId)) {
//                if ($option = $observer->getProduct()->getOptionById($optionId)) {
//
////                     $quoteItemOption = $this->getItem()->getOptionByCode('option_' . $option->getId());
//                    $quoteItemOption = $observer->getEvent()->getQuoteItem()->getOptionByCode('option_' . $option->getId());
//                    Mage::log('updateItem+++++++++++++++++++++');
//                    Mage::log($quoteItemOption);
//                    Mage::log('updateItem+++++++++++++++++++++');
//
//                    $group = $option->groupFactory($option->getType())
//                        ->setOption($option)
//                        ->setQuoteItemOption($quoteItemOption);
//
//                    $options[] = array(
//                        'label' => $option->getTitle(),
//                        'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
//                        'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
//                        'option_id' => $option->getId(),
//                        'option_type' => $option->getType(),
//                        'custom_view' => $group->isCustomizedView()
//                    );
//                }
//            }
//        }
    }


    /**
     * Set price 0 for product fht
     * @param $observer
     */
    public function salesQuoteAddressCollectTotalsBefore($observer)
    {
        Mage::log('salesQuoteAddressCollectTotalsBefore');
        $quote = $observer->getQuote();
        $quote_items = $quote->getItemsCollection();
        foreach ($quote_items as $item) {
//            $additionalOptions = array(
//                array(
//                    'code'  => 'my_code',
//                    'label' => 'This text is displayed through additional options',
//                    'value' => 'ID is ' . $item->getProductId() . ' and SKU is ' . $item->getSku()
//                )
//            );
//            $item->addOption(
//                array(
//                    'code'  => 'additional_options',
//                    'value' => serialize($additionalOptions),
//                )
//            );
//            print $item->getId();
//            Zend_Debug::dump($item->getOptionByCode('option_' . $item->getId()));
//            Zend_Debug::dump($item->getOptionByCode('Type cart'));
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
     * Save to session items before create order with part items
     * @param $observer
     */
    public function copyItemsBeforeCreateOrder($observer)
    {
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


}