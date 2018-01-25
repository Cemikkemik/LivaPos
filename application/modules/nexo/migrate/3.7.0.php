<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_arrivages' );

    if ( ! array_in([ 'VALUE', 'ITEMS', 'REF_PROVIDER' ], $columns ) ) {
        // edit item price and taxes
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_arrivages` 
        ADD `VALUE` float NOT NULL AFTER `DESCRIPTION`, 
        ADD `ITEMS` int(11) NOT NULL AFTER `VALUE`,
        ADD `REF_PROVIDER` varchar(200) NOT NULL AFTER `ITEMS`;' );
    }
}