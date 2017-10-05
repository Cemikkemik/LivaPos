<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // edit item price and taxes
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_tables` 
    ADD `BOOKING_START` datetime NOT NULL AFTER `STATUS`' );
}