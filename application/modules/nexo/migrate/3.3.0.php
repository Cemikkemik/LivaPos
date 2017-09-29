<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix . 'nexo_taxes` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `NAME` varchar(200) NOT NULL,
        `DESCRIPTION` text NOT NULL,
        `PERCENTAGE` float(11) NOT NULL,
        `AUTHOR` int(11) NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DATE_MOD` datetime NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    // edit item price and taxes
    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
    ADD `REF_TAXE` INT NOT NULL AFTER `QUANTITE_VENDU`, 
    ADD `PRIX_DE_VENTE_TTC` FLOAT NOT NULL AFTER `PRIX_DE_VENTE`;' );
}