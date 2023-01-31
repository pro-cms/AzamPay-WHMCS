<?php

#  selcom Payment Gateway

/**
 * WHMCS  selcom Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "gatewaymodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// if (isset($_POST[' selcom_model']) && $_POST[' selcom_model'] != '') {
//     $testMode = $_POST['testMode'];
//     $data = base64_decode($_POST[' selcom_model']);
//     if ($testMode == "yes" || $testMode) {
//         // $url = 'https://checkout-test. selcom.co.tz/api/v1/Partner/PostCheckout';
//         $url = 'https://sandbox. selcom.co.tz/api/v1/Partner/PostCheckout';
//     } else {
//         $url = 'https://checkout. selcom.co.tz/api/v1/Partner/PostCheckout';
//     }

//     $curl = curl_init();
//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//     curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json-patch+json'));
//     $result = curl_exec($curl);

//     header('Location: ' . $result);
// }

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function  selcom_MetaData() {
    return array(
        'DisplayName' => ' selcom Payment Gateway',
        'APIVersion' => '1.0', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function  selcom_config() {
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => ' selcom Payment Gateway',
        ),
        // a text field type allows for single line text input
        'vendorID' => array(
            'FriendlyName' => 'Vendor ID',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your app Name here',
        ),
        // a text field type allows for single line text input
        'apiKey' => array(
            'FriendlyName' => 'API key',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Client ID here',
        ),
         // a text field type allows for single line text input
         'apiSecret' => array(
            'FriendlyName' => 'API secret',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Client ID here',
        ),
        // the yesno field type displays a single checkbox option
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),

         // a text field type allows for single line text input
         'redirect_url' => array(
            'FriendlyName' => 'Redirect URL',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Client ID here',
        ),
         // a text field type allows for single line text input
         'cancel_url' => array(
            'FriendlyName' => 'Cancel URL',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Client ID here',
        ),
        // the yesno field type displays a single checkbox option
        'webhook' => array(
            'FriendlyName' => 'Cancel URL',
            'Type' => 'yesno',
            'Description' => 'Tick to enable test mode',
        ),
    );
}
 
/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function  selcom_link($params) {
    // Gateway Configuration Parameters
    $vendor = $params['vendorID'];
    $apiKey = $params['apiKey'];
    $apiSecret = $params['apiSecret'];
    $redirect_url = base64_encode($params['redirect_url']);
    $cancel_url = base64_encode($params['cancel_url']);
    $webhook = base64_encode($params['webhook']);
    $invoiceId = $params['invoiceid'];
    $amount = $params['amount'];
    $phone =  $params['clientdetails']['phonenumber'];
    $email = $params['clientdetails']['email'];
    $fullName = $params['clientdetails']['firstname']. " ".$params['clientdetails']['lastname'];
 
    //$appName = " selcom Mobile Shope";
    //$accountId = "e70c2833-242f-418e-b759-c73795b0b90d";

    $systemurl = $params['systemurl'];
    //$callback_url = 'http://192.168.112.44/whmcs/modules/gateways/ selcom/modules/gateways/callback/ selcom.php';
    // $callback_url = $systemurl . 'modules/gateways/callback/ selcom.php';
    // https://whmcs.duhosting.co.tz/clientarea.php?action=invoices
    $pay = new SelcomMobile();
    //Set your appropirate timezone
    date_default_timezone_set('Africa/Dar_es_Salaam');
//check timezone
    $api_key = 'ZEPSON-WsGHweDFyW5OOiAs';
    $api_secret = '987LLk3-khfd-54fa-Pj63-8dh7y9eb69b2';

    $base_url = "https://apigw.selcommobile.com/v1";
    $api_endpoint = "/checkout/create-order-minimal";
    $url = $base_url.$api_endpoint;
   


        $isPost =1;
        $req = array(
                    "vendor" => $vendor,
                    "order_id" =>$invoiceId,
                    "buyer_email" => $email,
                    "buyer_name" => $fullName,
                    "buyer_phone" => "255977777777",
                    "amount" => $amount,
                    "currency" => "TZS",
                    "redirect_url" =>  $redirect_url,
                    "cancel_url" => $cancel_url,
                    "no_of_items" => "1",
                    "webhook" =>  $webhook
                    );
                    $req = array(
                        "vendor" =>  $vendor,
                        "order_id" => $invoiceId,
                        "buyer_email" => $email,
                        "buyer_name" => $fullName,
                        "buyer_phone" => "255977777777",
                        "amount" => $amount,
                        "currency" =>  $params['currency'],
                        "payment_methods" => "ALL",
                        "redirect_url" => $redirect_url,
                        "cancel_url" =>  $cancel_url,
                        "webhook" => $webhook,
                        "billing" => array(
                          "firstname" =>  $params['clientdetails']['firstname'],
                          "lastname" =>$params['clientdetails']['lastname'],
                          "address_1" => $params['clientdetails']['address1'],
                          "address_2" => $params['clientdetails']['address2'],
                          "city" => $params['clientdetails']['city'],
                          "state_or_region" => $params['clientdetails']['region']??"Dar Es Salaam",
                          "postcode_or_pobox" => $params['clientdetails']['zipcode'],
                          "country" => "TZ",
                          "phone" =>   $phone
                        ),
                        // "shipping" => array(
                        //   "firstname" => "John",
                        //   "lastname" => "Doe",
                        //   "address_1" => "969 Market",
                        //   "address_2" => "",
                        //   "city" => "San Francisco",
                        //   "state_or_region" => "CA",
                        //   "postcode_or_pobox" => "94103",
                        //   "country" => "US",
                        //   "phone" => "255682852526"
                        // ),
                        // "buyer_remarks" => "None",
                        // "merchant_remarks" => "None",
                        "no_of_items" => 1
                      );
                      
        $authorization = base64_encode($api_key);

        $timestamp = date('c'); //2019-02-26T09:30:46+03:00

        $signed_fields  = implode(',', array_keys($req));
        $digest = $pay->computeSignature($req, $signed_fields, $timestamp, $api_secret);

        $response = $pay->sendJSONPost($url, $isPost, json_encode($req), $authorization, $digest, $signed_fields, $timestamp);
        try {
            $url = base64_decode($response['data'][0]['payment_gateway_url']);
 
            } catch (\Throwable $th) {
               return null;
            }
            if($url==null){
                header('Location: ' . $params['systemurl']);
            }
        header('Location: ' . $url);
                      
}


class SelcomMobile {

    public function sendJSONPost($url, $isPost, $json, $authorization, $digest, $signed_fields, $timestamp) {
      $headers = array(
        "Content-type: application/json;charset=\"utf-8\"", "Accept: application/json", "Cache-Control: no-cache",
        "Authorization: SELCOM $authorization",
        "Digest-Method: HS256",
        "Digest: $digest",
        "Timestamp: $timestamp",
        "Signed-Fields: $signed_fields",
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      if($isPost){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      }
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch,CURLOPT_TIMEOUT,90);
      $result = curl_exec($ch);
      curl_close($ch);
      $resp = json_decode($result, true);
      return $resp;
   }
  
  
    public function computeSignature($parameters, $signed_fields, $request_timestamp, $api_secret){
      $fields_order = explode(',', $signed_fields);
      $sign_data = "timestamp=$request_timestamp";
      if($signed_fields!=null){
      foreach ($fields_order as $key) {
        $sign_data .= "&$key=".$parameters[$key];
      }
  }
      //HS256 Signature Method
      return base64_encode(hash_hmac('sha256', $sign_data, $api_secret, true));
    }
  
  
    public function getStatus($url, $isPost, $authorization, $digest, $signed_fields,$timestamp){
      // /v1/checkout/order-status?order_id={order_id}
      $url = $url;
      $isPost = false;
      //get request
      $headers = array(
          "Content-type: application/json;charset=\"utf-8\"", "Accept: application/json", "Cache-Control: no-cache",
          "Authorization: SELCOM $authorization",
          "Digest-Method: HS256",
          "Digest: $digest",
          "Timestamp: $timestamp",
          // "Signed-Fields: $signed_fields",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($isPost){
          curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_TIMEOUT,90);
        $result = curl_exec($ch);
        curl_close($ch);
        $resp = json_decode($result, true);
        return $resp;
  
    }
  
  }
