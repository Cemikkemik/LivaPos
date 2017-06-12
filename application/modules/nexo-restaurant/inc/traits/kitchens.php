<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
            'TYPE'      =>  'nexo_order_dine_ongoing'
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
        foreach( [ 'takeaway', 'dine' ] as $type ) {
            $types[ $type ][ 'pending' ]        =   'nexo_order_' . $type . '_pending';
            $types[ $type ][ 'ongoing' ]        =   'nexo_order_' . $type . '_ongoing';
            $types[ $type ][ 'partially' ]        =   'nexo_order_' . $type . '_partially';
            $types[ $type ][ 'ready' ]        =   'nexo_order_' . $type . '_ready';
            $types[ $type ][ 'incomplete' ]        =   'nexo_order_' . $type . '_incomplete';
            $types[ $type ][ 'canceled' ]        =   'nexo_order_' . $type . '_canceled';
            $types[ $type ][ 'denied' ]        =   'nexo_order_' . $type . '_denied';
        }

        switch( $this->post( 'order_real_type' ) ) {
            case 'dine_in'      :   $current =     'dine'; break;
            case 'take_away'    :   $current =     'takeaway'; break;
            default             :   $current =     'dine'; break;
        }

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
        }
    }

}
