<?php
class Nexo_Restful extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'tendoo_settings_tables', array( $this, 'sql' ) );
		$this->events->add_action( 'do_enable_module', array( $this, 'enable' ) );
		$this->events->add_action( 'do_remove_module', array( $this, 'remove' ) );
	}
	
	/** 
	 * Enable
	**/
	
	function enable( $module )
	{
		global $Options;
		if( $module == 'codeigniter_restful' && @$Options[ 'codeigniter_restful_installed' ] == NULL ) {
			$this->sql();
			$this->options->set( 'codeigniter_restful_installed', true, true );
		}
	}
	
	function remove( $module )
	{
		if( $module == 'codeigniter_restful' ) {
			$this->db->query( 'DROP TABLE IF EXISTS `'.$this->db->dbprefix.'restapi_keys`;' );
			$this->options->delete( 'codeigniter_restful_installed' );
		}
	}
	
	/**
	 * Keys table
	**/
	
	function sql()
	{
		global $CurrentScreen;
		if( $CurrentScreen != 'dashboard' ) {
			// Enable Me
			Modules::enable( 'codeigniter_restful' );
		}
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `' . $this->db->dbprefix . 'restapi_keys` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`key` VARCHAR(40) NOT NULL,
			`level` INT(2) NOT NULL,
			`ignore_limits` TINYINT(1) NOT NULL DEFAULT "0",
			`date_created` INT(11) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;' );
	}
}

new Nexo_Restful;