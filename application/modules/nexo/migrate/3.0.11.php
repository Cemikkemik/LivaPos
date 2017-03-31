<?php
// @since 3.0.1

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_ARTICLE_BARCODE` varchar(250) NOT NULL,
      `QUANTITE` int(11) NOT NULL,
      `DATE_CREATION` datetime NOT NULL,
      `AUTHOR` int(11) NOT NULL,
      `REF_COMMAND_CODE` varchar(250) NOT NULL,
      `TYPE` varchar(200) NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

    $this->db->query( 'DROP TABLE IF EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_articles_defectueux`');

    $this->db->query( 'DROP TABLE IF EXISTS `'. $this->db->dbprefix . $store_prefix .'nexo_articles_utilisables`');
}
