<?php

defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php');

class Nexo_categories extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->database();
		$this->load->helper( 'nexopos' );
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
     * Category Get
     *
     * @param Int category id
     * @return json
    **/
    
    public function get_get($mixed = null, $filter = 'id')
    {
        if ($filter == 'id' && $mixed != null) {
            $this->db->where('ID', $mixed);
        }
        
        $query    =    $this->db->get( store_prefix() . 'nexo_categories');
        $this->response($query->result(), 200);
    }
}
