<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NFC_Library
{
    /**
     *  Add to cart
     *  @param int item id
     *  @return void
    **/

    public function add_to_cart( $barcode )
    {
        $Cache      =   new CI_Cache([
            'adapter'       =>  'apc',
            'backup'        =>  'file',
            'key_prefix'    =>  'nfc_'
        ]);

        $cart       =   $Cache->get( 'cart_' . User::id() );
        $cart       =   is_array( $cart ) ? $cart : [];

        $data       =   get_instance()->db->where( 'CODEBAR', $barcode )->get( store_prefix() . 'nexo_articles' )->result_array();

        $exist      =   false;

        if( $data ) {
            foreach( $cart as &$item ) {
                if( $item[ 'CODEBAR' ] == $data[0][ 'CODEBAR'] ) {
                    $item[ 'QTE_ADDED' ]++;
                    $exist          =   true;
                }
            }

            if( $exist == false ) {
                $data[0][ 'QTE_ADDED' ] =   1;
                $cart[]                 =   $data[0];
            }

            return $Cache->save( 'cart_' . User::id(), $cart, 3600 );
        }

        die( 'Item not found' );
    }

    /**
     *  Get Cart
     *  @param
     *  @return
    **/

    public function load_cart()
    {
        $Cache      =   new CI_Cache([
            'adapter'       =>  'apc',
            'backup'        =>  'file',
            'key_prefix'    =>  'nfc_'
        ]);

        $items      =   $Cache->get( 'cart_' . User::id() );
        return $items == false ? [] : $items;
    }

}
