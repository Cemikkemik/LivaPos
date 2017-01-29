<?php
// @since 3.0.1

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_coupons` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_COMMAND` int(11) NOT NULL,
      `REF_COUPON` int(11) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_coupons` ADD `REWARDED_CASHIER` int(11) NOT NULL AFTER `USED_BY`;' );

    //

    // $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_articles_taxes` (
    //   `ID` int(11) NOT NULL AUTO_INCREMENT,
    //   `REF_COMMAND_CODE` varchar(250) NOT NULL,
    //   `REF_COUPON` int(11) NOT NULL,
    //   `DATE_CREATION` datetime NOT NULL,
    //   PRIMARY KEY (`ID`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
    //
    // $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_shipping_class` (
    //   `ID` int(11) NOT NULL AUTO_INCREMENT,
    //   `REF_COMMAND_CODE` varchar(250) NOT NULL,
    //   `REF_COUPON` int(11) NOT NULL,
    //   `DATE_CREATION` datetime NOT NULL,
    //   PRIMARY KEY (`ID`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
    //
    // $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_commandes_shippings` (
    //   `ID` int(11) NOT NULL AUTO_INCREMENT,
    //   `REF_COMMAND_CODE` varchar(250) NOT NULL,
    //   `REF_SHIPPING` int(11) NOT NULL,
    //   `DATE_CREATION` datetime NOT NULL,
    //   PRIMARY KEY (`ID`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
    //
    // $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_expenses_categories` (
    //   `ID` int(11) NOT NULL AUTO_INCREMENT,
    //   `NAME` varchar(250) NOT NULL,
    //   `DESCRIPTION` int(11) NOT NULL,
    //   `DATE_CREATION` datetime NOT NULL,
    //   `AUTHOR` int(11) NOT NULL,
    //   PRIMARY KEY (`ID`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');


}

$this->aauth        =    $this->users->auth;

// Coupons
$this->aauth->create_perm('create_coupons',    $this->lang->line( 'create_coupons' ),        $this->lang->line( 'create_coupons_details' ));
$this->aauth->create_perm('edit_coupons',    $this->lang->line( 'edit_coupons' ),        $this->lang->line( 'edit_coupons_details' ));
$this->aauth->create_perm('delete_coupons',    $this->lang->line( 'delete_coupons' ),        $this->lang->line( 'delete_coupons_details' ));

// Coupons
$this->aauth->allow_group( 'shop_cashier', 'create_coupons');
$this->aauth->allow_group( 'shop_cashier', 'edit_coupons');
$this->aauth->allow_group( 'shop_cashier', 'delete_coupons');

$this->aauth->allow_group( 'shop_manager', 'create_coupons');
$this->aauth->allow_group( 'shop_manager', 'edit_coupons');
$this->aauth->allow_group( 'shop_manager', 'delete_coupons');

$this->aauth->allow_group( 'master', 'create_coupons');
$this->aauth->allow_group( 'master', 'edit_coupons');
$this->aauth->allow_group( 'master', 'delete_coupons');

$this->aauth->allow_group( 'shop_tester', 'create_coupons');
$this->aauth->allow_group( 'shop_tester', 'edit_coupons');
$this->aauth->allow_group( 'shop_tester', 'delete_coupons');
