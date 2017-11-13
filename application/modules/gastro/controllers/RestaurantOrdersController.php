<?php
class RestaurantOrdersController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     *  Get orders
     *  @param  void
     *  @return array json
    **/

    public function get_orders()
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

                echo json_encode( $data );
                return;
            }
        }
    
        echo json_encode( [ ] );
        return false;
    }

    public function get_orders_with_meals()
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

            echo json_encode( $data );
            return;
        }

        echo json_encode( [ ] );
        return false;
    }
}