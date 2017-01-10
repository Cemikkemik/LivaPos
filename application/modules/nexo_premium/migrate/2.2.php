<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $this->db->dbprefix . ( $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_' );

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $store_prefix .'nexo_premium_factures_categories` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `NAME` varchar(200) NOT NULL,
      `DESCRIPTION` text NOT NULL,
      `DATE_CREATION` datetime NOT NULL,
      `DATE_MODIFICATION` datetime NOT NULL,
      `AUTHOR` int(11) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query('
    	ALTER TABLE `' . $store_prefix . 'nexo_premium_factures' . '`
    	ADD `REF_CATEGORY` INT(11) NOT NULL AFTER `MONTANT`;
    ');
}
