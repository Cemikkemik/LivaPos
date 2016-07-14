<?php
class Nexo_Restaurant_Install extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->events->add_action('do_enable_module', array( $this, 'enable' ));
		$this->events->add_action('do_remove_module', array( $this, 'uninstall' ));
		$this->events->add_action('tendoo_settings_tables', array( $this, 'install' ));
	}
	
	/**
	 * SQL Tables
	**/
	
	public function sql_install_queries()
	{
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `STATUS` int NOT NULL,
		  `REF_GROUP` int NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables_groups` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `REF_PARENT` int NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_restaurant_orders_meta` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `KEY` varchar(200) NOT NULL,
		  `VALUE` text NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `REF_ORDER` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
		
		$this->options->set( 'nexo_restaurant_installed', 'true', true );
	}
	
	/**
	 * Install
	**/
	
	public function install()
	{
		Modules::enable( 'nexo_restaurant' );
		$this->sql_install_queries();
	}
	
	/**
	 * Enable Modules
	**/
	
	public function enable( $module ) 
	{
		global $Options;
		if( $module == 'nexo_restaurant' ) {
			if( @$Options[ 'nexo_restaurant_installed' ] == null ) {
				$this->sql_install_queries();
			}
		}
	}
	
	/**
	 * Uninstall
	**/
	
	public function uninstall( $module )
	{
		global $Options;
		if( $module == 'nexo_restaurant' ) {

			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_orders_meta`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables_groups`;');

			$this->options->delete( 'nexo_restaurant_installed' );
		}
	}
}

new Nexo_Restaurant_Install;