<?php
// @since 1.4

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('CREATE TABLE IF NOT EXISTS `' . $store_prefix . 'alvaro_log` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(200) not null,
      `description` text not null,
      `ref_appointment` int not null,
      `author` int not null,
      `date_creation` datetime not null,
      `date_modification` datetime not null,
      PRIMARY KEY (`ID`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

}
