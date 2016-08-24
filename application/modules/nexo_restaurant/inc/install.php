<?php
class Nexo_Restaurant_Install extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->events->add_action('do_enable_module', array( $this, 'enable' ));
		$this->events->add_action('do_remove_module', array( $this, 'uninstall' ));
		$this->events->add_action('tendoo_settings_tables', array( $this, 'install' ) );
		$this->events->add_action('tendoo_settings_final_config', array( $this, 'create_permissions' ) );
	}
	
	/**
	 * Create Permissions
	**/
	
	public function create_permissions()
	{
		$this->aauth        =    $this->users->auth;
		
		// Table Management
		$this->aauth->create_perm(
			'create_restaurant_tables',    
			__('Manage Restaurant tables', 'nexo_restaurant'),            
			__('Can create tables', 'nexo_restaurant')
		);
		
		$this->aauth->create_perm(
			'edit_restaurant_tables',    
			__('Manage Restaurant tables', 'nexo_restaurant'),            
			__('Can edit tables', 'nexo_restaurant')
		);
		
		$this->aauth->create_perm(
			'delete_restaurant_tables',    
			__('Manage Restaurant tables', 'nexo_restaurant'),            
			__('Can delete tables', 'nexo_restaurant')
		);
		
		// Kitchen Management
		$this->aauth->create_perm(
			'create_restaurant_kitchens',    
			__('Manage Restaurant Kitchen', 'nexo_restaurant'),            
			__('Can create kitchen', 'nexo_restaurant')
		);
		
		$this->aauth->create_perm(
			'edit_restaurant_kitchens',    
			__('Manage Restaurant Kitchen', 'nexo_restaurant'),            
			__('Can create kitchen', 'nexo_restaurant')
		);
		
		$this->aauth->create_perm(
			'delete_restaurant_kitchens',    
			__('Manage Restaurant Kitchen', 'nexo_restaurant'),            
			__('Can delete kitchen', 'nexo_restaurant')
		);
		
		$this->aauth->allow_group( 'shop_manager', 'create_restaurant_tables' );
		$this->aauth->allow_group( 'shop_manager', 'edit_restaurant_tables' );
		$this->aauth->allow_group( 'shop_manager', 'delete_restaurant_tables' );
		
		$this->aauth->allow_group( 'shop_manager', 'edit_restaurant_kitchens' );
		$this->aauth->allow_group( 'shop_manager', 'create_restaurant_kitchens' );
		$this->aauth->allow_group( 'shop_manager', 'delete_restaurant_kitchens' );
		
		$this->aauth->allow_group( 'master', 'create_restaurant_tables' );
		$this->aauth->allow_group( 'master', 'edit_restaurant_tables' );
		$this->aauth->allow_group( 'master', 'delete_restaurant_tables' );
		
		$this->aauth->allow_group( 'master', 'edit_restaurant_kitchens' );
		$this->aauth->allow_group( 'master', 'create_restaurant_kitchens' );
		$this->aauth->allow_group( 'master', 'delete_restaurant_kitchens' );
		
		$this->options->set( 'nexo_restaurant_installed', 'true', true );
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
		  `AXIS_X` float NOT NULL,
		  `AXIS_Y` float NOT NULL,
		  `AXIS_Z` float NOT NULL,
		  `MAX_SEATS` int(11) NOT NULL,
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
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$this->db->dbprefix.'nexo_restaurant_kitchens` (
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
		$this->aauth        =    $this->users->auth;
		global $Options;
		if( $module == 'nexo_restaurant' ) {

			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_orders_meta`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_tables_groups`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix.'nexo_restaurant_kitchens`;');
			
			$this->aauth->deny_group( 'shop_manager', 'edit_restaurant_tables');
			$this->aauth->deny_group( 'shop_manager', 'create_restaurant_tables');
			$this->aauth->deny_group( 'shop_manager', 'delete_restaurant_tables');
			
			$this->aauth->deny_group( 'shop_manager', 'edit_restaurant_kitchens');
			$this->aauth->deny_group( 'shop_manager', 'create_restaurant_kitchens');
			$this->aauth->deny_group( 'shop_manager', 'delete_restaurant_kitchens');
			
			$this->aauth->deny_group( 'master', 'edit_restaurant_tables');
			$this->aauth->deny_group( 'master', 'create_restaurant_tables');
			$this->aauth->deny_group( 'master', 'delete_restaurant_tables');
			
			$this->aauth->deny_group( 'master', 'edit_restaurant_kitchens');
			$this->aauth->deny_group( 'master', 'create_restaurant_kitchens');
			$this->aauth->deny_group( 'master', 'delete_restaurant_kitchens');

			$this->options->delete( 'nexo_restaurant_installed' );
		}
	}
}

new Nexo_Restaurant_Install;