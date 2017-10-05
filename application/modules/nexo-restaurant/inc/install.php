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
        // @deprecated
        // $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_rooms` (
        //   `ID` int(11) NOT NULL AUTO_INCREMENT,
        //   `NAME` varchar(200) NOT NULL,
        //   `DESCRIPTION` text NOT NULL,
        //   `DATE_CREATION` datetime NOT NULL,
        //   `DATE_MODIFICATION` datetime NOT NULL,
        //   `AUTHOR` int(11),
        //   PRIMARY KEY (`ID`)
        // ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_tables` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar( 200 )  NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `MAX_SEATS` int( 11 ),
          `CURRENT_SEATS_USED` int(11),
          `STATUS` varchar(200),
          `SINCE` datetime not null,
          `BOOKING_START` datetime not null,
          `DATE_CREATION` datetime not null,
          `DATE_MODIFICATION` datetime not null,
          `AUTHOR` int(11) NOT NULL,
          `REF_AREA` int(11) NOT NULL,
          `CURRENT_SESSION_ID` int(11) NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_tables_sessions` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `REF_TABLE` int(11) NOT NULL,
          `SESSION_STARTS` datetime NOT NULL,
          `SESSION_ENDS` datetime NOT NULL,
          `AUTHOR` int(11) NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $prefix . 'nexo_restaurant_tables_relation_orders` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `REF_ORDER` int(11) NOT NULL,
          `REF_TABLE` int(11) NOT NULL,
          `REF_SESSION` int(11) NOT NULL,
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

        $this->db->query('CREATE TABLE IF NOT EXISTS `'. $prefix .'nexo_restaurant_kitchens` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar(200) NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `AUTHOR` int(11) NOT NULL,
          `DATE_CREATION` datetime NOT NULL,
          `DATE_MOD` datetime NOT NULL,
          `REF_CATEGORY` varchar(200) NOT NULL,
          `REF_ROOM` int NOT NULL,
          `PRINTER` text NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query('CREATE TABLE IF NOT EXISTS `'. $prefix .'nexo_restaurant_modifiers` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar(200) NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `AUTHOR` int(11) NOT NULL,
          `DATE_CREATION` datetime NOT NULL,
          `DATE_MODIFICATION` datetime NOT NULL,
          `REF_CATEGORY` int(11) NOT NULL,
          `DEFAULT` boolean NOT NULL,
          `PRICE` float(11) NOT NULL,
          `IMAGE` text NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query('CREATE TABLE IF NOT EXISTS `'. $prefix .'nexo_restaurant_modifiers_categories` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `NAME` varchar(200) NOT NULL,
            `DESCRIPTION` text NOT NULL,
            `AUTHOR` int(11) NOT NULL,
            `DATE_CREATION` datetime NOT NULL,
            `DATE_MODIFICATION` datetime NOT NULL,
            `FORCED` boolean NOT NULL,
            `MULTISELECT` boolean NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_registers` ADD `REF_KITCHEN` INT NOT NULL AFTER `USED_BY`;');
        $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_articles` ADD `REF_MODIFIERS_GROUP` INT NOT NULL AFTER `BARCODE_TYPE`;');
        $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_commandes` ADD `RESTAURANT_ORDER_TYPE` varchar(200) NOT NULL AFTER `TYPE`;');
        $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_commandes` ADD `RESTAURANT_ORDER_STATUS` varchar(200) NOT NULL AFTER `RESTAURANT_ORDER_TYPE`;');
        $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_commandes_produits` ADD `RESTAURANT_PRODUCT_REAL_BARCODE` varchar(200) NOT NULL AFTER `REF_PRODUCT_CODEBAR`;');
        // $this->db->query( 'ALTER TABLE `'. $prefix .'nexo_commandes` ADD `REF_BOOKING` INT NOT NULL AFTER `BARCODE_TYPE`;');

        Modules::enable( 'nexo-restaurant' );
        $this->options->set( 'nexo_restaurant_installed', true, true );
    }

    /**
     *  Delete Tables
     *  @param string table prefix
     *  @return void
    **/

    public function delete_tables( $table_prefix = '' )
    {
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_rooms`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_tables`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_areas`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_kitchens`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_modifiers`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_table_sessions`;');
        $this->db->query('DROP TABLE IF EXISTS `' . $table_prefix . 'nexo_restaurant_modifiers_categories`;');
        $this->db->query( 'ALTER TABLE `' . $table_prefix . 'nexo_registers` DROP IF EXISTS `REF_KITCHEN`;' );
        $this->db->query( 'ALTER TABLE `' . $table_prefix . 'nexo_articles` DROP IF EXISTS `REF_MODIFIERS_GROUP`;' );
        $this->db->query( 'ALTER TABLE `' . $table_prefix . 'nexo_commandes` DROP IF EXISTS `RESTAURANT_ORDER_TYPE`;' );
        $this->db->query( 'ALTER TABLE `' . $table_prefix . 'nexo_commandes` DROP IF EXISTS `RESTAURANT_ORDER_STATUS`;' );
        $this->db->query( 'ALTER TABLE `' . $table_prefix . 'nexo_commandes_produits` DROP IF EXISTS `RESTAURANT_PRODUCT_REAL_BARCODE`;' );

        $this->options->delete( 'nexo_restaurant_installed' );
    }
}
