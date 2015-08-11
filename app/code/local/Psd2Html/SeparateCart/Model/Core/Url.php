<?php


class Psd2Html_SeparateCart_Model_Core_Url extends Mage_Core_Model_Url {

    /**
     * Build url by requested path and parameters
     *
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null) {

//        if ( $routePath == 'checkout/cart' ) {
//            $routePath = 'checkout/fht';
//        }

        return parent::getUrl($routePath, $routeParams);
    }
}