<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_taxes' );
    if( ! in_array( 'TYPE', $columns ) && ! in_array( 'FIXED', $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_taxes` 
        ADD `TYPE` VARCHAR(200) NOT NULL AFTER `RATE`, 
        ADD `FIXED` float(11) NOT NULL AFTER `TYPE`;' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_articles' );
    if( ! array_in([ 'STOCK_ALERT', 'ALERT_QUANTITY', 'EXPIRATION_DATE', 'ON_EXPIRE_ACTION' ], $columns ) ) {
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
        ADD `STOCK_ALERT` VARCHAR(200) NOT NULL AFTER `STOCK_ENABLED`, 
        ADD `ALERT_QUANTITY` int(11) NOT NULL AFTER `STOCK_ENABLED`, 
        ADD `ON_EXPIRE_ACTION` varchar(200) NOT NULL AFTER `STOCK_ENABLED`, 
        ADD `EXPIRATION_DATE` datetime NOT NULL AFTER `TYPE`;' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_articles' );
    if( ! array_in([ 'TAX_TYPE' ], $columns ) ) {
        // inclusive && exclusive
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
        ADD `TAX_TYPE` VARCHAR(200) NOT NULL AFTER `REF_TAXE`' );
    }

    $columns            =   $this->db->list_fields( $store_prefix . 'nexo_commandes_produits' );
    if( ! array_in([ 'PRIX_BRUT_TOTAL' ], $columns ) ) {
        // inclusive && exclusive
        $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_commandes_produits` 
        ADD `PRIX_BRUT_TOTAL` flat(11) NOT NULL AFTER `PRIX_TOTAL`' );
    }

    $this->options->delete( $store_prefix . 'nexo_enable_stock_warning' );
}