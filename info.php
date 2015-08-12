<?php

	//phpinfo();

require_once 'app/Mage.php';
ini_set('display_errors', 1);
$yourStoreCode = 'default';
Mage::app($yourStoreCode);
Mage::getSingleton('core/session', array('name'=>'frontend'))->setSessionId($_COOKIE['frontend']);
//echo "cart_id=".Mage::helper('checkout/cart')->getCart()->getQuote()->getId();


//$order = Mage::getModel('sales/order')->load(55);
//$quote_items = $order->getAllItems();
//
//
////$quote = Mage::getModel('checkout/cart')->getQuote();
////$quote_items = $quote->getItemsCollection();
//$data = array();
////Mage::getSingleton('core/session')->setSessionItemsCart();
//foreach ($quote_items as $item) {
//
//    Zend_Debug::dump($item->getData('product_options'));
//
//    $additionalOptions = $item->getOptionByCode('additional_options');
//
//    Zend_Debug::dump($additionalOptions);
//
//    if (isset($additionalOptions)) {
//        $currentItem = unserialize($additionalOptions->getValue());
//        $data[] = array(
//            'product_id' => $additionalOptions['product_id'],
//            'qty' => $item->getQty(),
//            'code' => $additionalOptions['code'],
//            'value' => $currentItem[0]['value'],
//            'old_item_id' => $additionalOptions['item_id']
//        );
//    }
//
////    $additionalOptions = $item->getOptionByCode('additional_options');
////    if(isset($additionalOptions)){
////        $currentItem = unserialize($additionalOptions->getValue());
////        $data[] = array(
////            'product_id' => $additionalOptions['product_id'],
////            'qty' => $item->getQty(),
////            'code' => $additionalOptions['code'],
////            'value' => $currentItem[0]['value'],
////            'old_item_id' => $additionalOptions['item_id']
////        );
////    }
//}
//Mage::getSingleton('core/session')->setSessionItemsCart($data);
//Mage::getSingleton('core/session')->setSessionItemsCart();
print '-------------------------------------------';
//Zend_Debug::dump($data);
//print '===========================================';
//////Zend_Debug::dump(Mage::getSingleton('core/session')->getSessionItemsCart());
//$items = Mage::getSingleton('core/session')->getSessionItemsCart();
//foreach($items as $item){
//
//    Zend_Debug::dump($item);
//
//    $_product = Mage::getModel('catalog/product')->load($item['product_id']);
//    $item1 = $_product;
//    $additionalOptions = array();
//    $additionalOptions[] = array(
//        'label' => 'Type cart',
//        'value' => $item['value']
//    );
//    $item1->addCustomOption('additional_options', serialize($additionalOptions));
//    $opts = $item1->getData('additional_options');
//
//
//
//    $cart = Mage::getModel('checkout/cart');
//    $cart->init();
//    $params = array(
//        'product' => 1,
//        'product_id' => 1,
//        'qty' => $item['qty'],
////        'additional_options' => array(
////            'label' => 'Type cart',
////            'value' => $item['value']
////        ),
////        'options' => array(
////            'additional_options' => array(
////                'label' => 'Type cart',
////                'value' => $item['value']),
////        )
//    );
//    $request = new Varien_Object();
//    $request->setData($params);
////    $cart->addProduct($_product, $params);
//    $cart->addProduct($_product, $request);
//    $cart->save();
//
////    Mage::dispatchEvent('catalog_product_load_after', array());
//
//
////    $cart->getItems()->clear();
////    $cart->getItems()->setQuote($cart->getQuote());
//    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
//}
//
//
//
//////sales_order_place_before
//////adminhtml_sales_order_create_process_data
////sales_order_save_after



$incrementId = '100000022';
$incrementId = '100000055';
$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
foreach ($order->getAllItems() as $item) {
    $options = $item->getProductOptions();
    $customOptions = $options['additional_options'];
    if(!empty($customOptions))
    {
        foreach ($customOptions as $option)
        {
            $optionTitle = $option['label'];
            $optionId = $option['option_id'];
            $optionType = $option['type'];
            $optionValue = $option['value'];
//            Zend_Debug::dump($option['value']);
        }
    }
}

Zend_Debug::dump($optionValue);

print Mage::getBaseUrl()->getCheckoutUrl();

?>
