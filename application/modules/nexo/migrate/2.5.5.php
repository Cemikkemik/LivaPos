<?php
@$this->db->query('ALTER TABLE `' . $this->db->dbprefix('nexo_commandes') . '` ADD `TVA` VARCHAR(200) NOT NULL AFTER `DISCOUNT_TYPE`;');
