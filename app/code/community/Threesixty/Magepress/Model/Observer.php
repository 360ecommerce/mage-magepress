<?php

class Threesixty_Magepress_Model_Observer
{
    function updateAllCookies($observer)
    {
        $this->updateRecentlyViewedCookie();
        $this->updateCustomerCookie();
        $this->updateCartCookie();
    }

    function updateRecentlyViewedCookie($observer = null)
    {
        //Recently viewed
        $_viewedProducts = array();

        // Add to array
        foreach (Mage::getSingleton('Mage_Reports_Block_Product_Viewed')->getItemsCollection() as $item)
        {
            $_viewedProducts[$item->getId()]['request_path'] = Mage::getUrl( $item['request_path'] );
            $_viewedProducts[$item->getId()]['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $item['small_image'];
            $_viewedProducts[$item->getId()]['name'] = $item['name'];
        }

        if( ! empty( $_viewedProducts ) ) {
            $this->setCookie( 'magepress_recently_viewed', $_viewedProducts, true );
        }
    }

    function updateCustomerCookie($observer = null)
    {
        $_user = Mage::getSingleton('customer/session');

        if( $_user->isLoggedIn() ) {
            $this->setCookie( 'magepress_user', $_user->getCustomer()->getData(), true );
        } else {
            $this->removeCookie( 'magepress_user' );
        }
    }

    function updateCartCookie($observer = null) 
    {
        //Recalculate totals
        $_cartQty = Mage::getSingleton('checkout/cart')->getSummaryQty();

        //Add new Cookie
        if( $_cartQty !== null ) {
            $this->setCookie( 'magepress_cart_count', $_cartQty );
        } else {
            $this->setCookie( 'magepress_cart_count', 0 );
        }
    }

    function setCookie( $name, $data, $encrypt = false )
    {
        $host = Mage::getStoreConfig('wordpress/integration/url');
        $host = parse_url($host);
        $host = isset( $host['host'] ) ? $host['host'] : false;

        // Encrypt ?
        if( $encrypt ) {
            $data = base64_encode( serialize( $data ) );
        }

        if($host) {
            unset($_COOKIE[$name]);
            Mage::getModel('core/cookie')->set( $name, $data, 3600, '/', '.' . $host );
        }
    }

    function removeCookie( $name )
    {
        $host = Mage::getStoreConfig('wordpress/integration/url');
        $host = parse_url($host);
        $host = isset( $host['host'] ) ? $host['host'] : false;

        if($host) {
            unset($_COOKIE[$name]);
            Mage::getModel('core/cookie')->set( $name, null, -1, '/', '.' . $host );
        }
    }
}
