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
        ->get('nexo_premium_factures');
        
        $bills    =    $query->result();
        
        $query    =    $this->db
        ->where('DATE_CREATION >=', $this->post('start'))
        ->where('DATE_CREATION <=', $this->post('end'))
        ->get('nexo_commandes');
        
        $orders    =    $query->result();
        
        $this->response(array(
            'bills'        =>    $bills,
            'orders'    =>    $orders
        ), 200);
    }
    
    /**
     * Sale Stats
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
                $this->db
                ->select('*,
				nexo_commandes.TYPE as TYPE_COMMANDE')
                ->from('nexo_commandes_produits')
                ->join('nexo_articles', 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = nexo_articles.CODEBAR')
                ->join('nexo_categories', 'nexo_articles.REF_CATEGORIE = nexo_categories.ID')
                ->join('nexo_commandes', 'nexo_commandes_produits.REF_COMMAND_CODE = nexo_commandes.CODE')
                ->where('nexo_commandes.DATE_CREATION >=', $startOfMonth)
                ->where('nexo_commandes.DATE_CREATION <=', $endOfMonth)
                ->where('nexo_categories.ID', $_id);
                
                $query    =    $this->db->get();
                
                $complete[ $key + 1 ]    =    $query->result();
            }
        }
        
        $this->response($complete, 200);
    }
    
    /**
     * Get Shipping Current Stock
     *
     * @params Int shipping id
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
            ->from('nexo_articles')
            ->join('nexo_arrivages', 'nexo_arrivages.ID = nexo_articles.REF_SHIPPING')
            ->join('nexo_categories', 'nexo_categories.ID = nexo_articles.REF_CATEGORIE');
                
        $this->db->where('nexo_arrivages.ID <', $shipping_id);
        
        $cats_array        =    array();
        
        if (isset($_POST[ 'categories_id' ]) && is_array($_POST[ 'categories_id' ])) {
            foreach ($_POST[ 'categories_id' ] as $_catid) {
                $cats_array[]    =    $_catid[ 'ID' ];
            }
            
            $this->db->where_in('nexo_categories.ID', $cats_array);
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
            ->from('nexo_articles')
            ->join('nexo_arrivages', 'nexo_arrivages.ID = nexo_articles.REF_SHIPPING')
            ->join('nexo_categories', 'nexo_categories.ID = nexo_articles.REF_CATEGORIE');
                
        $this->db->where('nexo_arrivages.ID =', $shipping_id);
        
        $cats_array        =    array();
        
        if (isset($_POST[ 'categories_id' ]) && is_array($_POST[ 'categories_id' ])) {
            foreach ($_POST[ 'categories_id' ] as $_catid) {
                $cats_array[]    =    $_catid[ 'ID' ];
            }
            
            $this->db->where_in('nexo_categories.ID', $cats_array);
        }
        
        $query    =    $this->db->get();
        
        $this->response($query->result(), 200);
    }
    
    /**
     * Run Restore Query
    **/
    
    public function run_restore_query_get($index, $table_prefix = '')
    {
		$table_prefix	 = $table_prefix == '' ? $this->db->dbprefix : $table_prefix;
		
        $Cache            =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_'));
        $Query            =    $Cache->get('restore_queries');
        if (@$Query[ $index - 1 ] != null) {
			$SQL			=	str_replace( $table_prefix, $this->db->dbprefix, $Query[ $index - 1 ] );
			$SQL			=	str_replace(array('.', "\n", "\t", "\r", ',/' ), '', $SQL);
            $this->db->query( $SQL );
            $this->response( $SQL, 200);
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
        
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_'));
        
        if ($cached    =    $Cache->get('best_items_post_' . $this->post('start') . '_' . $this->post('end'))) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');
            
            $Dates                =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options            =    $this->Options->get();
            $CashOrder        =    'nexo_order_comptant';
            $Response            =    array();
            
            if (! empty($Dates)) {
                $items_sales    =    array();
                
                foreach ($Dates as $Date) {
                    $StartDay            =        Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay                =        Carbon::parse($Date)->endOfDay()->toDateTimeString();
                    
                    $this->db->select('
					nexo_articles.DESIGN as DESIGN,
					nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
					nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
					nexo_articles.ID as ITEM_ID,
					nexo_commandes.DATE_CREATION as SOLD_DATE')
                    ->from('nexo_commandes_produits')
                    ->join('nexo_articles', 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = nexo_articles.CODEBAR')
                    ->join('nexo_commandes', 'nexo_commandes.CODE = nexo_commandes_produits.REF_COMMAND_CODE');
                    
                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by('nexo_articles.QUANTITE_VENDU', 'DESC');
                    
                    $this->db->where('nexo_commandes.TYPE', $CashOrder);
                    
                    $this->db->where('nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where('nexo_commandes.DATE_CREATION <=', $EndDay);
                    
                    $this->db->limit($this->config->item('best_of_items_limit'));
                    
                    $query                    =    $this->db->get();
                    $items_sales[ $Date ]    =    $query->result();
                }
                
                $Response            =    array(
                    'dates_between_borders'    =>    $Dates,
                    'items_sales'            =>    $items_sales
                );
                
                $Cache->save('best_items_post_' . $this->post('start') . '_' . $this->post('end'), $Response, $this->config->item('best_of_cache_lifetime'));
            
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
        
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_'));
        
        if ($cached    =    $Cache->get('best_categories_post_' . $this->post('start') . '_' . $this->post('end'))) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');
            
            $Dates            =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options        =    $this->Options->get();
            $CashOrder        =    'nexo_order_comptant';
            $Response        =    array();
            
            if (! empty($Dates)) {
                $items_sales    =    array();
                
                foreach ($Dates as $Date) {
                    $StartDay            =        Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay                =        Carbon::parse($Date)->endOfDay()->toDateTimeString();
                    
                    // Here categories takes item_as and item use product_id instead.
                    $this->db->select('
					nexo_categories.NOM as DESIGN,
					nexo_categories.ID as ITEM_ID,
					nexo_articles.DESIGN as ITEM_NAME,
					nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
					nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
					nexo_articles.ID as PRODUCT_ID,
					nexo_commandes.DATE_CREATION as SOLD_DATE')
                    ->from('nexo_commandes_produits')
                    ->join('nexo_articles', 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = nexo_articles.CODEBAR')
                    ->join('nexo_categories', 'nexo_categories.ID = nexo_articles.REF_CATEGORIE')
                    ->join('nexo_commandes', 'nexo_commandes.CODE = nexo_commandes_produits.REF_COMMAND_CODE');
                    
                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by('nexo_articles.QUANTITE_VENDU', 'DESC');
                    
                    $this->db->where('nexo_commandes.TYPE', $CashOrder);
                    
                    $this->db->where('nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where('nexo_commandes.DATE_CREATION <=', $EndDay);
                    
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
        
        $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_premium_'));
        
        if ($cached    =    $Cache->get('best_categories_post_' . $this->post('start') . '_' . $this->post('end'))) {
            $this->response($cached, 200);
        } else {
            $this->config->load('nexo_premium');
            
            $Dates            =    $this->Nexo_Misc->dates_between_borders($this->post('start'), $this->post('end'));
            $Options        =    $this->Options->get();
            $CashOrder        =    'nexo_order_comptant';
            $Response        =    array();
            
            if (! empty($Dates)) {
                $items_sales    =    array();
                
                foreach ($Dates as $Date) {
                    $StartDay            =        Carbon::parse($Date)->startOfDay()->toDateTimeString();
                    $EndDay                =        Carbon::parse($Date)->endOfDay()->toDateTimeString();
                    
                    // Here categories takes item_as and item use product_id instead.
                    $this->db->select('
					nexo_arrivages.TITRE as DESIGN,
					nexo_arrivages.ID as ITEM_ID,
					nexo_articles.DESIGN as ITEM_NAME,
					nexo_commandes_produits.QUANTITE as QUANTITE_UNIQUE_VENDUE,
					nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
					nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,
					nexo_articles.ID as PRODUCT_ID,
					nexo_commandes.DATE_CREATION as SOLD_DATE')
                    ->from('nexo_commandes_produits')
                    ->join('nexo_articles', 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = nexo_articles.CODEBAR')
                    ->join('nexo_arrivages', 'nexo_arrivages.ID = nexo_articles.REF_SHIPPING')
                    ->join('nexo_commandes', 'nexo_commandes.CODE = nexo_commandes_produits.REF_COMMAND_CODE');
                    
                    // $this->db->group_by( 'nexo_articles.CODEBAR' );

                    $this->db->order_by('nexo_articles.QUANTITE_VENDU', 'DESC');
                    
                    $this->db->where('nexo_commandes.TYPE', $CashOrder);
                    
                    $this->db->where('nexo_commandes.DATE_CREATION >=', $StartDay);
                    $this->db->where('nexo_commandes.DATE_CREATION <=', $EndDay);
                    
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
}
