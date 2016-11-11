<?php
$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_articles` 					ADD `AUTO_BARCODE` INT NOT NULL AFTER `AUTHOR`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_articles` 					ADD `BARCODE_TYPE` VARCHAR(200) NOT NULL AFTER `AUTO_BARCODE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_articles` 					ADD `USE_VARIATION` int(11) NOT NULL AFTER `BARCODE_TYPE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `DISCOUNT_TYPE` varchar(200) NOT NULL AFTER `PRIX_TOTAL`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `DISCOUNT_AMOUNT` float NOT NULL AFTER `DISCOUNT_TYPE`;' );

    $this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix .'nexo_commandes_produits` ADD `DISCOUNT_PERCENT` float NOT NULL AFTER `DISCOUNT_AMOUNT`;' );

    // `DISCOUNT_TYPE` varchar(200) NOT NULL,
    // Allow multiple paiement per item

    $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix . $store_prefix.'nexo_commandes_paiements` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_COMMAND_CODE` varchar(250) NOT NULL,
      `MONTANT` float NOT NULL,
      `AUTHOR` int(11) NOT NULL,
      `DATE_CREATION` datetime NOT NULL,
      `PAYMENT_TYPE` varchar(200) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    // Variation des produits

    $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix . $store_prefix.'nexo_articles_variations` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_ARTICLE` int(11) NOT NULL,
      `VAR_DESIGN` varchar(250) NOT NULL,
      `VAR_DESCRIPTION` varchar(250) NOT NULL,
      `VAR_PRIX_DE_VENTE` float NOT NULL,
      `VAR_QUANTITE_TOTALE` int(11) NOT NULL,
      `VAR_QUANTITE_RESTANTE` int(11) NOT NULL,
      `VAR_QUANTITE_VENDUE` int(11) NOT NULL,
      `VAR_COULEUR` varchar(250) NOT NULL,
      `VAR_TAILLE` varchar(250) NOT NULL,
      `VAR_POIDS` varchar(250) NOT NULL,
      `VAR_HAUTEUR` varchar(250) NOT NULL,
      `VAR_LARGEUR` varchar(250) NOT NULL,
      `VAR_SHADOW_PRICE` FLOAT NOT NULL,
      `VAR_SPECIAL_PRICE_START_DATE` datetime NOT NULL,
      `VAR_SPECIAL_PRICE_END_DATE` datetime NOT NULL,
      `VAR_APERCU` VARCHAR(200) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix . $store_prefix.'nexo_articles_defectueux` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_ARTICLE_BARCODE` varchar(250) NOT NULL,
      `QUANTITE` int(11) NOT NULL,
      `DATE_CREATION` datetime NOT NULL,
      `AUTHOR` int(11) NOT NULL,
      `REF_COMMAND_CODE` varchar(250) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}
