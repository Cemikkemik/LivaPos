<?php
$this->db->query('ALTER TABLE `'.$this->db->dbprefix.'nexo_clients` ADD `REF_GROUP` INT NOT NULL AFTER `DISCOUNT_ACTIVE`;');
