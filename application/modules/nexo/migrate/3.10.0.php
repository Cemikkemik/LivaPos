<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs_history` 
    ADD `REF_SUPPLY` int(11) NOT NULL AFTER `REF_PROVIDER`;' );
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs_history` 
    ADD `REF_INVOICE` int(11) NOT NULL AFTER `REF_PROVIDER`;' );
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs_history` 
    ADD `BEFORE_AMOUNT` float(11) NOT NULL AFTER `REF_PROVIDER`;' );
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs_history` 
    ADD `AFTER_AMOUNT` float(11) NOT NULL AFTER `REF_PROVIDER`;' );
}