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

class Notification extends REST_Controller
{
    /**
     * URL: http://localhost/willbackend/notification/fetch
     * Method: GET
     */
    public function fetch_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Notification_model', 'notification');

        //$notifications = $this->notification->fetch_entry(1);
        
        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                $notifications = $this->notification->fetch_entry($id);
                $this->set_response(array('status' => true, 'data' => $notifications), REST_Controller::HTTP_OK);
                return;      
            }
            return $this->set_response(array('status' => false, 'error' => 'UnAuthorized'), REST_Controller::HTTP_OK);
        }

        $this->set_response(array('status' => false, 'error' => 'No authorization field', 'token' => $jsonArray), REST_Controller::HTTP_OK);
        
        
    }

    /**
     * URL: http://localhost/willbackend/notification/read
     * Method: POST
     */
    public function read_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Notification_model', 'notification');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                
                if($this->notification->read_entry($id, $jsonArray['id']))
                {
                    $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                    return;
                } 
                               
            }
        }

        $this->set_response(array('status' => false), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/willbackend/notification/add
     * Method: POST
     */
    public function add_post()
    {
        $jsonArray = json_decode(file_get_contents('php://input'),true); 
        $this->load->model('Notification_model', 'notification');

        if (array_key_exists('authorization', $jsonArray) && !empty($jsonArray['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($jsonArray['authorization']);
            if ($decodedToken != false) {
                $id = $decodedToken->id;
                
                if($this->notification->add_entry($jsonArray['title'], $jsonArray['content']))
                {
                    $this->set_response(array('status' => true), REST_Controller::HTTP_OK);
                    return;
                } 
                               
            }
        }

        $this->set_response(array('status' => false), REST_Controller::HTTP_OK);
    }

    

}
