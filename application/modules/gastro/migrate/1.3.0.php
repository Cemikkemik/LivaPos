<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // edit item price and taxes
    $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_tables_relation_orders` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `REF_ORDER` int(11) NOT NULL,
        `REF_TABLE` int(11) NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );
}