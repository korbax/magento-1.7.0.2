<?php

	//phpinfo();

require_once 'app/Mage.php';
ini_set('display_errors', 1);
$yourStoreCode = 'default';
Mage::app($yourStoreCode);
Mage::getSingleton('core/session', array('name'=>'frontend'))->setSessionId($_COOKIE['frontend']);
//echo "cart_id=".Mage::helper('checkout/cart')->getCart()->getQuote()->getId();


$quote = Mage::getModel('checkout/cart')->getQuote();
$quote_items = $quote->getItemsCollection();
$data = array();
//Mage::getSingleton('core/session')->setSessionItemsCart();
foreach ($quote_items as $item) {

    $additionalOptions = $item->getOptionByCode('additional_options');
    if(isset($additionalOptions)){
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
//Mage::getSingleton('core/session')->setSessionItemsCart($data);
//Mage::getSingleton('core/session')->setSessionItemsCart();
//print '-------------------------------------------';
//Zend_Debug::dump($data);
//print '===========================================';
////Zend_Debug::dump(Mage::getSingleton('core/session')->getSessionItemsCart());
$items = Mage::getSingleton('core/session')->getSessionItemsCart();
foreach($items as $item){

//    Zend_Debug::dump($item);

    $_product = Mage::getModel('catalog/product')->load($item['product_id']);
    $cart = Mage::getModel('checkout/cart');
    $cart->init();
    $params = array(
        'product' => 1,
        'product_id' => 1,
        'qty' => $item['qty'],
//        'options' => array(
//            12345 => array(
//                'quote_path' => $image,
//                'secret_key' => substr(md5(file_get_contents(Mage::getBaseDir() . $image)), 0, 20)),
//        )
    );
    $request = new Varien_Object();
    $request->setData($params);
//    $cart->addProduct($_product, $params);
    $cart->addProduct($_product, $request);
    $cart->save();


//    $cart->getItems()->clear();
//    $cart->getItems()->setQuote($cart->getQuote());
    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

//    Mage::getSingleton('core/session', array('name' => 'frontend'));
//    $cProd = Mage::getModel('catalog/product');
////    $id = $cProd->getIdBySku("$sku");
//    header('Location: '. Mage::getUrl('checkout/cart/add', array('product' => $item['product_id'])));
}



////sales_order_place_before
////adminhtml_sales_order_create_process_data
////sales_order_save_after

?>
