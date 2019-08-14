<?php
/**
 * Created by PhpStorm.
 * User: xinzheng
 * Date: 25/07/2014
 * Time: 12:42 AM
 */

//import the lib
require_once(Mage::getBaseDir('lib') . '/Sharerails/ShareRails.php');

class Sharerails_PushMessage_Model_Observer {
    
    public function pushMessage($postData){
        
        /**************** here is push message ************************/
        /** push message start ************************/
        //create an api object
        $currentStoreName = Mage::helper("pushmessage")->getCoreConfig('storename');
        $currentSecret = Mage::helper("pushmessage")->getCoreConfig('secret');
        $api = new ShareRails($currentStoreName, $currentSecret);
        /***  set up the pus data ****/
        //set timestamp
        $api->setTimestamp(time());
        
        $api->setPostData($postData);

        //set post data hash
        $postDataHash = $api->getPostDataHash();

        //set protocol
        $api->setProtocol('http');
        //set domain
        $api->setDomain("www.sharerails.com");

        //set path
        $api->setPath("/api/push");
        //set query
        $currentQuery = array(
            'app' => $api->getStoreName(),
            "timestamp" => $api->getTimestamp(),
            "datamd5hash" => $postDataHash
        );

        //set Query
        $api->setQuery($currentQuery);

               
        //post to api
        $api->post();

    }
    
    public function pushMessageAddProduct($observer){
        // fetch the current event
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $productName = $product->getName();
        //get unit price
        $unit_price = number_format($product->getPrice(),2,'.',',');

        //get product image
        //can set getImage, getSmallImage, getThumbnail
        $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        
        
        $login_session = Mage::getSingleton('customer/session');

        //default name
        $customerName = "Guest";

        if($login_session->isLoggedIn()){
            $customerName = Mage::helper('customer')->getCustomerName();
        }

        $productUrl = $product->getProductUrl();

        //add custom message to shopping cart
        $checkout_session = Mage::getSingleton("checkout/session");

        //get session quote
        $session_quote = $checkout_session->getQuote();

        //get cart quote
        $cart_quote = Mage::getModel('checkout/cart')->getQuote();

        //get total quantity
//        $totalQuantity = round($cart_quote->getItemsQty(),0);
        $totalQuantity = $product->getQty();

        //get subtotal
//        $subtotal = number_format($cart_quote->getSubtotal(),2,'.',',');
        $subtotal = number_format($totalQuantity*$unit_price, 2,'.',',');


        //the message
        $msg = "<strong>Retrieved Info: <br />".$customerName."</strong> currently added product: <strong>"
            . $productName."</strong><br />The unit price of the item is: <strong>".$unit_price."</strong>
                    <br />The subtotal price of this selection is: <strong>".$subtotal."</strong>
                    <br /><a href='$productUrl'>Product URL: ".$productUrl."</a>
                    <br />The total product quantity of this selection is: ".$totalQuantity."
                    <br />The image of the product: <img src='".$img."' width='150'/>";
        //$checkout_session->addSuccess($msg);
        
        $postData = array(
          "to" => array(),
          "message" => array(
            "product_title" => $productName,
            "product_id" => $product->getId(),
            "product_price" => $unit_price,
            "product_link" =>  $productUrl,
            "product_image" => $img,
            "template" => "Shopping Cart Updated",
            "event" => "cart-updated"
          ),
         // "event" => "Shopping Cart Updated"
        );
        
        $postData = json_encode($postData);
        
        
        $this->pushMessage($postData);

        
        //die('dead');
    }

    public function placeOrder($observer){
        //fetch the current event
        $event = $observer->getEvent();

        //get event name
        $event_name = $event->getName();

        //get customer
        $login_session = Mage::getSingleton('customer/session');
        //default name
        $customer_name = "Guest";

        if($login_session->isLoggedIn()){
            $customer_name = Mage::helper('customer')->getCustomerName();
        }

        //get customer email address
        $customer_email = $observer->getEvent()->getOrder()->getCustomerEmail();

        //add custom message to shopping cart
        $checkout_session = Mage::getSingleton("checkout/session");

        //get product details
        $quote = $checkout_session->getQuote();
//        $cartItems = $quote->getAllVisibleItems();
        $cartItems = $quote->getAllItems();
        $billingAddress = $quote->getBillingAddress();
        
        $countryCode = $billingAddress->getCountry();
        $countryModel = Mage::getModel('directory/country')->loadByCode($countryCode);
        $country = $countryModel->getName();
        
        $city = $billingAddress->getCity();
        
        
        //$checkout_session->addSuccess('Customer UserName: ' . $customer_name);
        //$checkout_session->addSuccess('Customer Email: ' . $customer_email);
        //$checkout_session->addSuccess('Shopping info details: <br />');

        foreach ($cartItems as $item)
        {

            $productId = $item->getProductId();

            $product = Mage::getModel('catalog/product')->load($productId);
            $product_name = $product->getName();
            $product_url = $product->getProductUrl();
            $unit_price = number_format($product->getPrice(),2,'.',',');
            // Do something
            //$checkout_session->addSuccess('Product ID: ' . $productId);

            //$checkout_session->addSuccess('Product Name: ' . $product_name);
            //$checkout_session->addSuccess('Product URL: ' . $product_url);
            //$checkout_session->addSuccess('Product Unit Price: ' . $unit_price);
            $img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();

            
            $postData = array(
              "to" => array(),
              "message" => array(
                "product_title" => $product_name,
                "product_id" => $product->getId(),
                "product_price" => $unit_price,
                "product_link" =>  $product_url,
                "product_image" => $img,
                
                "user_city" => $city,
                "user_country" => $country,

                "template" => "Product Purchased",
                "event" => "product-purchased"
              )
            );
                        
            $postData = json_encode($postData);
            
            
            $this->pushMessage($postData);
            

        }

        //get total items and total price of the cart
        // Total items added in cart
        //$totalItems = Mage::getModel('checkout/cart')->getQuote()->getItemsCount();

        // Total Quantity added in cart
        //$totalQuantity = Mage::getModel('checkout/cart')->getQuote()->getItemsQty();

        // Sub Total for item added in cart
        //$subTotal = Mage::getModel('checkout/cart')->getQuote()->getSubtotal();

        //grand total for for item added in cart
        //$grandTotal = Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();

        //mail('james.butler@dimicho.com', "test", "hello world");
        //die('dea');
        
    }
    
    public function configChanged($observer)
    {      
        
        $clientId = Mage::helper('pushmessage')->getCoreConfig('clientid');
        
        $verifyFile = fopen(Mage::getBaseDir().DS."sharerails.html", "w") or die("Unable to open file!");
        $txt = "{ key: '$clientId' }";
        fwrite($verifyFile, $txt);
        fclose($verifyFile);        
        
    }
}