<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `'. $this->db->dbprefix . $store_prefix . 'nexo_notices` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `TYPE` varchar(200) NOT NULL,
        `TITLE` varchar(200) NOT NULL,
        `MESSAGE` text NOT NULL,
        `ICON` varchar(200) NOT NULL,
        `LINK` varchar(200) NOT NULL,
        `REF_USER` int(11) NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DATE_MOD` datetime NOT NULL,
        PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}