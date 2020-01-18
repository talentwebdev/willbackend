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

class User extends REST_Controller
{
    /**
     * URL: http://localhost/willbackend/user/signup
     * Method: POST
     */
    public function signup_post()
    {
        /*
        $this->load->model('Users_model', 'user');
        $user = $this->user->insert_entry();
        if($user != null)
        {
            $tokenData = array();
            $tokenData['id'] = $user->id; //TODO: Replace with data for token
            $user->token = AUTHORIZATION::generateToken($tokenData);
            return $this->set_response(array('status' => true, 'data' => $user), REST_Controller::HTTP_OK);
        }
        return $this->set_response(array('status' => false, 'error' => "Error"), REST_Controller::HTTP_OK);
        
        */
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        return $this->set_response($jsonArray["firstParam"], REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/user/signin
     * Method: POST
     */
    public function signin_post()
    {
        $this->load->model('Users_model', 'user');
        $users = $this->user->get_entry();
        if(count($users) && password_verify($_POST["password"], $users[0]->password))
        {
            $tokenData = array();
            $tokenData['id'] = $users[0]->id; //TODO: Replace with data for token
            $users[0]->token = AUTHORIZATION::generateToken($tokenData);            
            return $this->set_response(array('status' => true, 'data' => $users[0]), REST_Controller::HTTP_OK);
        }        
        
        return $this->set_response(array('status' => false, 'error' => 'Error'), REST_Controller::HTTP_OK);
    }

}
