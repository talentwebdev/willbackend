<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Paygate
{
    public $PayWeb3;
    public function __construct(){
        
        // include autoloader
        require_once dirname(__FILE__).'/paygate.payweb3.php';
        
        $this->PayWeb3 = new PayGate_PayWeb3();
    }
}
?>