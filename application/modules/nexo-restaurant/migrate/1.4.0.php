<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // edit item price and taxes
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_tables_relation_orders` 
    ADD `REF_SESSION` int(11) NOT NULL AFTER `REF_TABLE`' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_tables` 
    ADD `CURRENT_SESSION_ID` int(11) NOT NULL AFTER `CURRENT_SEATS_USED`' );

    $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_tables_sessions` (
     `ID` int(11) NOT NULL AUTO_INCREMENT,
     `REF_TABLE` int(11) NOT NULL,
     `SESSION_STARTS` datetime NOT NULL,
     `SESSION_ENDS` datetime NOT NULL,
     `AUTHOR` int(11) NOT NULL,
     PRIMARY KEY (`ID`)
   ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

   $this->db->query( 'ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `RESTAURANT_ORDER_TYPE` varchar(200) NOT NULL AFTER `TYPE`;');
   $this->db->query( 'ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `RESTAURANT_ORDER_STATUS` varchar(200) NOT NULL AFTER `RESTAURANT_ORDER_TYPE`;');
   $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_restaurant_kitchens` CHANGE `REF_CATEGORY` `REF_CATEGORY` VARCHAR(200) NOT NULL;');
}