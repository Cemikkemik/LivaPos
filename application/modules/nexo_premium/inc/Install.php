<?php
! defined('APPPATH') ? die() : null;

/**
 * Nexo Premium UI
 *
 * @author Blair Jersyer
 * @version 1.0
**/

class Nexo_Premium_Install extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        global $Options;

        // While installing

        $this->events->add_action('tendoo_settings_tables', array( $this, 'Install' ));
		$this->events->add_action( 'nexo_after_install_tables', array( $this, 'SQL_Install_Queries' ) );
		$this->events->add_action( 'nexo_after_delete_tables', array( $this, 'SQL_Uninstall_Queries' ) );

        // While activating

        if (@$Options[ 'nexo_premium_installed' ] == null) {
            $this->events->add_action('do_enable_module', array( $this, 'Install' ));
        }

        $this->events->add_action('do_remove_module', array( $this, 'Uninstall' ));
    }

    /**
     * Uninstall
     *
     * @return void
    **/

    public function Uninstall($module)
    {
        if ($module != 'nexo_premium') : return ;
        endif;

		$this->SQL_Uninstall_Queries( $this->db->dbprefix );
    }

    /**
     * Install
     *
     * @return void
    **/

    public function Install($module)
    {
        global $Options, $CurrentScreen;

        if ($CurrentScreen == 'dashboard' && $module != 'nexo_premium') {
            return;
        }

        if ($CurrentScreen == 'dashboard' && $module == 'nexo_premium' &&  @$Options[ 'nexo_premium_installed' ] != null) {
            return;
        }

        Modules::enable('nexo_premium');

        $this->SQL_Install_Queries( $this->db->dbprefix );

        $this->options->set('nexo_premium_installed', true, true);
    }

	/**
	 * SQL
	**/

	public function SQL_Install_Queries( $table_prefix )
	{
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_premium_factures` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `INTITULE` varchar(200) NOT NULL,
		  `REF` varchar(200) NOT NULL,
		  `MONTANT` float(11) NOT NULL,
          `REF_CATEGORY` int(11) NOT NULL,
		  `IMAGE` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MODIFICATION` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Backup

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_premium_backups` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `FILE_LOCATION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MODIFICATION` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query('CREATE TABLE IF NOT EXISTS `'. $table_prefix .'nexo_premium_factures_categories` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `NAME` varchar(200) NOT NULL,
          `DESCRIPTION` text NOT NULL,
          `DATE_CREATION` datetime NOT NULL,
          `DATE_MODIFICATION` datetime NOT NULL,
          `AUTHOR` int(11) NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
	}

	/**
	 * Uninstall Query
	**/

	public function SQL_Uninstall_Queries( $table_prefix )
	{
		$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_premium_factures`;');
		$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_premium_backups`;');
        $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_premium_factures_categories`;');
        $this->options->delete( 'nexo_premium_installed' );
	}
}

new Nexo_Premium_Install;
