<?php

defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php');

class Nexo_checkout extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->database();
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
     * Get Order
     *
     * @param Int order id
     * @return Json
    **/
    
    public function orders_get($id = null)
    {
        if ($id != null) {
            $this->db->where('ID', $id);
        }
        
        $query    =    $this->db->get('nexo_commandes');
        $this->response($query->result(), 200);
    }
    
    /**
     * Get Order Products
     *
     * @param String order code
     * @return Json code
    **/
    
    public function orders_products_get($code = null)
    {
        if ($code != null) {
            $this->db->where('REF_COMMAND_CODE', $code);
        }
        
        $query    =    $this->db->get('nexo_commandes_produits');
        $this->response($query->result(), 200);
    }
}
