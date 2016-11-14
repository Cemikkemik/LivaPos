<?php

defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php'); // Include Rest Controller

include_once(APPPATH . '/modules/nexo/vendor/autoload.php'); // Include from Nexo module dir

use Carbon\Carbon;

class Nexo extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
		$this->load->helper( 'nexopos' );
        $this->load->database();
    }

    /**
     * Get Post Item
     *
    **/

    public function compare_item_post($filter, $exclude_id = 'add')
    {
        $this->db->where($filter, $this->post('filter'));
        
        if ($exclude_id != 'add') {
            $this->db->where('ID !=', $exclude_id);
        }
                            
        $result            =    $this->db->get('nexo_articles')->result();

        $result        ?    $this->response($result, 200)  : $this->__empty();
    }

    /**
     * Get item
     *
    **/

    public function item_get($id = null, $filter = 'ID')
    {
        if ($id != null && $filter != 'sku-barcode') {
            $result        =    $this->db->where($filter, $id)->get('nexo_articles')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } elseif ($filter == 'sku-barcode') {
            $result        =    $this->db
                                ->where('CODEBAR', $id)
                                ->or_where('SKU', $id)
                                ->get('nexo_articles')
                                ->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } else {
            $this->db->select('*,
			nexo_articles.ID as ID,
			nexo_categories.ID as CAT_ID
			')
            ->from('nexo_articles')
            ->join('nexo_categories', 'nexo_articles.REF_CATEGORIE = nexo_categories.ID');
            $this->response($this->db->get()->result());
        }
    }

    /**
     * Delete Item from Shop
     *
    **/

    public function item_delete($id = null)
    {
        if ($id == null) {
            $this->response(array(
                'status' => 'failed'
            ));
        } else {
            $this->db->where('ID', $id)->delete('nexo_articles')->result();

            $this->response(array(
                'status' => 'failed'
            ));
        }
    }

    /**
     * PUt item
	 * @deprecated
     *
    **/

    public function item_put()
    {
        $request    =    $this->db->where($this->put('id'))
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->update('nexo_articles');

        if ($request) {
            $this->response(array(
                'status'        =>        'success'
            ), 200);
        } else {
            $this->response(array(
                'status'        =>        'error'
            ), 404);
        }
    }

    /**
     * Item insert
	 * @deprecated
    **/

    public function item_post()
    {
        $request    =    $this->db
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->insert('nexo_articles');

        if ($request) {
            $this->response(array(
                'status'        =>        'success'
            ), 200);
        } else {
            $this->response(array(
                'status'        =>        'error'
            ), 404);
        }
    }

    /**
     * Customers
    **/

    public function customer_get($id = null, $filter = 'ID')
    {
        if ($id != null) {
            $result        =    $this->db->where($filter, $id)->get('nexo_clients')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 500);
        } else {
            $this->response($this->db->get('nexo_clients')->result());
        }
    }

    /**
     * Customer Insert
     *
     * @param POST string name
     * @param POST string email
     * @param POST string tel
     * @param POST string prenom
	 * @deprecated
    **/

    public function customer_post()
    {
        $request    =    $this->db
        ->set('NOM',    $this->post('nom'))
        ->set('EMAIL',    $this->post('email'))
        ->set('TEL',    $this->post('tel'))
        ->set('PRENOM',    $this->post('prenom'))
        ->set('REF_GROUP', $this->post('ref_group'))
        ->set('AUTHOR', $this->post('author'))
        ->set('DATE_CREATION', $this->post('date_creation'))
        ->insert('nexo_clients');

        if ($request) {
            $this->response(array(
                'status'        =>        'success'
            ), 200);
        } else {
            $this->response(array(
                'status'        =>        'error'
            ), 404);
        }
    }

    /**
     * MISC PARTS
    **/

    /**
     * Reset shop
    **/

    public function reset_post()
    {
        $this->load->model('Nexo_Misc');
        $this->Nexo_Misc->empty_shop();

        $this->response(array(
            'status'        =>        'success'
        ), 200);
    }

    /**
     * Demo data
    **/

    public function demo_post()
    {
        include_once(APPPATH . '/libraries/User.php');
        $this->load->library('Aauth', array(), 'auth');
        $this->load->model('Nexo_Misc');
        $this->Nexo_Misc->enable_demo();

        $this->response(array(
            'status'        =>        'success'
        ), 200);
    }

    /**
     * Feed Get
     * @since 2.5.5
    **/

    public function feed_get()
    {
        $this->config->load('nexo');
        // Set max execution Time
        set_time_limit($this->config->item('feed_execution_time '));

        // Fetch from cache
        $this->cache        =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'nexo_' . store_prefix() ));

        if ($this->cache->get('tutorials_feed')) {
            $content        =    json_decode($this->cache->get('tutorials_feed'));
        } else {
            $xml = file_get_contents('http://nexo.tendoo.org/category/tutorials/feed');
            $x = new SimpleXmlElement($xml);
            $content        =    $x->channel;
            $this->cache->save('tutorials_feed', json_encode($x->channel), 43200);
        }

        $this->response($content, 200);
    }

    /**
     * News Feed Get
     * @since 2.5.5
    **/

    public function news_feed_get()
    {
        $this->load->config('nexo');
        // Set max execution Time
        set_time_limit($this->config->item('feed_execution_time '));

        $this->cache        =    new CI_Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'nexo_' . store_prefix() ));

        if ($this->cache->get('news_feed')) {
            $content        =    json_decode($this->cache->get('news_feed'));
        } else {
            $xml = file_get_contents('http://nexo.tendoo.org/category/news/feed');
            $x = new SimpleXmlElement($xml);
            $content        =    $x->channel;
            $this->cache->save('news_feed', json_encode($x->channel), 43200);
        }

        $this->response($content, 200);
    }

    /**
     * Customer Groups
     * @param int/string group par
     * @return json
    **/

    public function customers_groups_get($id = null, $filter = 'id')
    {
        if ($id != null) {
            $this->db->where('ID', $id);
        }

        $query    =    $this->db->get('nexo_clients_groups');
        $this->response($query->result(), 200);
    }

    /**
     * Customer Groups Post
     * @param String name
     * @param String Description
     * @param Int author
     * @return void
    **/

    public function customers_groups_post()
    {
        $this->db->insert('nexo_clients_groups', array(
            'NAME'            =>    $this->post('name'),
            'DESCRIPTION'    =>    $this->post('descirption'),
            'DATE_CREATION'    =>    date_now(),
            'AUTHOR'        =>    $this->post('user_id')
        ));

        $this->__success();
    }

    /**
     * Customer Groupe delete
     * @param Int group id
     * @return json
     *
    **/

    public function customers_groups_delete($id)
    {
        if ($this->db->where('ID', $id)->delete('nexo_clients_groups')) {
            $this->__failed();
        } else {
            $this->__success();
        }
    }

    /**
     * Customer edit
     * @param Int group id
     * @return json
    **/

    public function customers_groups_update($group_id)
    {
        if ($this->where('ID', $group_id)->update('nexo_clients_groups', array(
            'NAME'                =>    $this->put('name'),
            'DESCRIPTION'        =>    $this->put('description'),
            'AUTHOR'            =>    $this->put('user_id'),
            'DATE_MODIFICATION'    =>    date_now()
        ))) {
            $this->__success();
        } else {
            $this->__failed();
        }
    }

    /**
     * Cashier Performance
     * @param string filter
     * @param int cashier id
     * @param string start date
     * @param string end date
     * @return json
    **/

    public function cashier_performance_post($filter = 'by-days', $start_date = null, $end_date = null)
    {
        $CarbonStart    =    Carbon::parse($start_date);
        $CarbonEnd        =    Carbon::parse($end_date);

        if ($filter == 'by-days') {
            if (
                $CarbonStart->lt($CarbonEnd)
                && $CarbonStart->diffInDays($CarbonEnd) >= 7
                && $CarbonStart->diffInMonths($CarbonEnd) < 4 // report can't exceed 3 months
            ) {
                $Dates        =    array();
                $i = 0;

                while ($CarbonStart->toDateTimeString() != $CarbonEnd->copy()->addDay()->toDateTimeString()) {
                    $Dates[ $CarbonStart->toDateTimeString() ]    =    array();
                    $CarbonStart->addDay();
                }

                // Fetching Sales for current cashier
                $cashier_id        =    $this->input->post('cashier_id');

                foreach ($Dates as $date_key => &$content) {
                    if (is_array($cashier_id)) {
                        foreach ($cashier_id as $id) {
                            $this->db->select('*')
                            ->from( store_prefix() . 'nexo_commandes')
                            ->join('aauth_users', store_prefix() . 'nexo_commandes.AUTHOR = aauth_users.id')
                            ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', Carbon::parse($date_key)->startOfDay())
                            ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', Carbon::parse($date_key)->endOfDay());

                            $this->db->where('aauth_users.id', $id);

                            $query                            =    $this->db->get();
                            $content[ 'cashiers' ][ $id ]    =    $query->result_array();
                        }
                    }
                }


                $this->response($Dates, 200);
            }

            $this->response(array(
                'error'        =>    'insufficient_data'
            ), 200);
        } elseif ($filter == 'by-months') { // doesn't yet support multi id

            if (
                $CarbonStart->lt($CarbonEnd)
                && $CarbonStart->diffInMonths($CarbonEnd) >= 2
                && $CarbonStart->diffInYears($CarbonEnd) < 2 // report can't exceed 3 months
            ) {
                $Dates        =    array();

                while ($CarbonStart->startOfMonth()->toDateTimeString() != $CarbonEnd->copy()->startOfMonth()->addMonth()->toDateTimeString()) {
                    $Dates[ $CarbonStart->startOfMonth()->toDateTimeString() ]    =    array();
                    $CarbonStart->startOfMonth()->addMonth();
                }

                // Fetching Sales for current cashier

                foreach ($Dates as $date_key => &$content) {
                    $this->db->select('*')
                    ->from( store_prefix() . 'nexo_commandes')
                    ->join('aauth_users', store_prefix() . 'nexo_commandes.AUTHOR = aauth_users.id')
                    ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', Carbon::parse($date_key)->startOfMonth())
                    ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', Carbon::parse($date_key)->endOfMonth())
                    ->where('aauth_users.id', $cashier_id);

                    $query        =    $this->db->get();
                    $content    =    $query->result_array();
                }

                $this->response($Dates, 200);
            }

            $this->response(array(
                'error'        =>    'insufficient_data'
            ), 200);
        }
    }

    /**
     * Customer performance
     *
    **/

    public function customer_statistics_post($start_date = null, $end_date = null)
    {
        $CarbonStart    =    Carbon::parse($start_date);
        $CarbonEnd        =    Carbon::parse($end_date);

        if (
            $CarbonStart->lt($CarbonEnd)
            && $CarbonStart->diffInMonths($CarbonEnd) >= 1
            && $CarbonStart->diffInYears($CarbonEnd) < 1 // report can't exceed 3 months
        ) {
            $Dates        =    array();

            while ($CarbonStart->startOfMonth()->toDateTimeString() != $CarbonEnd->copy()->startOfMonth()->addMonth()->toDateTimeString()) {
                $Dates[ $CarbonStart->startOfMonth()->toDateTimeString() ]    =    array();
                $CarbonStart->startOfMonth()->addMonth();
            }

            // Fetching Sales for current customer
            $customers_id        =    $this->input->post('customer_id');

            foreach ($Dates as $date_key => &$content) {
                if (is_array($customers_id)) {
                    foreach ($customers_id as $customer_id) {
                        $this->db->select('*')
                        ->from( store_prefix() . 'nexo_clients')
                        
						->join( 
							store_prefix() . 'nexo_commandes', 
							store_prefix() . 'nexo_commandes.REF_CLIENT = ' . 
							store_prefix() . 'nexo_clients.ID', 'inner'
						)
						
                        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', Carbon::parse($date_key)->startOfMonth()->toDateTimeString())
                        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', Carbon::parse($date_key)->endOfMonth()->toDateTimeString())
                        ->where( store_prefix() . 'nexo_clients.ID', $customer_id);

                        $query        =    $this->db->get();
                        $content[ 'customers' ][ $customer_id ]        =    $query->result_array();
                    }
                }
            }

            $this->response($Dates, 200);
        } else {
            $this->__failed();
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
     * Display empty
     *
     * @return json status
    **/

    private function __empty()
    {
        $this->response(array(), 200);
    }
}
