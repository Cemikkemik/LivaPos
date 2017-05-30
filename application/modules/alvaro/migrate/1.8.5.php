<?php
// @since 1.8.5

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` ADD `STOCK_ALERT` INT NOT NULL AFTER `USE_VARIATION`;');

}
