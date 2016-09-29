<?php
$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `AUTO_BARCODE` INT NOT NULL AFTER `AUTHOR`;' ); 

$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `BARCODE_TYPE` VARCHAR(200) NOT NULL AFTER `AUTO_BARCODE`;' );

$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . 'nexo_articles` 					ADD `USE_VARIATION` int(11) NOT NULL AFTER `BARCODE_TYPE`;' ); 

// Allow multiple paiement per item

$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_paiement` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `REF_COMMAND_CODE` varchar(250) NOT NULL,
  `MONTANT` float NOT NULL,
  `AUTHOR` int(11) NOT NULL,
  `DATE_CREATION` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

// Variation des produits

$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_variations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `REF_ARTICLE` int(11) NOT NULL,
  `VAR_DESIGN` varchar(250) NOT NULL,
  `VAR_DESCRIPTION` varchar(250) NOT NULL,
  `VAR_PRIX_DE_VENTE` float NOT NULL,
  `VAR_QUANTITE_TOTALE` int(11) NOT NULL,
  `VAR_QUANTITE_RESTANTE` int(11) NOT NULL,
  `VAR_QUANTITE_VENDUE` int(11) NOT NULL,
  `VAR_COULEUR` varchart(250) NOT NULL,
  `VAR_TAILLE` varchart(250) NOT NULL,
  `VAR_POIDS` varchart(250) NOT NULL,
  `VAR_HAUTEUR` varchart(250) NOT NULL,
  `VAR_LARGEUR` varchart(250) NOT NULL,
  `VAR_SHADOW_PRICE` FLOAT NOT NULL,
  `VAR_SPECIAL_PRICE_START_DATE` datetime NOT NULL,
  `VAR_SPECIAL_PRICE_END_DATE` datetime NOT NULL,
  `VAR_APERCU` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');