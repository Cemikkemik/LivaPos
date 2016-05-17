<?php
$this->db->query('ALTER TABLE `' . $this->db->dbprefix('nexo_clients') . '` CHANGE `TEL` `TEL` VARCHAR(200) NOT NULL;');
