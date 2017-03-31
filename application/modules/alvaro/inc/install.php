<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alvaro_Install extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  delete Store
     *  @param int store id
     *  @param object store
     *  @param string store prefix
     *  @return void
    **/

    public function delete_store( $int )
    {
        $this->db->query( 'DROP TABLE IF EXISTS `' . $this->db->dbprefix . 'store_' . $int . 'alvaro_commissions`;' );
        $this->db->query( 'DROP TABLE IF EXISTS `' . $this->db->dbprefix . 'store_' . $int . 'alvaro_appointments`;' );
        $this->db->query( 'DROP TABLE IF EXISTS `' . $this->db->dbprefix . 'store_' . $int . 'alvaro_log`;' );
    }

    /**
     *  Install tables
     *  @param void
     *  @return void
    **/

    public function install( $module, $scope = 'default', $prefix = '' )
    {
        global $Options;
        if( $module  == 'alvaro' ) {
            if( @$Options[ 'alvaro_installed' ] == null ) {
                $table_prefix		=    $prefix == '' ?	$this->db->dbprefix : $prefix;
                $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'alvaro_commissions` (
        		  `id` int(11) NOT NULL AUTO_INCREMENT,
        		  `commission_percentage` float not null,
                  `commission_amount` float not null,
                  `ref_order` int not null,
                  `ref_author` int not null,
                  `date_creation` datetime not null,
        		  PRIMARY KEY (`ID`)
        		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

                $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'alvaro_appointments` (
        		  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(200) not null,
                  `description` text not null,
                  `ref_order` int not null,
        		  `startsAt` datetime not null,
                  `endsAt` datetime not null,
                  `author` int not null,
                  `beautican` int not null,
                  `date_creation` datetime not null,
                  `date_modification` datetime not null,
        		  PRIMARY KEY (`ID`)
        		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

                $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'alvaro_log` (
        		  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `title` varchar(200) not null,
                  `description` text not null,
                  `ref_appointment` int not null,
                  `author` int not null,
                  `date_creation` datetime not null,
                  `date_modification` datetime not null,
        		  PRIMARY KEY (`ID`)
        		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

                $this->options->set( 'alvaro_installed', 'yes' );
            }
        }
    }

    /**
     *  install store tables
     *  @param
     *  @return
    **/

    public function install_store_tables( $table_prefix )
    {
        $this->install( 'alvaro', 'default', $table_prefix );
    }

    /**
     *  Uninstall
     *  @param
     *  @return
    **/

    public function uninstall( $namespace, $scope = 'default', $prefix = '')
    {
        if( $namespace == 'alvaro' ) {

            $this->load->model( 'Nexo_Stores' );
            $stores             =   $this->Nexo_Stores->get();
            $table_prefix		=	$this->db->dbprefix . $prefix;

            array_unshift( $stores, [
                'ID'        =>  0
            ]);

            foreach( $stores as $store ) {

                $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

                $this->db->query( 'DROP TABLE IF EXISTS `' . $table_prefix . $store_prefix . 'alvaro_commissions`;' );

                $this->db->query( 'DROP TABLE IF EXISTS `' . $table_prefix . $store_prefix . 'alvaro_appointments`;' );

                $this->db->query( 'DROP TABLE IF EXISTS `' . $table_prefix . $store_prefix . 'alvaro_log`;' );

                $this->options->delete( $prefix . $store_prefix . 'alvaro_installed');

            }
        }
    }
}
