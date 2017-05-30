<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

Trait SMS_Reminder {
    public function sms_reminder_post()
    {
        $this->load->helper('nexo_sms');
        
        /*
        * Requirements: your PHP installation needs cUrl support, which not all PHP installations
        * include by default.
        *
        * Simply substitute your own username, password and phone number
        * below, and run the test code:
        */
        $username = $this->post('nexo_bulksms_username');
        $password = $this->post('nexo_bulksms_password');
        
        /*
        * Please see the FAQ regarding HTTPS (port 443) and HTTP (port 80/5567)
        */
        $url        = $this->post('nexo_bulksms_url'); //'https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
        $port       = $this->post('nexo_bulksms_port'); //443;

        /*
        * A 7-bit GSM SMS message can contain up to 160 characters (longer messages can be
        * achieved using concatenation).
        *
        * All non-alphanumeric 7-bit GSM characters are included in this example. Note that Greek characters,
        * and extended GSM characters (e.g. the caret "^"), may not be supported
        * to all networks. Please let us know if you require support for any characters that
        * do not appear to work to your network.
        */
        
        $seven_bit_msg = $this->post('sms');

        $transient_errors = array(
        40 => 1 # Temporarily unavailable
        );
        
        /*
        * Sending 7-bit message
        */
        
        $message        =    '';
        
        foreach ( ( array ) $this->post('phone') as $number) {
            
            /*
            * Your phone number, including country code, i.e. +44123123123 in this case:
            */
            
            $msisdn = $number;
                    
            $post_body = seven_bit_sms($username, $password, $seven_bit_msg, $msisdn);
            
            $result = send_message($post_body, $url, $port) ;
            $message    .=    formatted_server_response($result) . '\n';
        }
        
        if ($result['success']) {
            $this->response(array(
                'status'    =>    'success',
                'error'        =>    array(
                    'message'    =>    $message
                )
            ), 403);
        } else {
            $this->response(array(
                'status'    =>    'failed',
                'error'        =>    array(
                    'message'    =>    $message
                )
            ), 403);
        }
    }
}