<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait cu_display {

    /**
     * Cart status
    **/

    public function cart_status_post()
    {
        $Cache        =    new CI_Cache(array(
            'adapter' => 'apc',
            'backup' => 'file',
            'key_prefix' => 'cu_display_'
        ) );

        $cache_namespace        =   'view_' . $this->post( 'store_id' ) . '_' . $this->post( 'register_id' );

        if( $Cache->get( $cache_namespace ) ) {
            $this->response( $Cache->get( $cache_namespace ), 200 );
        }

        $this->__empty();
    }
    /**
     * Save data
    **/

    public function save_data_post()
    {
        $Cache        =    new CI_Cache(array(
            'adapter' => 'apc',
            'backup' => 'file',
            'key_prefix' => 'cu_display_'
        ) );

        $cache_namespace        =   'view_' . $this->post( 'store_id' ) . '_' . $this->post( 'register_id' );

        $Cache->delete( $cache_namespace );

        // Save only when there is something to save. Obviously
        if( $this->post( 'items' ) ) {
            $Cache->save( $cache_namespace, array(
                'items'     =>  $this->post( 'items' ),
                'vat'       =>  $this->post( 'vat' ),
                'paidSoFar' =>  $this->post( 'paidSoFar' ),
                'balance'   =>  $this->post( 'balance' )
            ), 1600 );
        }
    }
}
