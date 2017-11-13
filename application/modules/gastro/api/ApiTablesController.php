<?php
class ApiTablesController extends Api_Controller
{
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
        $order      =   $this->db->where( 'ID', $this->put( 'ORDER_ID' ) )
        ->get( store_prefix() . 'nexo_commandes' )->result_array();

        // current table
        $table      =   $this->db->where( 'ID', $table_id )
        ->get( store_prefix() . 'nexo_restaurant_tables' )->result_array();

        if( $table ) {
            $data       =   [
                'CURRENT_SEATS_USED'    =>  $this->put( 'CURRENT_SEATS_USED' ),
                'STATUS'                =>  $this->put( 'STATUS' )
            ];
    
            if( $this->put( 'STATUS' ) == 'in_use' ) {
                // if the current order status is in_use, we assume the table has been opened before
                if( $table[0][ 'STATUS' ]   == 'in_use' ) {
                    $current_session_id     =   $table[0][ 'CURRENT_SESSION_ID' ];
                } else {
                    // we're placing order for the first time
                    // create session
                    $this->db->insert( store_prefix() . 'nexo_restaurant_tables_sessions', [
                        'REF_TABLE'         =>  $table_id,
                        'SESSION_STARTS'    =>  date_now(),
                        'AUTHOR'            =>  User::id(),
                    ]);

                    // save last session id
                    $data[ 'CURRENT_SESSION_ID' ]       =   $this->db->insert_id();
                    // the table is placed for the first time
                    $data[ 'SINCE' ]        =   @$order[0][ 'DATE_MOD' ];
                    $current_session_id     =   $data[ 'CURRENT_SESSION_ID' ];
                }   
                
                // add table relation to order
                $this->db->insert( store_prefix() . 'nexo_restaurant_tables_relation_orders', [
                    'REF_ORDER'     =>  $this->put( 'ORDER_ID' ),
                    'REF_TABLE'     =>  $table_id,
                    'REF_SESSION'   =>  $current_session_id
                ]);
            } else {
                // close table session
                $this->db->where( 'ID', $table[0][ 'CURRENT_SESSION_ID' ])
                ->update( store_prefix() . 'nexo_restaurant_tables_sessions', [
                    'SESSION_ENDS'      =>  date_now(),
                    'AUTHOR'            =>  User::id(),
                ]);

                // remove last session id
                $data[ 'CURRENT_SESSION_ID' ]       =   0; // reset last session id
                $data[ 'SINCE' ]                    =   '0000-00-00 00:00:00';
            }        
    
            $this->db->where( 'ID', $table_id )->update( store_prefix() . 'nexo_restaurant_tables', $data );
            return $this->__success();
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
			' . store_prefix() . 'nexo_articles.DESIGN as DESIGN')
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
        echo json_encode( $orders );
        return;
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