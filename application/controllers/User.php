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
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');
        $user = $this->user->insert_entry($jsonArray);
        if($user != null)
        {
            $tokenData = array();
            $tokenData['id'] = $user->id; //TODO: Replace with data for token
            $user->token = AUTHORIZATION::generateToken($tokenData);
            return $this->set_response(array('status' => true, 'data' => $user), REST_Controller::HTTP_OK);
        }
        return $this->set_response(array('status' => false, 'error' => "Error"), REST_Controller::HTTP_OK);
        
    }

    /**
     * URL: http://localhost/willbackend/user/signin
     * Method: POST
     */
    public function signin_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');
        $users = $this->user->get_entry($jsonArray);
        if(count($users) && password_verify($jsonArray["password"], $users[0]->password))
        {
            $tokenData = array();
            $tokenData['id'] = $users[0]->id; //TODO: Replace with data for token
            $users[0]->token = AUTHORIZATION::generateToken($tokenData);            
            return $this->set_response(array('status' => true, 'data' => $users[0]), REST_Controller::HTTP_OK);
        }        
        
        return $this->set_response(array('status' => false, 'error' => 'Error'), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/user/willupdate
     * Method: POST
     */
    public function willupdate_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                if($this->user->update_will($id, $jsonArray['will']))
                {
                    $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                    return;
                }               
                return $this->set_response(array('status' => false, 'error' => 'Can not update data'), REST_Controller::HTTP_OK);
            }
        }

        $this->set_response(array('status' => false, 'error' => 'UnAuthorized Request'), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/user/savetoken
     * Method: POST
     */
    public function savetoken_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                if($this->user->update_token($id, $jsonArray['token']))
                {
                    $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                    return;
                }               
                return $this->set_response(array('status' => false, 'error' => 'Can not update token'), REST_Controller::HTTP_OK);
            }
        }

        $this->set_response(array('status' => false, 'error' => 'UnAuthorized Request'), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/user/profileupdate
     * Method: POST
     */
    public function profileupdate_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                if($this->user->update_profile($id, $jsonArray))
                {
                    $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                    return;
                }               
                return $this->set_response(array('status' => false, 'error' => 'Can not update profile', 'token' => $decodedToken), REST_Controller::HTTP_OK);
            }
        }

        $this->set_response(array('status' => false, 'error' => 'UnAuthorized Request'), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/user/requestresetpassword
     * Method: POST
     */
    public function requestresetpassword_post()
    {
        
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        
        $users = $this->user->get_entry($jsonArray);
        if(count($users) == 0)
        {
            return $this->set_response(array('status' => false, 'error' => 'No registered email'), REST_Controller::HTTP_OK);
        }
        
        $code = "";
        
        for($i = 0 ; $i < 6 ; $i ++)
        {
            $code .= rand(0, 9);
        }
        
        
        $this->user->save_code($jsonArray['email'], $code);
        
        $this->load->library('email');
        $config = array();
        $config["protocol"] = "smtp";
        $config["smtp_host"] = "smtp.luna9.co.za";
        $config["smtp_port"] = "587";
        $config["smtp_user"] = "wewill@luna9.co.za";
        $config["smtp_pass"] = "Online2020@!";
        $config["mailtype"] = "text"; // or text
        $config["newline"] = "\r\n";
        $this->email->initialize($config);
        $this->email->from('wewill@luna9.co.za', 'Identification');        
        $this->email->subject('Will Password Reset Code');   
        $this->email->message("Code: " . $code);
        $this->email->to($jsonArray['email']);
        if($this->email->send())
        {
            return $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
        }
        
        return $this->set_response(array('status' => false, 'error' => 'Can not send email. Please check your email again.'), REST_Controller::HTTP_OK);
    }    
    
    /**
     * URL: http://localhost/willbackend/user/checkcode
     * Method: POST
     */
    public function checkcode_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if($this->user->check_code($jsonArray['email'], $jsonArray['code']))
        {
            return $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
        }
        return $this->set_response(array('status' => false, 'error' => 'Wrong Code'), REST_Controller::HTTP_OK);
    }    

    /**
     * URL: http://localhost/willbackend/user/resetpassword
     * Method: POST
     */
    public function resetpassword_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if($this->user->reset_password($jsonArray['email'], $jsonArray['password']))
        {
            return $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
        }
        return $this->set_response(array('status' => false, 'error' => 'Can not reset password'), REST_Controller::HTTP_OK);
    }    

    /**
     * URL: http://localhost/willbackend/user/authresetpassword
     * Method: POST
     */
    public function authresetpassword_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Users_model', 'user');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                if($this->user->reset_id_password($id, $jsonArray['password']))
                {
                    return $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                }
                return $this->set_response(array('status' => false, 'error' => 'Can not reset password'), REST_Controller::HTTP_OK);
            }
        }

        $this->set_response(array('status' => false, 'error' => 'UnAuthorized Request'), REST_Controller::HTTP_OK);
    }   
}
