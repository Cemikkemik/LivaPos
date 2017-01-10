<?php
trait nfc_trait {
    public function update_post()
    {
        $items          =   [];

        foreach( ( array ) $this->post( 'items' ) as $_item ) {
            $items[]      =   $_item;
        }

        $Cache      =   new CI_Cache([
            'adapter'       =>  'apc',
            'backup'        =>  'file',
            'key_prefix'    =>  'nfc_'
        ]);

        $cart       =   $Cache->get( 'cart_' . $this->post( 'user_id' ) );
        $cart       =   is_array( $cart ) ? $cart : [];

        $exists     =   [];
        $fresh      =   [];
        $old        =   [];

        // On parcours tous les identifiants qui existent et on les rescencent
        foreach( $items as $_item ) {
            $fresh[]        =  $_item[ 'CODEBAR' ];
        }

        foreach( $cart as $_item ) {
            $old[]          =   $_item[ 'CODEBAR' ];
        }

        $toRemove           =   array_diff( $old, $fresh );

        foreach( $cart as $key => $_item ) {
            if( in_array( $_item[ 'CODEBAR' ], $toRemove ) ) {
                array_splice( $cart, $key, 1 );
            }
        }

        foreach( $cart as &$item ) {
            foreach( $items as $_item ) {
                if( $_item[ 'CODEBAR' ] == $item[ 'CODEBAR' ] ) {
                    $item       =   $_item;
                    $exists[]   =   $_item[ 'CODEBAR' ];
                    break;
                }
            }
        }

        foreach( $items as $_item ) {
            if( ! in_array( $_item[ 'CODEBAR' ], $exists ) ) {
                array_unshift( $cart, $_item );
            }
        }

        $Cache->save( 'cart_' . $this->post( 'user_id' ), $cart, 3600 );

        $this->__success();
    }

    /**
     *  Retrieve
     *  @param
     *  @return
    **/

    public function retreive_get()
    {
        $Cache      =   new CI_Cache([
            'adapter'       =>  'apc',
            'backup'        =>  'file',
            'key_prefix'    =>  'nfc_'
        ]);

        $cart       =   $Cache->get( 'cart_' . $this->get( 'user_id' ) );

        $this->response( $cart == false ? [] : $cart    , 200 );
    }

    /**
     *  Delete Cache
     *  @param
     *  @return
    **/

    public function cache_delete()
    {
        $Cache      =   new CI_Cache([
            'adapter'       =>  'apc',
            'backup'        =>  'file',
            'key_prefix'    =>  'nfc_'
        ]);

        $Cache->delete( 'cart_' . $_GET[ 'user_id' ] );
        $this->__success();
    }
}
