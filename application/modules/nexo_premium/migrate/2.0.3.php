<?php
// Customers
$this->db->query('
	ALTER TABLE `' . $this->db->dbprefix('nexo_premium_factures') . '` 
	CHANGE `MONTANT` `MONTANT` FLOAT(11) NOT NULL;
');
