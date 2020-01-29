<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

require_once dirname(__FILE__).'/../libraries/global.inc.php';
/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 * 
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class Payment extends CI_Controller 
{
    /**
     * URL: http://localhost/willbackend/payment
     * Method: Get
     */
    public function index()
    {
       $this->load->library("paygate");        

        $fullPath  = getCurrentUrl();
        $root      = getRoot($fullPath);
        $directory = getFinalDirectory($fullPath);

       $mandatoryFields = array(
            'PAYGATE_ID'        => '10011072130',
            'REFERENCE'         => generateReference(),
            'AMOUNT'            => 100,
            'CURRENCY'          => 'ZAR',
            'RETURN_URL'        => $fullPath['protocol'] . $fullPath['host'] . '/' . $root . '/payment/result',
            'TRANSACTION_DATE'  => '2020-01-23 15:37:37',
            'LOCALE'            => 'en-za',
            'COUNTRY'           => 'ZAF',
            'EMAIL'             => 'support@paygate.co.za'
        );

        $encryption_key = "secret";

        $this->paygate->PayWeb3->setEncryptionKey($encryption_key);

        $this->paygate->PayWeb3->setInitiateRequest($mandatoryFields);

        $returnData = $this->paygate->PayWeb3->doInitiate();

        $data['process_url'] = $this->paygate->PayWeb3::$process_url;
        $data['PayWeb3'] = $this->paygate->PayWeb3;
        $this->load->view('paygate', $data);
    }

    public function result()
    {
        $this->load->view("payment_success");
    }
}
