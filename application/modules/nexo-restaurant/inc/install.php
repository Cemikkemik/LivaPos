<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nexo_Restaurant_Install extends Tendoo_Module
{
    /**
     *  create tables
     *  @param string table prefix
     *  @return void
    **/

    public function create_tables( $prefix = '' )
    {
        $prefix     =   $prefix == '' ? $this->db->dbprefix : $prefix;

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_rooms` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar(200) NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `DATE_CREATION` datetime NOT NULL,
          `DATE_MODIFICATION` datetime NOT NULL,
          `AUTHOR` int(11),
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_tables` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar( 200 )  NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `MAX_SEATS` int( 11 ),
          `CURRENT_SEATS_USED` int(11),
          `STATUS` varchar(200),
          `DATE_CREATION` datetime not null,
          `DATE_MODIFICATION` datetime not null,
          `AUTHOR` int(11) NOT NULL,
          `REF_AREA` int(11) NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_areas` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar(200) NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `DATE_CREATION` datetime NOT NULL,
          `DATE_MODIFICATION` datetime NOT NULL,
          `AUTHOR` int(11) NOT NULL,
          `REF_ROOM` int(11) NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query('CREATE TABLE IF NOT EXISTS `'. $table_prefix .'nexo_restaurant_kitchens` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `REF_CATEGORY` int NOT NULL,
		  `PRINTER` text NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
    }

    /**
     *  Delete Tables
     *  @param string table prefix
     *  @return void
    **/

    public function delete_tables( $table_prefix = '' )
    {
        $table_prefix   =   $table_prefix == '' ? $this->db->dbprefix : $table_prefix;

        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_rooms`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_tables`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_areas`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_kitchens`;');
    }
}
