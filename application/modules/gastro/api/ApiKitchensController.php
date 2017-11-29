<?php
use Carbon\Carbon;
use Dompdf\Dompdf;

class ApiKitchensController extends Tendoo_Api
{
    public function orders()
    {
        // get order id where items belongs to a specific category
        $kitchen        =   $this->db->where( 'ID', @$_GET[ 'current_kitchen' ] )
        ->get( store_prefix() . 'nexo_restaurant_kitchens' )
        ->result_array();

        // if( ! $kitchen ) {
        //     echo json_encode([
        //         'message'       =>  __( 'Unable to locate the kitchen', 'gastro' ),
        //         'status'        =>  'failed'
        //     ]);
        //     return;
        // }

        // check if kitchen listen to specific categories
        $categories             =   @$kitchen[0][ 'REF_CATEGORY' ];
        $categories_ids         =   [];
        $filtred_order_ids      =   [];
        $filtred_item_ids       =   [];

        if( ! empty( $categories ) ) {
            $categories_ids         =   explode( ',', $categories );
        }

        if( ! empty( $categories_ids ) ) {
            $orders         =   $this->db
            ->select( '*,
            ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
            ' . store_prefix() . 'nexo_commandes_produits.ID as ITEM_ID' )
            ->from( store_prefix() . 'nexo_commandes' )
            ->join( 
                store_prefix() . 'nexo_commandes_produits', 
                store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . 
                store_prefix() . 'nexo_commandes.CODE' 
            )
            ->join( 
                store_prefix() . 'nexo_articles', 
                store_prefix() . 'nexo_articles.CODEBAR = ' . 
                store_prefix() . 'nexo_commandes_produits.RESTAURANT_PRODUCT_REAL_BARCODE' 
            )
            ->where_in( 'REF_CATEGORIE', $categories_ids );

            if( @$_GET[ 'from-kitchen' ] ) {
                $this->db
                ->where_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'pending', 'ongoing', 'partially' ] );
            } else {
                $this->db
                /** ->where_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'ready', 'collected' ] ) **/
                ->where( store_prefix() . 'nexo_commandes.TYPE !=', 'nexo_order_comptant' );
            }

            $orders        =   $this->db            
            ->get()
            ->result_array();

            // keep order ids
            if( $orders ) {
                foreach( $orders as $order ) {
                    $filtred_order_ids[]     =   $order[ 'ORDER_ID' ];
                    $filtred_item_ids[]      =   $order[ 'ITEM_ID' ];
                }

                $filtred_order_ids       =   array_unique( $filtred_order_ids );
                $filtred_item_ids        =   array_unique( $filtred_item_ids );
            }
        } 

        if( $filtred_order_ids || @$_GET[ 'from-kitchen' ] == 'true' ) {
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

            if( $filtred_order_ids ) {
                $this->db->where_in( store_prefix() . 'nexo_commandes.ID', $filtred_order_ids );
            } else if( @$_GET[ 'from-kitchen' ] == 'true' ) {
                $this->db->where_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'pending', 'ongoing', 'partially' ]);
            } else {
                $this->db->where( store_prefix() . 'nexo_commandes.TYPE !=', 'nexo_order_comptant' );
                $this->db->where_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'ready', 'collected' ]);
            }

            // $this->db->or_where( '( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta
            //     WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.ID
            //     AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.DATE_CREATION >= "' . Carbon::parse( date_now() )->startOfDay()->toDateTimeString() . '"
            //     AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes.DATE_CREATION <= "' . Carbon::parse( date_now() )->endOfDay()->toDateTimeString() . '"
            //     AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_meta.KEY = "order_real_type"
            // ) = "takeaway"' );

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
                    
                    // if some items has to be filtred
                    if( $filtred_item_ids ) {
                        $this->db->where_in( store_prefix() . 'nexo_commandes_produits.ID', $filtred_item_ids );
                    }

                    $sub_query        =    $this->db->get();

                    $data[ $key ][ 'items' ]    =   $sub_query->result_array();
                }

                return response()->json( $data );
            }
        }
    
        return $this->__empty();
    }  

    /**
     *  Start Cook
     *  @param
     *  @return
    **/

    public function start_cooking_post()
    {
        $this->db->where( 'CODE', $this->post( 'order_code' ) )
        ->update( store_prefix() . 'nexo_commandes', [
            'TYPE'      =>  'nexo_order_dinein_ongoing'
        ]);

        foreach( $this->post( 'during_cooking' ) as $item_id ) {
            $this->db
            ->where( 'REF_COMMAND_PRODUCT', $item_id )
            ->where( 'KEY', 'restaurant_food_status' )
            ->update( store_prefix() . 'nexo_commandes_produits_meta', [
                'VALUE'   =>    'in_preparation'
            ]);
        }
    }

    /**
     *  Are Ready, change food state
     *  @param void
     *  @return json
    **/

    public function food_state_post()
    {
        // $types          =   [];
        // foreach( [ 'takeaway', 'dinein', 'delivery' ] as $type ) {
        //     $types[ $type ][ 'pending' ]        =   'nexo_order_' . $type . '_pending';
        //     $types[ $type ][ 'ongoing' ]        =   'nexo_order_' . $type . '_ongoing';
        //     $types[ $type ][ 'partially' ]        =   'nexo_order_' . $type . '_partially';
        //     $types[ $type ][ 'ready' ]        =   'nexo_order_' . $type . '_ready';
        //     $types[ $type ][ 'incomplete' ]        =   'nexo_order_' . $type . '_incomplete';
        //     $types[ $type ][ 'canceled' ]        =   'nexo_order_' . $type . '_canceled';
        //     $types[ $type ][ 'denied' ]        =   'nexo_order_' . $type . '_denied';
        // }

        // $current        =   $this->post( 'order_real_type' );

        foreach( $this->post( 'selected_foods' ) as $item_id ) {
            $this->db
            ->where( 'REF_COMMAND_PRODUCT', $item_id )
            ->where( 'KEY', 'restaurant_food_status' )
            ->update( store_prefix() . 'nexo_commandes_produits_meta', [
                'VALUE'   =>    $this->post( 'state' )
            ]);
        }

        $order_foods     =   $this->db
        ->where( 'REF_COMMAND_CODE', $this->post( 'order_code' ) )
        ->where( 'KEY', 'restaurant_food_status' )
        ->get( store_prefix() . 'nexo_commandes_produits_meta' )
        ->result_array();

        if( $order_foods ) {
            
            $order_is_ready     =   [];
            $order_is_canceled  =   [];
            $order_all_food     =   $this->post( 'all_foods' );

            foreach( $order_foods as $food ) {
                if( in_array( $food[ 'VALUE' ], [ 'ready', 'collected' ] ) ) {
                    $order_is_ready[]   =   true;
                }

                if( in_array( $food[ 'VALUE' ], [ 'denied', 'canceled', 'issue' ] )  ) {
                    $order_is_canceled[]   =   false;
                }
            }

            
            if( count( $order_is_ready ) == count( $order_foods ) ) {
                $status     =   'ready';
            } else if( count( $order_is_canceled ) == count( $order_foods ) ) {
                $status     =   'denied';
            } else {
                if( count( $order_is_canceled ) > 0 ) {
                    $status     =   'denied';
                } else if( count( $order_is_ready ) > 0 ) {
                    $status     =   'partially';
                } else {
                    $status     =   'ongoing';
                }
            }

            // update if it's ready
            $this->db->where( 'CODE', $this->post( 'order_code' ) )
            ->update( store_prefix() . 'nexo_commandes', [
                'RESTAURANT_ORDER_STATUS'      =>   $status,
            ]);

            // if order is ready we should send a notification
            if( count( $order_is_ready ) == count( $order_foods ) ) {
                nexo_notices([
                    'user_id'       =>  User::id(),
                    'link'          =>  site_url([ 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ]),
                    'icon'          =>  'fa fa-cutlery',
                    'type'          =>  'text-success',
                    'message'       =>  sprintf( __( 'The order <strong>%s</strong> is ready', 'nexo' ), $this->post( 'order_code' ) )
                ]);
            }
        }

        return $this->__success();
    }

    /**
     * Print To Kitchen
    **/

    public function print_to_kitchen_get( $order_id )
    {
        $this->load->library( 'Curl' );
        $this->load->model( 'options' );
        $this->load->model( 'Nexo_Checkout' );
        $this->load->config( 'nexo' );
        // get Printer id associate to that printer
        $Options        =   $this->options->get();
        
        // Get kitchen id
        $order          =   $this->Nexo_Checkout->get_order_with_metas( $order_id );

        if( store_option( 'disable_area_rooms' ) == 'yes' ) {
            $printer_id     =   store_option( 'printer_takeway' );
        } else {
            if( @$order[0][ 'METAS' ][ 'room_id' ] != null ) {
                // get Kitchen linked to that room
                $kitchen        =   $this->get_kitchen( $order[0][ 'METAS' ][ 'room_id' ], 'REF_ROOM' );
                $printer_id     =   store_option( 'printer_kitchen_' . $kitchen[0][ 'ID' ] );
            } else {
                $printer_id     =   store_option( store_prefix() . 'printer_takeway' );
            }
        }

        $document       =   json_encode( $order );

        if( $printer_id != null && ! in_array( $order[0][ 'RESTAURANT_ORDER_STATUS' ], [ 'ready', 'collected' ] ) ) {
            
            $data               =   $this->curl->post( tendoo_config( 'nexo', 'store_url' ) . '/api/gcp/submit-print-job/' . $printer_id . '?app_code=' . @$_GET[ 'app_code' ], [
                'content'       =>  $this->load->module_view( 'gastro', 'print.kitchen-receipt', [
                    'order'     =>  $order[0],
                    'Options'   =>  $Options,
                    'items'     =>  $this->get_order_items( $order[0][ 'CODE' ] )
                ], true ),
                'title'         =>  $order[0][ 'TITRE' ]
            ]);

            return $data;
        }
        return $this->__failed();
    }

    /**
     * Split print
     * @param int order id
     * @return void
    **/

    public function split_print_get( $order_id ) 
    {
        $this->load->library( 'Curl' );
        $this->load->model( 'options' );
        $this->load->model( 'Nexo_Checkout' );
        $this->load->config( 'nexo' );
        // let's make sure those items has not yet been printed
        $this->cache        =   new CI_Cache(array( 'adapter' => 'file', 'backup' => 'file', 'key_prefix'    =>    'gastro_print_status_' . store_prefix() ));
        // get Printer id associate to that printer
        $Options        =   $this->options->get();
        $kitchens       =   $this->db->get( store_prefix() . 'nexo_restaurant_kitchens' )
        ->result_array();

        $errors         =   [];
        // Get kitchen id
        $order          =   $this->Nexo_Checkout->get_order_with_metas( $order_id );

        if( $kitchens ) {
            foreach( $kitchens as $kitchen ) {
                $printer_id             =   store_option( 'printer_kitchen_' . $kitchen[ 'ID' ], false );

                // if printer is not set, then break it
                if( ! $printer_id ) {
                    break;
                }

                // check if kitchen listen to specific categories
                $categories             =   $kitchen[ 'REF_CATEGORY' ];
                $categories_ids         =   [];

                if( ! empty( $categories ) ) {
                    $categories_ids         =   explode( ',', $categories );
                }

                if( ! empty( $categories_ids ) ) {
                    $orders         =   $this->db
                    ->select( '*,
                    aauth_users.name  as AUTHOR_NAME,
                    ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
                    ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
                    ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
                    ' . store_prefix() . 'nexo_commandes_produits.ID as ITEM_ID,
                    ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR' )
                    ->from( store_prefix() . 'nexo_commandes' )
                    ->join( 
                        store_prefix() . 'nexo_commandes_produits', 
                        store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . 
                        store_prefix() . 'nexo_commandes.CODE' 
                    )
                    ->join( 
                        store_prefix() . 'nexo_articles', 
                        store_prefix() . 'nexo_articles.CODEBAR = ' . 
                        store_prefix() . 'nexo_commandes_produits.RESTAURANT_PRODUCT_REAL_BARCODE' 
                    )
                    ->join( 
                        'aauth_users',
                        'aauth_users.id = '. 
                        store_prefix() . 'nexo_commandes.AUTHOR' 
                    )
                    ->where_not_in( store_prefix() . 'nexo_commandes.RESTAURANT_ORDER_STATUS', [ 'ready', 'collected' ] )
                    ->where( store_prefix() . 'nexo_commandes.ID', $order_id )
                    ->where_in( 'REF_CATEGORIE', $categories_ids )
                    ->get()
                    ->result_array(); 

                    // keep order ids
                    // basically that order should be printed
                    if( $orders ) {

                        $printed_items              =   ! $this->cache->get( 'order_' . $order_id ) ? []   :   $this->cache->get( 'order_' . $order_id );
                        $items_to_print             =   [];
                        $printed_items_copy         =   $printed_items;

                        foreach( $orders as $order ) {
                            if( 
                                ( $order[ 'RESTAURANT_ORDER_TYPE' ] == 'dinein' && $order[ 'TYPE' ] == 'nexo_order_comptant' && $order[ 'RESTAURANT_ORDER_STATUS' ] == 'ready' )
                            ) {
                                // if the order restaurant type is "dine in" and the order has been paid. 
                                // Then we can't allow printing
                                $errors[]   =   [
                                    'status'    =>  'failed',
                                    'message'   =>  sprintf( __( 'cant print dinein ready paid order %s', 'gastro' ), $order[ 'CODE' ] )
                                ];
                                log_message( 'error', sprintf( __( 'cant print dinein ready paid order %s', 'gastro' ), $order[ 'CODE' ] ) );
                                break;
                            }

                            // if looped item match was has yet been printed, then just remove it from
                            // the copy of printed items
                            $key    =   array_search( $order[ 'REF_PRODUCT_CODEBAR' ], $printed_items_copy );
                            if( $key !== FALSE ) {
                                array_splice( $printed_items_copy, $key, 1 );                                
                            } else {
                                // We assume that item has'nt yet been printed
                                $items_to_print[]       =      $order[ 'REF_PRODUCT_CODEBAR' ]; 
                            }
                        }

                        // if there is at least something to print
                        if( $items_to_print ) {
                            $printed_items      =   array_merge( $printed_items, $items_to_print );
                            $table              =   $this->db->select( '*' )
                            ->from( store_prefix() . 'nexo_restaurant_tables_relation_orders' )
                            ->join( store_prefix() . 'nexo_restaurant_tables', store_prefix() . 'nexo_restaurant_tables.ID = ' . store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_TABLE' )
                            ->where( store_prefix() . 'nexo_restaurant_tables_relation_orders.REF_ORDER', $order_id )
                            ->get()->result_array();

                            $this->cache->save( 'order_' . $order_id, $printed_items, 3600*24 );// save for 24 hours
                            
                            $data               =   $this->curl->post( tendoo_config( 'nexo', 'store_url' ) . '/api/gcp/submit-print-job/' . $printer_id . '?app_code=' . @$_GET[ 'app_code' ], [
                                'content'       =>  $this->load->module_view( 'gastro', 'print.kitchen-receipt', [
                                    'order'     =>  $orders[0],
                                    'table'     =>  $table,
                                    'kitchen'   =>  $kitchen,
                                    'Options'   =>  $Options,
                                    'items'     =>  $this->get_order_items( $order[ 'CODE' ], $items_to_print ) // get order code from last entry on $orders loop
                                ], true ),
                                'title'         =>  $order[ 'TITRE' ]
                            ]);
                            
                            $errors[]       =   [
                                'status'     => 'success',
                                'message'   =>  sprintf( __( '%s item(s) has been printed', 'gastro' ), count( $items_to_print ) ),
                                'response'  =>  json_decode( $data, true )
                            ];

                            log_message( 'debug', sprintf( __( '%s item(s) has been printed', 'gastro' ), count( $items_to_print ) ) );
                        } else {
                            $errors[]   =   [
                                'status'    =>  'failed',
                                'message'   =>  __( 'No new item to print', 'gastro' )
                            ];

                            log_message( 'debug', __( 'No new item to print', 'gastro' ) );
                        }
                    }
                }
            }
            return $errors ? $this->response( $errors ) : $this->__success();
        }
        return $this->__failed();
    }

    /**
     *  Get Kitchen
     *  @param int kitchen id
     *  @return array
    **/

    private function get_kitchen( $id = null, $filter = 'ID' )
    {
        if( $id != null && $filter == 'ID' ) {
            $this->db->where( 'ID', $id );
        } else if( $filter == 'REF_ROOM' && $id != null ) {
            $this->db->where( 'REF_ROOM', $id );
        }

        $query =    $this->db->get( store_prefix() . 'nexo_restaurant_kitchens' );
        return $query->result_array();
    }

    /** 
     * Refresh Google
    **/

    public function google_refresh_get()
    {
        $this->load->library( 'Curl' );
        $this->response( $this->curl->get( tendoo_config( 'nexo', 'store_url' ) . '/api/google-refresh?app_code=' . $_GET[ 'app_code' ] ), 200 );
    }

    private function get_order_items( $order_code, $barcodes = []) 
    {
        $this->db
        ->select('
        ' . store_prefix() . 'nexo_articles.CODEBAR as CODEBAR,
        ' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QTE_ADDED,
        ' . store_prefix() . 'nexo_commandes_produits.ID as COMMAND_PRODUCT_ID,
        ' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
        ' . store_prefix() . 'nexo_articles.REF_CATEGORIE as REF_CATEGORIE,
        ' . store_prefix() . 'nexo_commandes_produits.NAME as NAME,

        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order_code . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_note"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
        ) as FOOD_NOTE,
        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order_code . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_status"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
        ) as FOOD_STATUS,
        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order_code . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "meal"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
        ) as MEAL,
        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order_code . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "restaurant_food_issue"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
        ) as FOOD_ISSUE,
        ( SELECT ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.VALUE FROM ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta
            WHERE ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_CODE = "' . $order_code . '"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.KEY = "modifiers"
            AND ' . $this->db->dbprefix . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT = COMMAND_PRODUCT_ID
        ) as MODIFIERS')
        ->from( store_prefix() . 'nexo_commandes')
        ->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
        ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'left')
        ->join( store_prefix() . 'nexo_commandes_produits_meta', store_prefix() . 'nexo_commandes_produits.ID = ' . store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT', 'left' )
        ->group_by( store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT' )
        ->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order_code );

        if( $barcodes ) {
            $this->db->where_in( store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', $barcodes );
        }

        $query  = $this->db->get();

        return $query->result_array();
    }

    /**
     * Get Ready Orders
     * @param 
    **/

    public function ready_orders_get()
    {
        $this->db->select( '*,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
        ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE' 
        )
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( 
            store_prefix() . 'nexo_commandes_produits',
            store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . store_prefix() . 'nexo_commandes.CODE'
        );
        $orders     =   $this->db->where( 'RESTAURANT_ORDER_STATUS', 'ready' )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >', Carbon::parse( date_now() )->startOfDay()->toDateTimeString() )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <', Carbon::parse( date_now() )->endOfDay()->toDateTimeString() )
        ->get()
        ->result_array();

        return $this->response( $orders, 200 );
    }

    /**
     * Set an order has collected
     * @param void
     * @return void
    **/

    public function order_collected_post()
    {
        $order      =   $this->db->where( 'ID', $this->post( 'order_id' ) )
        ->get( store_prefix() . 'nexo_commandes' )
        ->result_array();

        // exists
        if( $order ) {
            $this->db->where( 'ID', $this->post( 'order_id' ) )
            ->update( store_prefix() . 'nexo_commandes', [
                'RESTAURANT_ORDER_STATUS'   =>   'collected'
            ]);
            return $this->__success();
        } else {
            return $this->__failed();
        }
    }
}