<?php

defined('BASEPATH') or exit('No direct script access allowed');

! is_file(APPPATH . '/libraries/REST_Controller.php') ? die('CodeIgniter RestServer is missing') : null;

include_once(APPPATH . '/libraries/REST_Controller.php');

include_once(APPPATH . '/modules/nexo/vendor/autoload.php'); // Include from Nexo module dir

use Carbon\Carbon;

class Nexo_premium extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper( 'nexopos' );        
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
     * Orders
     *
    **/

    public function flux_post()
    {
        $query    =    $this->db
        ->where('DATE_CREATION >=', $this->post('start'))
        ->where('DATE_CREATION <=', $this->post('end'))
        ->get( store_prefix() . 'nexo_premium_factures');

        $bills    =    $query->result();

        $query    =    $this->db
        ->where('DATE_CREATION >=', $this->post('start'))
        ->where('DATE_CREATION <=', $this->post('end'))
        ->get( store_prefix() . 'nexo_commandes');

        $orders    =    $query->result();

        $this->response(array(
            'bills'        =>    $bills,
            'orders'    =>    $orders
        ), 200);
    }

    /**
     * Sale Stats
	 * @param string Year
	 * @response json
	 * @return void
    **/

    public function sales_stats_post($year)
    {
        require_once(APPPATH . 'modules/nexo/vendor/autoload.php');

        $date            =    Carbon::create($year, $this->input->post('month'));
        $startOfMonth    =    $date->copy()->startOfMonth();
        $endOfMonth        =    $date->copy()->endOfMonth();
        $complete        =    array();

        if (is_array($_POST[ 'ids' ])) {
            foreach ($_POST[ 'ids' ] as $key    =>    $_id) {

				if( $_id != '' ) {

					$this->db

					->select(
						'*,' .
						store_prefix() . 'nexo_commandes.TYPE as TYPE_COMMANDE'
					)

					->from( store_prefix() . 'nexo_commandes_produits')

					->join(
						store_prefix() . 'nexo_articles',
						store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' .
						store_prefix() . 'nexo_articles.CODEBAR'
					)

					->join(
						store_prefix() . 'nexo_categories',
						store_prefix() . 'nexo_articles.REF_CATEGORIE = ' .
						store_prefix() . 'nexo_categories.ID'
					)

					->join(
						store_prefix() . 'nexo_commandes',
						store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' .
						store_prefix() . 'nexo_commandes.CODE'
					)

					->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $startOfMonth)
					->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $endOfMonth)
					->where( store_prefix() . 'nexo_categories.ID', $_id);

					$query    =    $this->db->get();

					$complete[ $key + 1 ]    =    $query->result();

				}
            }
        }

        $this->response($complete, 200);
    }

    /**
     * Get Shipping Current Stock
     *
     * @param Int shipping id
     * @return void
    **/

    public function current_shipping_stock_post($shipping_id)
    {
        $this->response(array(), 200);
    }

    /**
     * Previous stock
     *
     * @param int shipping id
    **/

    public function previous_stock_post($shipping_id)
    {
        $this->db->select('*')
            ->from( store_prefix() . 'nexo_articles')
            ->join( store_prefix() . 'nexo_arrivages', store_prefix() . 'nexo_arrivages.ID = ' . store_prefix() . 'nexo_articles.REF_SHIPPING')
            ->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_categories.ID = ' . store_prefix() . 'nexo_articles.REF_CATEGORIE');

        $this->db->where( store_prefix() . 'nexo_arrivages.ID <', $shipping_id);

        $cats_array        =    array();

        if (isset($_POST[ 'categories_id' ]) && is_array($_POST[ 'categories_id' ])) {
            foreach ($_POST[ 'categories_id' ] as $_catid) {
                $cats_array[]    =    $_catid[ 'ID' ];
            }

            $this->db->where_in( store_prefix() . 'nexo_categories.ID', $cats_array);
        }

        $query    =    $this->db->get();

        $this->response($query->result(), 200);
    }

    /**
     * Load Current Stock
     *
     * @param int shipping id
     * @echo json
    **/

    public function current_stock_post($shipping_id)
    {
        $this->db->select('*')
        ->from( store_prefix() . 'nexo_articles')
        ->join( store_prefix() . 'nexo_arrivages', store_prefix() . 'nexo_arrivages.ID = ' . store_prefix() . 'nexo_articles.REF_SHIPPING')
        ->join( store_prefix() . 'nexo_articles_stock_flow', store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE = ' . store_prefix() . 'nexo_articles.CODEBAR')
        ->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_categories.ID = ' . store_prefix() . 'nexo_articles.REF_CATEGORIE');

        $this->db->where( store_prefix() . 'nexo_arrivages.ID =', $shipping_id);

        $cats_array        =    array();

        if (isset($_POST[ 'categories_id' ]) && is_array($_POST[ 'categories_id' ])) {
            foreach ($_POST[ 'categories_id' ] as $_catid) {
                $cats_array[]    =    $_catid[ 'ID' ];
            }

            $this->db->where_in( store_prefix() . 'nexo_categories.ID', $cats_array);
        }

        $query    =    $this->db->get();

        $this->response($query->result(), 200);
    }

    /**
     * Run Restore Query
    **/

    public function run_restore_query_get($index, $table_prefix = '')
    {
        $table_prefix     = $table_prefix == '' ? $this->db->dbprefix : $table_prefix;

        $Cache            =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_' . store_prefix() ));
        $Query            =    $Cache->get('restore_queries');
        if (@$Query[ $index - 1 ] != null) {
            $SQL            =    str_replace($table_prefix, $this->db->dbprefix, $Query[ $index - 1 ]);
            $SQL            =    str_replace(array('.', "\n", "\t", "\r", ',/' ), '', $SQL);
            $this->db->query($SQL);
            $this->response($SQL, 200);
        }
    }

    /**
     * Best Of
     *
     * Items
    **/

    public function best_items_post()
    {
        $this->config->load('nexo_premium');
        $this->load->model('Options');
        $this->load->model('Nexo_Misc');

        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_' . store_prefix() ));

        if ($cached    =    $Cache->get('best_items_post_' . $this->post('start') . '_' . $this->post('end')) && $this->input->get( 'disable_cache' ) != 'true' ) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');

            $Dates          =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options        =    $this->Options->get();
            $CashOrder      =    $this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ]);
            $Response       =    array();

            if (! empty($Dates)) {

                $items_sales    =    array();

                foreach ($Dates as $Date) {
                    $StartDay       =       Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay         =       Carbon::parse($Date)->endOfDay()->toDateTimeString();

                    $this->db->select('
					' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
					' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					' . store_prefix() . 'nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
					' . store_prefix() . 'nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
					' . store_prefix() . 'nexo_articles.ID as ITEM_ID,
					' . store_prefix() . 'nexo_commandes.DATE_CREATION as SOLD_DATE,
                    ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX,
                    ' . store_prefix() . 'nexo_commandes.REMISE_TYPE as REMISE_TYPE,
                    ' . store_prefix() . 'nexo_commandes.REMISE as REMISE,
                    ' . store_prefix() . 'nexo_commandes.REMISE_PERCENT as REMISE_PERCENT
                    ')
                    ->from( store_prefix() . 'nexo_commandes_produits')
                    ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' . store_prefix() . 'nexo_articles.CODEBAR')
                    ->join( store_prefix() . 'nexo_commandes', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE');

                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by( store_prefix() . 'nexo_articles.QUANTITE_VENDU', 'DESC');

                    foreach( $CashOrder as $order) {
                        $this->db->or_where( store_prefix() . 'nexo_commandes.TYPE', $order);
                    }

                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $EndDay);

                    $this->db->limit( $this->config->item('best_of_items_limit') );

                    $query                    =    $this->db->get();
                    $items_sales[ $Date ]    =    $query->result();
                }

                $Response            =    array(
                    'dates_between_borders'     =>    $Dates,
                    'items_sales'               =>    $items_sales
                );

                $Cache->save( 'best_items_post_' . $this->post('start') . '_' . $this->post('end'), $Response, $this->config->item('best_of_cache_lifetime'));

                $this->response($Response, 200);
            }
        }
    }

    /**
     * Best Of
     *
     * categories
    **/

    public function best_categories_post()
    {
        $this->config->load('nexo_premium');
        $this->load->model('Options');
        $this->load->model('Nexo_Misc');

        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_' . store_prefix() ) );

        if ( $cached    =    $Cache->get('best_categories_post_' . $this->post('start') . '_' . $this->post('end')) && $this->input->get( 'disable_cache' ) != 'true' ) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');

            $Dates              =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options            =    $this->Options->get();
            $CashOrder          =    $this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ]);

            $Response           =    array();

            if (! empty($Dates)) {
                $items_sales    =    array();

                foreach ($Dates as $Date) {
                    $StartDay               =        Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay                 =        Carbon::parse($Date)->endOfDay()->toDateTimeString();

                    // Here categories takes item_as and item use product_id instead.
                    $this->db->select('
					' . store_prefix() . 'nexo_categories.NOM as DESIGN,
					' . store_prefix() . 'nexo_categories.ID as ITEM_ID,
					' . store_prefix() . 'nexo_articles.DESIGN as ITEM_NAME,
					' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					' . store_prefix() . 'nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
					' . store_prefix() . 'nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
                    ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX,
					' . store_prefix() . 'nexo_articles.ID as PRODUCT_ID,
					' . store_prefix() . 'nexo_commandes.DATE_CREATION as SOLD_DATE')
                    ->from( store_prefix() . 'nexo_commandes_produits')
                    ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' . store_prefix() . 'nexo_articles.CODEBAR')
                    ->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_categories.ID = ' . store_prefix() . 'nexo_articles.REF_CATEGORIE')
                    ->join( store_prefix() . 'nexo_commandes', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE');

                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by( store_prefix() . 'nexo_articles.QUANTITE_VENDU', 'DESC');

                    foreach( $CashOrder as $order ) {
                        $this->db->or_where( store_prefix() . 'nexo_commandes.TYPE', $order );
                    }                    

                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $EndDay);

                    $this->db->limit($this->config->item('best_of_items_limit'));

                    $query                    =    $this->db->get();
                    $items_sales[ $Date ]    =    $query->result();
                }

                $Response            =    array(
                    'dates_between_borders'    =>    $Dates,
                    'items_sales'            =>    $items_sales
                );

                $Cache->save('best_categories_post_' . $this->post('start') . '_' . $this->post('end'), $Response, $this->config->item('best_of_cache_lifetime'));

                $this->response($Response, 200);
            }
        }
    }

    /**
     * Best Of Shipping
     *
    **/

    public function best_shippings_post()
    {
        $this->config->load('nexo_premium');
        $this->load->model('Options');
        $this->load->model('Nexo_Misc');

        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_' . store_prefix() ) );

        if ($cached    =    $Cache->get('best_shippings_post_' . $this->post('start') . '_' . $this->post('end')) && $this->input->get( 'disable_cache' ) != 'true' ) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');

            $Dates            =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options        =    $this->Options->get();
            $CashOrder          =    $this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ]);
            $Response        =    array();

            if (! empty($Dates)) {
                $items_sales    =    array();

                foreach ($Dates as $Date) {
                    $StartDay            =        Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay                =        Carbon::parse($Date)->endOfDay()->toDateTimeString();

                    // Here categories takes item_as and item use product_id instead.
                    $this->db->select('
					' . store_prefix() . 'nexo_arrivages.TITRE as DESIGN,
					' . store_prefix() . 'nexo_arrivages.ID as ITEM_ID,
					' . store_prefix() . 'nexo_articles.DESIGN as ITEM_NAME,
					' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					' . store_prefix() . 'nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
                    ' . store_prefix() . 'nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
					' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX,
					' . store_prefix() . 'nexo_articles.ID as PRODUCT_ID,
					' . store_prefix() . 'nexo_commandes.DATE_CREATION as SOLD_DATE')

                    ->from( store_prefix() . 'nexo_commandes_produits')
                    ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' . store_prefix() . 'nexo_articles.CODEBAR')
                    ->join( store_prefix() . 'nexo_arrivages', store_prefix() . 'nexo_arrivages.ID = ' . store_prefix() . 'nexo_articles.REF_SHIPPING')
                    ->join( store_prefix() . 'nexo_commandes', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE');

                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by( store_prefix() . 'nexo_articles.QUANTITE_VENDU', 'DESC');

                    foreach( $CashOrder as $order) {
                        $this->db->or_where( store_prefix() . 'nexo_commandes.TYPE', $order);
                    }

                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $EndDay);

                    $this->db->limit($this->config->item('best_of_items_limit'));

                    $query                    =    $this->db->get();
                    $items_sales[ $Date ]    =    $query->result();
                }

                $Response            =    array(
                    'dates_between_borders'    =>    $Dates,
                    'items_sales'            =>    $items_sales
                );

                $Cache->save('best_shippings_post_' . $this->post('start') . '_' . $this->post('end'), $Response, $this->config->item('best_of_cache_lifetime'));

                $this->response($Response, 200);
            }
        }
    }

	/**
	 * Load Expenses.
	**/

	public function expenses_get()
	{
		$this->response( $this->db->get( store_prefix() . 'nexo_premium_factures' )->result() );
	}
}
