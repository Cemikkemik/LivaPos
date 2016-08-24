<?php
/** 
 * Introduce Multi Store
**/

$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_stores` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `AUTHOR` int(11) NOT NULL,
  `STATUS` varchar(200) NOT NULL,
  `NAME` varchar(200) NOT NULL,
  `IMAGE` varchar(200) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `DATE_CREATION` datetime NOT NULL,
  `DATE_MOD` datetime NOT NULL,		
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

$this->db->query('CREATE TABLE IF NOT EXISTS `' . $this->db->dbprefix . 'nexo_stores_activities` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `AUTHOR` int(11) NOT NULL,
  `TYPE` varchar(200) NOT NULL,
  `DATE_CREATION` datetime NOT NULL,
  `DATE_MOD` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

// Permissions
$this->aauth        =    $this->users->auth;

// @since 2.8 Stores
$this->aauth->create_perm('create_shop',    $this->lang->line( 'create_shop' ),        $this->lang->line( 'create_shop_details' ));
$this->aauth->create_perm('edit_shop',    	$this->lang->line( 'edit_shop' ),        $this->lang->line( 'edit_shop_details' ));
$this->aauth->create_perm('delete_shop',	$this->lang->line( 'delete_shop' ),       $this->lang->line( 'delete_shop_details' ));
$this->aauth->create_perm('enter_shop',    	$this->lang->line( 'view_shop' ),       $this->lang->line( 'view_shop_details' ));

// Cashier Allow
// Shop
$this->aauth->allow_group('shop_cashier', 'enter_shop');

// Shop Manager
// @since 2.8
$this->aauth->allow_group('shop_manager', 'enter_shop');
$this->aauth->allow_group('shop_manager', 'create_shop');
$this->aauth->allow_group('shop_manager', 'delete_shop');
$this->aauth->allow_group('shop_manager', 'edit_shop');

// Master
// @since 2.8
$this->aauth->allow_group('master', 'enter_shop');
$this->aauth->allow_group('master', 'create_shop');
$this->aauth->allow_group('master', 'delete_shop');
$this->aauth->allow_group('master', 'edit_shop');