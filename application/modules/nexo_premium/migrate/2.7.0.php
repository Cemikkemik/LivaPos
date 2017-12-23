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

    $this->db->query('CREATE TABLE IF NOT EXISTS `'.$store_prefix.'nexo_premium_factures_items` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `NAME` varchar(200) NOT NULL,
        `PRICE` float(11) NOT NULL,
        `QUANTITY` float(11) NOT NULL,
        `TOTAL` float(11) NOT NULL,
        `FLAT_DISCOUNT` float(11) NOT NULL,
        `PERCENTAGE_DISCOUNT` float(11) NOT NULL,
        `DISCOUNT_TYPE` varchar(200) NOT NULL,
        `REF_INVOICE` int(11) NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}