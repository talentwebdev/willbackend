<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

require APPPATH . '/libraries/REST_Controller.php';

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

class Email extends REST_Controller
{
    /**
     * URL: http://localhost/willbackend/email/send
     * Method: POST
     */
    public function send_post()
    {
        $this->load->library('email');

        $config = array();

        $config["protocol"] = "smtp";
        $config["smtp_host"] = "smtp.luna9.co.za";
        $config["smtp_port"] = "587";
        $config["smtp_user"] = "wewill@luna9.co.za";
        $config["smtp_pass"] = "Online2020@!";
        $config["mailtype"] = "html"; // or text
        $config["newline"] = "\r\n";

        $this->email->initialize($config);
        $this->email->from('wewill@luna9.co.za', 'Identification');        
        $this->email->subject('Will');   
        
        $headers = $this->input->request_headers();
        $this->load->model('Users_model', 'user');
        
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                $email = $jsonArray['email'];
                //if($this->user->check_email($id, $email))
                {
                    $this->email->message($jsonArray['content']);
                    
                    $this->email->to($email);
                    if($this->email->send())
                    {
                        $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                        return;
                    }
                    return $this->set_response(array('status' => false, 'error' => 'sending error', 'token' => $jsonArray['authorization']), REST_Controller::HTTP_OK);
                }                
            }
            return $this->set_response(array('status' => false, 'error' => 'autorization error', 'token' => $jsonArray['authorization']), REST_Controller::HTTP_OK);
        }

        return $this->set_response(array('status' => false, 'error' => 'no field'), REST_Controller::HTTP_OK);
    }
}
