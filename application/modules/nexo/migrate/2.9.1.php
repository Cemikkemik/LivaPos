<?php

$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_meta` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `REF_ARTICLE` int(11) NOT NULL,
  `KEY` varchar(250) NOT NULL,
  `VALUE` text NOT NULL,
  `DATE_CREATION` datetime NOT NULL,
  `DATE_MOD` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');