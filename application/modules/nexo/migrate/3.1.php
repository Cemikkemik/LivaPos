<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `NAME` varchar(200) NOT NULL AFTER `DISCOUNT_PERCENT`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `DESCRIPTION` text NOT NULL AFTER `NAME`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `INLINE` text NOT NULL AFTER `DESCRIPTION`;');

    $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix . $store_prefix.'nexo_clients_address` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(200) NOT NULL,
        `name` varchar(200) NOT NULL,
        `surname` varchar(200) NOT NULL,
        `enterprise` varchar(200) NOT NULL,
        `address_1` varchar(200) NOT NULL,
        `address_2` varchar(200) NOT NULL,
        `city` varchar(200) NOT NULL,
        `pobox` varchar(200) NOT NULL,
        `country` varchar(200) NOT NULL,
        `state` varchar(200) NOT NULL,
        `ref_client` int(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `REF_SHIPPING_ADDRESS` int(11) NOT NULL AFTER `GROUP_DISCOUNT`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_commandes` ADD `SHIPPING_AMOUNT` float(11) NOT NULL AFTER `GROUP_DISCOUNT`;');

    $this->db->query( 'CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_shippings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `ref_shipping` int(11) NOT NULL,
        `ref_order` int(11) NOT NULL,
        `name` varchar( 200 ) NOT NULL,
        `surname` varchar( 200 ) NOT NULL,
        `address_1` varchar( 200 ) NOT NULL,
        `address_2` varchar( 200 ) NOT NULL,
        `city` varchar( 200 ) NOT NULL,
        `country` varchar( 200 ) NOT NULL,
        `pobox` varchar( 200 ) NOT NULL,
        `state` varchar( 200 ) NOT NULL,
        `enterprise` varchar( 200 ) NOT NULL,
        `title` varchar(200) NOT NULL,
		`price` float(11) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );
}