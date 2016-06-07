<?php
$this->db->query('
	ALTER TABLE `' . $this->db->dbprefix('nexo_clients_groups') . '` 
	ADD `DISCOUNT_TYPE` VARCHAR(220) NOT NULL AFTER `AUTHOR`, 
	ADD `DISCOUNT_PERCENT` INT NOT NULL AFTER `DISCOUNT_TYPE`, 
	ADD `DISCOUNT_AMOUNT` INT NOT NULL AFTER `DISCOUNT_PERCENT`, 
	ADD `DISCOUNT_ENABLE_SCHEDULE` varchar(220) NOT NULL AFTER `DISCOUNT_AMOUNT`, 
	ADD  `DISCOUNT_START` datetime NOT NULL AFTER `DISCOUNT_ENABLE_SCHEDULE`, 
	ADD `DISCOUNT_END` datetime NOT NULL AFTER `DISCOUNT_START`;
');

$this->db->query( '
	ALTER TABLE `' . $this->db->dbprefix('nexo_commandes') . '` CHANGE `TYPE` `TYPE` VARCHAR(200) NOT NULL;
	ALTER TABLE `' . $this->db->dbprefix('nexo_commandes') . '` ADD `GROUP_DISCOUNT` INT NOT NULL AFTER `TVA`;
	ALTER TABLE `' . $this->db->dbprefix('nexo_commandes') . '` CHANGE `PAYMENT_TYPE` `PAYMENT_TYPE` VARCHAR(200) NOT NULL;
' );