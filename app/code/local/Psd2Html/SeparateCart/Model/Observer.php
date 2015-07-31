<?php

class Psd2Html_SeparateCart_Model_Observer
{


    public function addPostData(Varien_Event_Observer $observer)
    {
        $action = Mage::app()->getFrontController()->getAction();
        if (is_object($action) AND $action->getFullActionName() == 'checkout_cart_add') {
            if ($action->getRequest()->getParam('typecart')) {

                Mage::log('addPostData');
                // ID IS PRESENT, SO LETS ADD IT
                $item = $observer->getProduct();
                $additionalOptions = array();
                $additionalOptions[] = array(
                    'label' => 'Type cart',
                    'value' => $action->getRequest()->getParam('typecart')
                );
                $item->addCustomOption('additional_options', serialize($additionalOptions));
//                Mage::log($item->getData());
                Mage::log('----------------------------');
                $opts = $item->getData('additional_options');
//                $opts=unserialize($opts);
                Mage::log($opts);

//                $productOptions = $item->getCustomOption();
            }
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

    public function updateItem(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        $productOptions = $product->getTypeInstance(true)->getOrderOptions($product);


        $options = array();
//         if ($optionIds = $observer->getItem()->getOptionByCode('option_ids')) {
//         if ($optionIds = $observer->getEvent()->getQuoteItem()->getOptionByCode('option_ids')) {
        if ($optionIds = $observer->getQuoteItem()->getOptionByCode('additional_options')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                Mage::log('updateItem*******************');
//                 Mage::log($optionId);
//                 Mage::log($optionIds->getValue());
                Mage::log($observer->getProduct()->getOptionById('aHR0cDovL3d3dy5tYWdlbnRvLmxvY2FsL3Byb2R1Y3QtdGVzdC5odG1s'));
//                 foreach($observer->getProduct()->getCustomOptions() as $item){
//                     Mage::log($item->getData());
//                 }

                Mage::log('updateItem*******************');
//                     if ($option = $this->getProduct()->getOptionById($optionId)) {
//                     if ($option = $observer->getProduct()->getOptionById($optionId)) {
                if ($option = $observer->getProduct()->getOptionById($optionId)) {

//                     $quoteItemOption = $this->getItem()->getOptionByCode('option_' . $option->getId());
                    $quoteItemOption = $observer->getEvent()->getQuoteItem()->getOptionByCode('option_' . $option->getId());
                    Mage::log('updateItem+++++++++++++++++++++');
                    Mage::log($quoteItemOption);
                    Mage::log('updateItem+++++++++++++++++++++');

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }

//        Zend_Debug::dump(Mage::getSingleton('core/session')->getSessionItemsCart());

//        Mage::log('updateItem=================================');
//        Mage::log($options);
//        Mage::log('updateItem=================================');

//        $item = $observer->getQuoteItem();
//        if ($item->getParentItem()) {
//            $item = $item->getParentItem();
//        }
//
////        Mage::log($item->getData());
//        Mage::log($item->getOptionList());
//
//        $customPrice = 55;
//
//        // set price
//        $item->setCustomPrice($customPrice);
//        $item->setOriginalCustomPrice($customPrice);
//        $item->getProduct()->setIsSuperMode(true);
//        Mage::log('updateItem');
    }


    /**
     * Set price 0 for product fht
     * @param $observer
     */
    public function salesQuoteAddressCollectTotalsBefore($observer)
    {

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
                    $item->setOriginalCustomPrice(2);
                }
            }

        }

//        Zend_Debug::dump($additionalOptions);
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