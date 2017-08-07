<?php
defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php'); // Include Rest Controller

include_once(APPPATH . '/modules/nexo/vendor/autoload.php'); // Include from Nexo module dir

include_once(APPPATH . '/modules/nexo/inc/traits_loader.php'); // Include from Nexo module dir

use Carbon\Carbon;

class Nexo extends REST_Controller
{
    use Nexo_orders,
        Nexo_items,
        Nexo_stripe,
        Nexo_cashiers,
        Nexo_rest_misc,
		Nexo_categories,
		Nexo_collection,
		Nexo_Registers,
		Nexo_customers,
		Nexo_stores,
        Nexo_Expenses,
        Nexo_coupons, // @since 3.0.1
        Nexo_notices; // @since 3.1.3

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

    private function __forbidden()
    {
        $this->response(array(
            'status'        =>    'forbidden'
        ), 403);
    }
}
