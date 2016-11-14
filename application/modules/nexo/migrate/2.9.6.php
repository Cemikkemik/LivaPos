<?php
$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `REMISE_TYPE` varchar(200) NOT NULL AFTER `RISTOURNE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `REMISE_PERCENT` float NOT NULL AFTER `REMISE_TYPE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `RABAIS_PERCENT` float NOT NULL AFTER `REMISE_PERCENT`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `RISTOURNE_PERCENT` float NOT NULL AFTER `RABAIS_PERCENT`;' );
}
