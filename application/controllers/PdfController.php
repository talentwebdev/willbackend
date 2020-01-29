<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");


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

class PdfController extends CI_Controller 
{
    /**
     * URL: http://localhost/willbackend/pdf/generate
     * Method: Get
     */
    public function generate()
    {
        $html = "<h2>Hello</h2>";
        
        // Load pdf library
        $this->load->library('pdf');
        
        // Load HTML content
        $this->pdf->dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation
        $this->pdf->dompdf->setPaper('A4', 'landscape');
        
        // Render the HTML as PDF
        $this->pdf->dompdf->render();
        
        // Output the generated PDF (1 = download and 0 = preview)
        $this->pdf->dompdf->stream("welcome.pdf", array("Attachment"=>1));
        
    }
}
