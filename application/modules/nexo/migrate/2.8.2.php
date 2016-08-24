<?php

/**
 * Introduce Order meta
**/

$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_commandes_meta` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `REF_ORDER_ID` int(11) NOT NULL,
  `KEY` varchar(250) NOT NULL,
  `VALUE` text NOT NULL,
  `DATE_CREATION` datetime NOT NULL,
  `DATE_MOD` datetime NOT NULL,
  `AUTHOR` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

// Add Type to item table
$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `TYPE` INT NOT NULL AFTER `AUTHOR`;' ); 

// Add Item Status
$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `STATUS` INT NOT NULL AFTER `TYPE`;' ); 

// Add Stock support
$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `STOCK_ENABLED` INT NOT NULL AFTER `STATUS`;' ); 

/**
 * Update all Item
**/

$this->db->update( 'nexo_articles', array(
	'TYPE'	=>	1, // Since 1 means physical
	'STATUS'	=>	1, // Since 1 means "true"
	'STOCK_ENABLED'	=>	1 // Since 1 means "true"
) );

