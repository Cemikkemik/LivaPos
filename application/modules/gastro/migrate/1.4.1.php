<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {
    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
    $this->db->query( 'ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `RESTAURANT_PRODUCT_REAL_BARCODE` varchar(200) NOT NULL AFTER `REF_PRODUCT_CODEBAR`;');
}