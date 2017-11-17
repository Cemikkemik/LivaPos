<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes_produits' );

    if( ! in_array( 'PRIX_BRUT', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_produits` ADD `PRIX_BRUT` float(11) NOT NULL AFTER `PRIX`;' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes_shippings' );

    if( ! in_array( 'phone', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_shippings` ADD `phone` varchar(200) NOT NULL AFTER `price`;' );
    }
    if( ! in_array( 'email', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_shippings` ADD `email` varchar(200) NOT NULL AFTER `price`;' );
    }
}