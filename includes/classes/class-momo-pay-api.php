<?php

/**
 * Book PostType
 *
 * @category   Components
 * @package    papa-site
 * @author     Bogere Goldsoft
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @link       tupimelab.com
 * @since      1.0.0
 * Reference - https://github.com/patricpoba/mtn-momo-api-php
 */

namespace Papa\Site;

use PatricPoba\MtnMomo\MtnConfig;
use PatricPoba\MtnMomo\MtnDisbursement;
use PatricPoba\MtnMomo\MtnRemittance;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 *  MoMo PAY API Class
 */
class MoMoPAYAPI {

    /**
     *  Disbursement config object
     */
    private $disbursementConfig = null;

    /**
     * Remittance config object
     */
    private $remittanceConfig = null;

    
    /**
     * Class constructor
     */
    public function __construct() {
       $this->initMTNConfiguration();
    }

    public function initMTNConfiguration(){

        //  Your Sandbox credentials :
        // Ocp-Apim-Subscription-Key: 14cc98e2d79e481188851609c4849f81
        // UserId (X-Reference-Id)  : 052a9341-157c-4664-9301-434033ea0174
        // ApiKey (ApiSecret)       : d833611f44264579bd2b4bba41b7b61a
        // Callback host            : https://tashal.faceduka.biz/pay-workers/
        
        $disburseConfig = new MtnConfig([
            // mandatory credentials
            'baseUrl'               => 'https://sandbox.momodeveloper.mtn.com', 
            'currency'              => 'EUR', 
            'targetEnvironment'     => 'sandbox', 

          // disbursement credentials
            "disbursementApiSecret"   => '1f6b121775514837bd3d1e8b03077c5a', 
            "disbursementPrimaryKey"  => '8b4e92655695412db34d225979d1545d', 
            "disbursementUserId"      => '69fe239f-9bb0-4e49-b34f-77dcb0acf6df'
        ]);

        $this->disbursementConfig = new MtnDisbursement($disburseConfig);

        // Set up Remittance configuration
        $remitConfig = new MtnConfig([ 
            // mandatory credentials
            'baseUrl'               => 'https://sandbox.momodeveloper.mtn.com', 
            'currency'              => 'EUR', 
            'targetEnvironment'     => 'sandbox', 
        
            // disbursement credentials
            "remittanceApiSecret"   => '16aed11b67314c2186801b4e5c4597e7', 
            "remittancePrimaryKey"  => '294789795faa4293a41b638ccc975454', 
            "remittanceUserId"      => 'c1c64f32-6311-4275-a400-47c332a5a22c'
        ]);

        $this->remittanceConfig  = new MtnRemittance($remitConfig);

    }

    /**
     * Disburse the funds to the employees mobile money accounts
     * 
     */
    public function depositFundsToEmployees(){
        $disbursement = $this->disbursementConfig;

        $fullName = $_POST['fullName'];
        $phoneNumber = $_POST['phoneNumber'];
        $payerNumber = $_POST['payerNumber'];
        $reason = $_POST['reason'];
        $payAmount =  $_POST['amount'];

        if (empty($fullName) || empty($phoneNumber) || empty($payerNumber) ||empty($reason)) {
            # code..
            $response = array(
                'success' => false,
                'message' => 'Either fullName or Employee phone number or payer Number is empty'
             );

             //wp_send_json_error($response);
             wp_send_json($response, 422);  
        }
        

        $params = [
            "mobileNumber"      => $phoneNumber, 
            "amount"            => $payAmount, 
            "externalId"        => $this->generateRandomId(),
            "payerMessage"      => $reason,
            "payeeNote"         => 'Withdraw your salary from mobile money'
        ];
        
        /**
         * Transfer() is used to request a payment from a consumer (Payer). 
         * The payer will be asked to authorize the payment. 
         * The transaction is executed once the payer has authorized the payment. 
         * The transaction will be in status PENDING until it is authorized or declined by the payer or it is timed out by the system. 
         */
        $transactionId = $disbursement->transfer($params);
        
        /**
         * Status of the transaction can be validated by checking the `status` 
         * field on the result of `getTransaction()` method.
         */
        $transaction = $disbursement->getTransaction($transactionId);
        //retry like 3 times before halting the getTransaction() status
        error_log("disbursement");
        //error_log(print_r($transaction->content,true));
         $response = $transaction->content;
        //$response = $transaction->toJson();
        error_log(print_r($response,true));
        wp_send_json($response, 200);
    }

    /**
     * Transfer operation is used to transfer an amount from the own account 
     * to a payee account.
     */
    public function remitFundsToPayeeAccount(){
     //yes u shall transfer money to the payee account...
     //as long as they have mtn mobile phone number, u can send or receive money from them.
       $remittance = $this->remittanceConfig;
        $payee= $_POST['payee'];
        $payeeNumber = $_POST['payeeNumber'];
        $payerNumber = $_POST['payerNumber'];
        $reason = $_POST['reason'];
        $payAmount =  $_POST['amount'];

        if (empty($payee) || empty($payeeNumber) || empty($payerNumber) || empty($payAmount)) {
            # code..
            $response = array(
                'success' => false,
                'message' => 'Either receiver or sender Number is empty'
             );

             //wp_send_json_error($response);
             wp_send_json($response, 422);  
        }
       $params = [
        "mobileNumber"      => $payeeNumber, 
        "amount"            => $payAmount, 
        "externalId"        => $this->generateRandomId(),
        "payerMessage"      => $reason,
        "payeeNote"         => 'Sending some money back to my parents'
      ];

    /**  
     * Transfer operation is used to transfer an amount from the own account to a payee account.
     * Status of the transaction can validated by using the GET /transfer/{referenceId}
    */
     $transactionId = $remittance->transfer($params);


    /**
    * This operation is used to get the status of a transfer. X-Reference-Id 
    * that was passed in the post is used as reference to the request.
    */
     $transaction = $remittance->getTransaction($transactionId);

     $response = $transaction->content;

     wp_send_json($response, 200);
    
    }

    /**
     * Generate the randomId
     */
    private function generateRandomId(){
        return random_int(100000, 999999);
    }

    /**
     * Customise the woocommerce checkout page instead of using 
     * checkout field editor plugin.. i prefer code instead
     * https://quadlayers.com/remove-woocommerce-checkout-fields/
     * These guys actually build the that plugin for customising the checkout fields
     * You do not need all the fields when using mtn mobile money
     */
    public function papa_remove_checkout_fields($fields){

        //dissable most of the checkout fields
        //unset($fields['billing']['billing_first_name']);
        //unset($fields['billing']['billing_last_name']);
        unset($fields['billing']['billing_company']);
        //unset($fields['billing']['billing_address_1']);
        unset($fields['billing']['billing_address_2']);
        //unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_state']);
        //unset($fields['billing']['billing_phone']);
        //unset($fields['order']['order_comments']);
        unset($fields['billing']['billing_email']);
        unset($fields['account']['account_username']);
        unset($fields['account']['account_password']);
        unset($fields['account']['account_password-2']);
          return $fields; // yes filter hook is for modifying the data 
    }



}