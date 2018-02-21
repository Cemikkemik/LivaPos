<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes' );
    if( ! in_array( 'REF_TAX', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes` 
        ADD `REF_TAX` int(11) NOT NULL AFTER `REF_SHIPPING_ADDRESS`' );
    }
}