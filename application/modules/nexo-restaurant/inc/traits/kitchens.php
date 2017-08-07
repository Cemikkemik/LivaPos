<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;

trait nexo_restaurant_kitchens
{
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
        $types          =   [];
        foreach( [ 'takeaway', 'dinein', 'delivery' ] as $type ) {
            $types[ $type ][ 'pending' ]        =   'nexo_order_' . $type . '_pending';
            $types[ $type ][ 'ongoing' ]        =   'nexo_order_' . $type . '_ongoing';
            $types[ $type ][ 'partially' ]        =   'nexo_order_' . $type . '_partially';
            $types[ $type ][ 'ready' ]        =   'nexo_order_' . $type . '_ready';
            $types[ $type ][ 'incomplete' ]        =   'nexo_order_' . $type . '_incomplete';
            $types[ $type ][ 'canceled' ]        =   'nexo_order_' . $type . '_canceled';
            $types[ $type ][ 'denied' ]        =   'nexo_order_' . $type . '_denied';
        }

        $current        =   $this->post( 'order_real_type' );

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
                if( $food[ 'VALUE' ] == 'ready' ) {
                    $order_is_ready[]   =   true;
                }

                if( in_array( $food[ 'VALUE' ], [ 'denied', 'canceled', 'issue' ] )  ) {
                    $order_is_canceled[]   =   false;
                }
            }

            if( count( $order_is_ready ) == count( $order_foods ) ) {
                $order_type     =   $types[ $current ][ 'ready' ];
            } else if( count( $order_is_canceled ) == count( $order_foods ) ) {
                $order_type     =   $types[ $current ][ 'denied' ];
            } else {
                if( count( $order_is_canceled ) > 0 ) {
                    $order_type     =   $types[ $current ][ 'denied' ];
                } else if( count( $order_is_ready ) > 0 ) {
                    $order_type     =   $types[ $current ][ 'partially' ];
                } else {
                    $order_type     =   $types[ $current ][ 'ongoing' ];
                }
            }

            // update if it's ready
            $this->db->where( 'CODE', $this->post( 'order_code' ) )
            ->update( store_prefix() . 'nexo_commandes', [
                'TYPE'      =>   $order_type
            ]);

            // if order is ready we should send a notification
            if( count( $order_is_ready ) == count( $order_foods ) ) {
                nexo_notices([
                    'user_id'       =>  User::id(),
                    'link'          =>  site_url([ 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ]),
                    'icon'          =>  'fa fa-cutlery',
                    'type'          =>  'text-success',
                    'message'       =>  sprintf( __( 'La commande <strong>%s</strong> est prête', 'nexo' ), $this->post( 'order_code' ) )
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

        if( @$order[0][ 'METAS' ][ 'room_id' ] != null ) {
            // get Kitchen linked to that room
            $kitchen        =   $this->get_kitchen( $order[0][ 'METAS' ][ 'room_id' ], 'REF_ROOM' );
            $printer_id     =   @$Options[ store_prefix() . 'printer_kitchen_' . $kitchen[0][ 'ID' ] ];
        } else {
            $printer_id     =   @$Options[ store_prefix() . 'printer_takeway' ];
        }
        

        $document       =   json_encode( $order );

        if( $printer_id != null ) {
            
            $data               =   $this->curl->post( tendoo_config( 'nexo', 'store_url' ) . '/api/gcp/submit-print-job/' . $printer_id . '?app_code=' . @$_GET[ 'app_code' ], [
                'content'       =>  $this->load->module_view( 'nexo-restaurant', 'print.kitchen-receipt', [
                    'order'     =>  $order[0],
                    'Options'   =>  $Options,
                    'items'     =>  $this->get_order_items( $order[0][ 'CODE' ] )
                ], true ),
                'title'         =>  $order[0][ 'TITRE' ]
            ]);

            return $this->response( $data, 200 );
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

    private function get_order_items( $order_code ) 
    {
        $query  = $this->db
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
        ->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order_code )
        ->get();

        return $query->result_array();
    }
}
