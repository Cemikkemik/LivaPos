<?php
$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `' . $this->db->dbprefix . $store_prefix . 'nexo_articles_meta` (
      `ID` int(11) NOT NULL AUTO_INCREMENT,
      `REF_ARTICLE` int(11) NOT NULL,
      `KEY` varchar(250) NOT NULL,
      `VALUE` text NOT NULL,
      `DATE_CREATION` datetime NOT NULL,
      `DATE_MOD` datetime NOT NULL,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}
