<?php

use Carbon\Carbon;
use Curl\Curl;

class ApiTablesController extends Tendoo_Api
{
    public function orders()
    {
        $this->db
        ->select( '
        aauth_users.name as AUTHOR_NAME,
        ' . store_prefix() . 'nexo_commandes.CODE as CODE,
        ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
        ' . store_prefix() . 'nexo_commandes.ID as ID,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
        ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
        ' . store_prefix() . 'nexo_commandes.DATE_MOD as DATE_MOD,
        ' . store_prefix() . 'nexo_commandes.REMISE_TYPE as REMISE_TYPE,
        ' . store_prefix() . 'nexo_commandes.REMISE_PERCENT as REMISE_PERCENT,
        ' . store_prefix() . 'nexo_commandes.GROUP_DISCOUNT as GROUP_DISCOUNT,
        ' . store_prefix() . 'nexo_commandes.SHIPPING_AMOUNT as SHIPPING_AMOUNT,
        ' . store_prefix() . 'nexo_commandes.REMISE as REMISE,
        ' . store_prefix() . 'nexo_clients.NOM as CUSTOMER_NAME,
        ' . store_prefix() . 'nexo_commandes.TITRE as TITLE,
        ' . store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_TYPE as REAL_TYPE,
        ' . store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS as STATUS,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "table_id"
        ) as TABLE_ID,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.NAME FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables
            WHERE TABLE_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.ID
        ) as TABLE_NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.REF_AREA FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables
            WHERE TABLE_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_tables.ID
        ) as AREA_ID,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas.NAME FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas
            WHERE AREA_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_restaurant_areas.ID
        ) as AREA_NAME' )
        
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( store_prefix() . 'nexo_clients', store_prefix() . 'nexo_commandes.REF_CLIENT = ' . store_prefix() . 'nexo_clients.ID' )
        ->join( store_prefix() . 'nexo_commandes_meta', store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . store_prefix() . 'nexo_commandes.ID' )
        ->join( 'aauth_users', 'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR' )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', Carbon::parse( date_now() )->startOfDay()->toDateTimeString() )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', Carbon::parse( date_now() )->endOfDay()->toDateTimeString() );
        
        $this->db->where( store_prefix() . 'nexo_commandes.TYPE !=', 'nexo_order_comptant' );
        $this->db->where_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'ready', 'collected', 'ongoing', 'partially' ]);

        $this->db->group_by( store_prefix() . 'nexo_commandes.CODE' );
        $this->db->order_by( store_prefix() . 'nexo_commandes.DATE_CREATION', 'desc' );
        
        $query    =    $this->db->order_by( store_prefix() . 'nexo_commandes.ID', 'desc' )
        ->get();

        $data   =   $query->result_array();

        if ( $data ) {
            foreach( $data as $key => $order ) {

                // ' . store_prefix() . 'nexo_articles.PRIX_DE_VENTE_TTC as PRIX_DE_VENTE_TTC,
                // ' . store_prefix() . 'nexo_articles.PRIX_DE_VENTE as PRIX_DE_VENTE,

                $this->db
                ->select('
                ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR as CODEBAR,
                ' . store_prefix() . 'nexo_articles.ID as ID,
                ' . store_prefix() . 'nexo_articles.APERCU as APERCU,
                ' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QTE_ADDED,
                ' . store_prefix() . 'nexo_commandes_produits.DISCOUNT_TYPE as DISCOUNT_TYPE,
                ' . store_prefix() . 'nexo_commandes_produits.DISCOUNT_PERCENT as DISCOUNT_PERCENT,
                ' . store_prefix() . 'nexo_commandes_produits.DISCOUNT_AMOUNT as DISCOUNT_AMOUNT,
                ' . store_prefix() . 'nexo_commandes_produits.INLINE as INLINE,
                ' . store_prefix() . 'nexo_commandes_produits.ID as COMMAND_PRODUCT_ID,
                ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX_DE_VENTE_TTC,
                ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX_DE_VENTE,
                ' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
                ' . store_prefix() . 'nexo_articles.STOCK_ENABLED as STOCK_ENABLED,
                ' . store_prefix() . 'nexo_articles.SPECIAL_PRICE_START_DATE as SPECIAL_PRICE_START_DATE,
                ' . store_prefix() . 'nexo_articles.SPECIAL_PRICE_END_DATE as SPECIAL_PRICE_END_DATE,
                ' . store_prefix() . 'nexo_articles.SHADOW_PRICE as SHADOW_PRICE,
                ' . store_prefix() . 'nexo_articles.PRIX_PROMOTIONEL as PRIX_PROMOTIONEL,
                ' . store_prefix() . 'nexo_articles.QUANTITE_RESTANTE as QUANTITE_RESTANTE,
                ' . store_prefix() . 'nexo_articles.QUANTITE_VENDU as QUANTITE_VENDU,
                ' . store_prefix() . 'nexo_articles.REF_CATEGORIE as REF_CATEGORIE,
                ' . store_prefix() . 'nexo_commandes_produits.NAME as NAME,

                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_note"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_NOTE,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_status"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_STATUS,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "meal"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as MEAL,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_issue"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as FOOD_ISSUE,
                ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
                    WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order[ 'CODE' ] . '"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "modifiers"
                    AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
                ) as MODIFIERS')
                ->from( store_prefix() . 'nexo_commandes')
                ->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
                ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.RESTAURANT_PRODUCT_REAL_BARCODE', 'left')
                ->join( store_prefix() . 'nexo_commandes_produits_meta', store_prefix() . 'nexo_commandes_produits.ID = ' . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT', 'left' )
                ->group_by( store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT' )
                ->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order[ 'CODE' ]);

                $sub_query        =    $this->db->get();

                $data[ $key ][ 'items' ]    =   $sub_query->result_array();
            }

            return response()->json( $data );
        }

        return $this->__empty();
    }

    /**
     *  get Rooms
     *  @param string int
     *  @return json
    **/

    public function tables_get( $id = null )
    {
        if( $id != null ) {
            $this->db->where( 'ID', $id );
        }

        $this->response(
            $this->db->get( store_prefix() . 'nexo_restaurant_tables' )
            ->result(),
            200
        );
    }

    /**
     *  Get Area from Rooms
     *  @param int room id
     *  @return json
    **/

    public function tables_from_area_get( $areaID )
    {
        $this->db->select(
            store_prefix() . 'nexo_restaurant_tables.NAME as TABLE_NAME,' .
            store_prefix() . 'nexo_restaurant_tables.STATUS as STATUS,' .
            store_prefix() . 'nexo_restaurant_tables.MAX_SEATS as MAX_SEATS,' .
            store_prefix() . 'nexo_restaurant_tables.CURRENT_SEATS_USED as CURRENT_SEATS_USED,' .
            store_prefix() . 'nexo_restaurant_tables.ID as TABLE_ID,' .
            store_prefix() . 'nexo_restaurant_areas.ID as AREA_ID,' .
            store_prefix() . 'nexo_restaurant_tables.SINCE as SINCE'
        )->from( store_prefix() . 'nexo_restaurant_tables' )
        ->join( store_prefix() . 'nexo_restaurant_areas', store_prefix() . 'nexo_restaurant_tables.REF_AREA = ' . store_prefix() . 'nexo_restaurant_areas.ID' )
        ->where( store_prefix() . 'nexo_restaurant_areas.ID', $areaID );

        $query  =   $this->db->get();

        $this->response( $query->result(), 200 );
    }

    /**
     *  Edit Table
     *  @param
     *  @return
    **/

    public function table_usage_put( $table_id )
    {
        $this->load->module_model( 'gastro', 'Nexo_Gastro_Tables_Models', 'gastro_model' );
        
        $result         =   get_instance()->gastro_model->table_status([
            'ORDER_ID'              =>  $this->put( 'ORDER_ID' ),
            'CURRENT_SEATS_USED'    =>  $this->put( 'CURRENT_SEATS_USED' ),
            'STATUS'                =>  $this->put( 'STATUS' ),
            'CURRENT_SESSION_ID'    =>  $this->put( 'CURRENT_SESSION_ID' ),
            'SINCE'                 =>  $this->put( 'SINCE' ),
            'TABLE_ID'              =>  $table_id
        ]);

        if( $result ) {
            return $this->response( $result );
        }
        return $this->__failed();        
    }

    /**
     * Dettache order to table
     * @param order id
     * @param table
     * @return void
    **/

    public function dettach_order_to_table( $order_id, $table_id ) 
    {
        $this->db->where( 'REF_ORDER', $order_id )
        ->where( 'TABLE_ID', $table_id )
        ->delete();
        $this->__success();
    }

    /**
     * Pay an order
     * @param int order id
     * @return json
    **/

    public function pay_order_put( $order_id )
    {
        $current_order          =    $this->db->where('ID', $order_id)
        ->get( store_prefix() . 'nexo_commandes')
        ->result_array();

        // @since 2.9 
        // @package nexopos
		// Save order payment
		$this->load->config( 'rest' );
		$Curl			=	new Curl;
        // $header_key		=	$this->config->item( 'rest_key_name' );
		// $header_value	=	$_SERVER[ 'HTTP_' . $this->config->item( 'rest_key_name' ) ];
		$Curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);

        if( is_array( $this->put( 'payments' ) ) ) {
			foreach( $this->put( 'payments' ) as $payment ) {

				$Curl->post( site_url( array( 'rest', 'nexo', 'order_payment', store_get_param( '?' ) ) ), array(
					'author'		=>	User::id(),
					'date'			=>	date_now(),
					'payment_type'	=>	$payment[ 'namespace' ],
					'amount'		=>	$payment[ 'amount' ],
					'order_code'	=>	$current_order[0][ 'CODE' ]
				) );

                // @since 3.1
                // if the payment is a coupon, then we'll increase his usage
                if( $payment[ 'namespace' ] == 'coupon' ) {
                    
                    $coupon         =   $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->get( store_prefix() . 'nexo_coupons' )
                    ->result_array();

                    $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->update( store_prefix() . 'nexo_coupons', [
                        'USAGE_COUNT'   =>  intval( $coupon[0][ 'USAGE_COUNT' ] ) + 1
                    ]);
                }
			}
        }
        
        $this->response(array(
            'order_id'          =>    $order_id,
            'order_type'        =>    $current_order[0][ 'TYPE' ],
            'order_code'        =>    $current_order[0][ 'CODE' ]
        ), 200);
    }

    public function table_order_history_get( $table_id )
    {
        $this->load->model( 'Nexo_Checkout' );
        $orders         =   $this->db->select('*,
        aauth_users.name as WAITER_NAME,
        ' . store_prefix() . 'nexo_commandes.AUTHOR as AUTHOR,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
        ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
        ' . store_prefix() . 'nexo_restaurant_tables_sessions.ID as SESSION_ID,
        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ORDER_ID
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "order_real_type"
        ) as REAL_TYPE' )
        ->from( store_prefix() . 'nexo_restaurant_tables' )
        // ->join( 
        //     store_prefix() . 'nexo_restaurant_tables_sessions', 
        //     store_prefix() . 'nexo_restaurant_tables_sessions.REF_TABLE = ' . store_prefix() . 'nexo_restaurant_tables.ID',
        //     'inner' 
        // )
        ->join( 
            store_prefix() . 'nexo_restaurant_tables_relation_orders', 
            store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_TABLE = ' . store_prefix() . 'nexo_restaurant_tables.ID' ,
            'inner'
        )
        ->join( 
            store_prefix() . 'nexo_restaurant_tables_sessions', 
            store_prefix() . 'nexo_restaurant_tables_sessions.ID = ' . store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_SESSION',
            'inner' 
        )
        ->join( 
            store_prefix() . 'nexo_commandes',
            store_prefix() . 'nexo_commandes.ID = ' . store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_ORDER',
            'inner'
        )
        ->join( 
            'aauth_users', 
            'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR' ,
            'inner'
        )
        ->join( 
            store_prefix() . 'nexo_commandes_meta', 
            store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . store_prefix() . 'nexo_commandes.ID' 
        )
        ->limit( 5 ) // 5 last orders
        ->where( store_prefix() . 'nexo_restaurant_tables_sessions.REF_TABLE', $table_id )
        ->group_by( store_prefix() . 'nexo_commandes.CODE' )
        ->order_by( store_prefix() . 'nexo_restaurant_tables_sessions.SESSION_STARTS', 'desc' )
        ->get()->result_array();

        foreach( $orders as &$order ) {
            $metas       =   $this->db->where( 'REF_ORDER_ID', $order[ 'REF_ORDER' ] )
            ->get( store_prefix() . 'nexo_commandes_meta' )
            ->result_array();
            
            if( $metas ) {
                foreach( $metas as $meta ) {
                    if( empty( @$order[ 'METAS' ] ) ) {
                        $order[ 'metas' ]   =   [];
                    }
                    
                    $order[ 'metas' ][ $meta[ 'KEY' ] ]     =   $meta[ 'VALUE' ];
                }
            }

            $order[ 'items' ]       =    $this->db
            ->select('*,
            ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX_DE_VENTE,
            ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX_DE_VENTE_TTC,
            ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR as CODEBAR,
            ' . store_prefix() . 'nexo_commandes_produits.ID as ITEM_ID,
			' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QTE_ADDED,
			' . store_prefix() . 'nexo_commandes_produits.NAME as DESIGN,
			' . store_prefix() . 'nexo_articles.DESIGN as ORIGINAL_NAME')
            ->from( store_prefix() . 'nexo_commandes')
            ->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
            ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.RESTAURANT_PRODUCT_REAL_BARCODE', 'left')
            ->where( store_prefix() . 'nexo_commandes.ID', $order[ 'REF_ORDER' ] )
            ->get()
            ->result_array();

            if( $order[ 'items' ] ) {
                foreach( $order[ 'items' ] as &$item ) {
                    $metas      =   $this->db
                    ->where( store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT', $item[ 'ITEM_ID' ] )
                    ->get( store_prefix() . 'nexo_commandes_produits_meta' )->result();
        
                    if( $metas ) {
                        $item[ 'metas' ]    =   [];
                    }
        
                    foreach( $metas as $meta ) {
                        $item[ 'metas' ][ $meta->KEY ]      =   $meta->VALUE;
                    }
                }
            }

            
        }
        return response()->json( $orders );
    }

    /**
     * Serve Food
     * @param int order id
     * @return json response 
    **/

    public function serve_post()
    {
        $order      =   $this->db->where( 'ID', $this->post( 'order_id' ) )
        ->get( store_prefix() . 'nexo_commandes' )
        ->result_array();

        if( $order ) {
            if( $order[0][ 'RESTAURANT_ORDER_STATUS' ] == 'ready' ) {
                $this->db->where( 'ID', $this->post( 'order_id' ) )
                ->update( store_prefix() . 'nexo_commandes', [
                    'RESTAURANT_ORDER_STATUS'       =>  'served'
                ]);
            }
            return $this->__success();
        }
        return $this->__failed();
    }

    /**
     * Collected Meal
     * @param void
     * @return json
    **/		  
    public function collect_meal_post()
    {
        $this->db->where( 'REF_COMMAND_PRODUCT', $this->post( 'meal_id' ) )
        ->where( 'KEY', 'restaurant_food_status' )
        ->update( store_prefix() . 'nexo_commandes_produits_meta', [
            'VALUE'     =>  'collected'
        ]);

        return $this->__success();
    }
}