<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // edit item price and taxes
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_premium_factures` 
    ADD `REF_PROVIDER` int(11) NOT NULL AFTER `REF_CATEGORY`, 
    ADD `REF_USER` int(11) NOT NULL AFTER `REF_PROVIDER`;' );
}