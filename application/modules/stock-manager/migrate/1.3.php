<?php
$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_stock_transfert` ADD `DEDUCT_FROM_SOURCE` VARCHAR(200) NOT NULL AFTER `FROM_STORE`' );