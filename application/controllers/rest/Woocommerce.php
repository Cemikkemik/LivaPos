<?php
defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php'); // Include Rest Controller

include_once(APPPATH . '/modules/nexo/vendor/autoload.php'); // Include from Nexo module dir

include_once(APPPATH . '/modules/nexo_woo/vendor/autoload.php'); // Include from Nexo_woo module dir

include_once(APPPATH . '/modules/nexo_woo/inc/traits_loader.php'); // Include from Nexo_woo module dir

use Carbon\Carbon;

use Automattic\WooCommerce\Client;

use \Curl\Curl;

class Woocommerce extends Rest_Controller
{
	use Woo_Categories,
		Woo_Items;
    
    public function __construct()
    {
        parent::__construct();   
		
		$this->load->helper('nexopos');
        $this->load->library('session');
        $this->load->model('Options');
        $this->load->database();    
		
        if (! $this->oauthlibrary->checkScope('core')) {
           $this->__forbidden();
        }
		
		$this->load->config( 'nexo_woo' );
		
		// Get WooCommerce API
		$url_prefix			=	str_replace( '-', '_', $this->config->item( 'nexo_woo_url_prefix' ) );
		$consumer_key		=	str_replace( '-', '_', $this->config->item( 'nexo_woo_consumer_key_prefix' ) );
		$consumer_secret	=	str_replace( '-', '_', $this->config->item( 'nexo_woo_consumer_secret_prefix' ) );
		
		$this->Woo 	= new Client(
			$_SERVER[ 'HTTP_' . $url_prefix ] , // Your store URL
			$_SERVER[ 'HTTP_' . $consumer_key ], // Your consumer key
			$_SERVER[ 'HTTP_' . $consumer_secret ], // Your consumer secret
			array(
				'wp_api' => true, 		// Enable the WP REST API integration
				'version' => 'wc/v1', 	// WooCommerce WP REST API version
				'query_string_auth' => true
			)
		);
		
		$this->Curl	=	new Curl;
		
		$this->WooCommerce	=	new WC_API_Client( 
			$_SERVER[ 'HTTP_' . $url_prefix ] , // Your store URL
			$_SERVER[ 'HTTP_' . $consumer_key ], // Your consumer key
			$_SERVER[ 'HTTP_' . $consumer_secret ], // Your consumer secret
			array(
				'ssl_verify'		=> 	false, 		// Enable the WP REST API integration
				'return_as_array'	=>	true,
				'debug' 			=> true, 	// WooCommerce WP REST API version
			) 
		);
		
		if( ! $this->Woo ) {
			$this->__forbidden( 'woo auth failed' );
		}
    }
    
    private function __success()
    {
        $this->response(array(
            'status'        =>    'success'
        ), 200);
    }
    
    /**
     * Display a error json status
     *
     * @return json status
    **/
    
    private function __failed()
    {
        $this->response(array(
            'status'        =>    'failed'
        ), 403);
    }
    
    /**
     * Return Empty
     *
    **/
    
    private function __empty()
    {
        $this->response(array(
        ), 200);
    }
    
    /**
     * Not found
     *
     *
    **/
    
    private function __404()
    {
        $this->response(array(
            'status'        =>    '404'
        ), 404);
    }
    
    /**
     * Forbidden
    **/
    
    private function __forbidden( $status = 'forbidden' )
    {
        $this->response(array(
            'status'        =>     $status
        ), 403);
    }	
}