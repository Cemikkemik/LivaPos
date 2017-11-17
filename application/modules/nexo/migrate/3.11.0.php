<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_arrivages' );

    if( in_array( 'REF_PROVIDERS', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_arrivages` CHANGE `REF_PROVIDERS` `REF_PROVIDER` INT(11) NOT NULL;' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_registers_activities' );

    if( ! in_array( 'NOTE', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_registers_activities` 
        ADD `NOTE` text NOT NULL AFTER `TYPE`;' );
    }
}