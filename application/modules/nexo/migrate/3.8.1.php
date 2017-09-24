<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    // edit item price and taxes

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_fournisseurs_history` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `TYPE` varchar(200) NOT NULL,
        `AMOUNT` float(11) NOT NULL,
        `REF_PROVIDER` int(11) NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DATE_MOD` datetime NOT NULL,
        `AUTHOR` int(11) NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_fournisseurs` 
    ADD `PAYABLE` float(11) NOT NULL AFTER `DESCRIPTION`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles_stock_flow` 
    ADD `BEFORE_QUANTITE` float(11) NOT NULL AFTER `REF_ARTICLE_BARCODE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles_stock_flow` 
    ADD `AFTER_QUANTITE` float(11) NOT NULL AFTER `QUANTITE`;' );

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_refunds` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `REF_COMMAND` int(11) NOT NULL,
        `REF_ITEM` int(11) NOT NULL,
        `QUANTITY` int(11) NOT NULL,
        `REF_UNIT` int(11) NULL,
        `AUTHOR` int(11) NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DATE_MODIFICATION` datetime NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}