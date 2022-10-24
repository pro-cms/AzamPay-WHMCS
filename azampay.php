<?php

# AzamPay Payment Gateway

/**
 * WHMCS AzamPay Payment Gateway Module
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

if (isset($_POST['azampay_model']) && $_POST['azampay_model'] != '') {
    $testMode = $_POST['testMode'];
    $data = base64_decode($_POST['azampay_model']);
    if ($testMode == "yes" || $testMode) {
        // $url = 'https://checkout-test.azampay.co.tz/api/v1/Partner/PostCheckout';
        $url = 'https://sandbox.azampay.co.tz/api/v1/Partner/PostCheckout';
    } else {
        $url = 'https://checkout.azampay.co.tz/api/v1/Partner/PostCheckout';
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json-patch+json'));
    $result = curl_exec($curl);

    header('Location: ' . $result);
}

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
function azampay_MetaData() {
    return array(
        'DisplayName' => 'AzamPay Payment Gateway',
        'APIVersion' => '1.1', // Use API Version 1.1
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
function azampay_config() {
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'AzamPay Payment Gateway',
        ),
        // a text field type allows for single line text input
        'appName' => array(
            'FriendlyName' => 'App Name',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your app Name here',
        ),
        // a text field type allows for single line text input
        'clientID' => array(
            'FriendlyName' => 'Client ID',
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
function azampay_link($params) {
    // Gateway Configuration Parameters
    $appName = $params['appName'];
    $clientID = $params['clientID'];
    $testMode = $params['testMode'];

    //$appName = "Azampay Mobile Shope";
    //$accountId = "e70c2833-242f-418e-b759-c73795b0b90d";

    $systemurl = $params['systemurl'];
    //$callback_url = 'http://192.168.112.44/whmcs/modules/gateways/azampay/modules/gateways/callback/azampay.php';
    // $callback_url = $systemurl . 'modules/gateways/callback/azampay.php';
    // https://whmcs.duhosting.co.tz/clientarea.php?action=invoices
    $callback_url = $systemurl . 'clientarea.php?action=invoices';
    $invoiceId = $params['invoiceid'];
    $amount = $params['amount'];
    $currencyCode = "Tsh";
    $Language = "en";

    $postfields = array();
    $postfields['VendorName'] = $appName;
    $postfields['ClientId'] = $clientID;
    $postfields['AppName'] = $appName;
    $postfields['ExternalId'] = $invoiceId;
    $postfields['Language'] = $Language;
    $postfields['Amount'] = $amount;
    $postfields['Currency'] = $currencyCode;
    $postfields['RedirectSuccessURL'] = $callback_url;
    $postfields['RedirectFailURL'] = $callback_url;
    $postfields['Cart']['Items'][0]['Name'] = $params["description"];

    $data = base64_encode(json_encode($postfields));

    $htmlOutput = '<form method="post" id="azampay_form_to_submit" target=”_blank”>';
    //  target=”_blank”
    $htmlOutput .= '<textarea name="azampay_model" id="azampay_model" rows="4" cols="50" style="display:none;">' . $data . '</textarea>';
    $htmlOutput .= '<input type="text" name="testMode" style="display:none;" value="' . $testMode . '" />';
    //$htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '<div id="azampay_btn_submit" class="" style="display: flex;flex-direction: right;gap: 2px;color: #1e54b6;align-items: center;font-weight: bold;cursor: pointer;" onclick="document.getElementById(\'azampay_form_to_submit\').submit();">
                                  <img src="' . $systemurl . 'modules/gateways/azampay/btn.svg" alt="Pay with AzamPay">
                                </div>';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}

//$p = azampay_config();

//echo azampay_link($p);
