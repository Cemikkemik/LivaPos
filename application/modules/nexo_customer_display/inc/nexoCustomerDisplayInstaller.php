<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoCustomerDisplayInstaller extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }

  /**
   * Tables
   * @param string scope
   * @param string table prefix
   * @return void
  **/

  public function tables( $scope= '', $prefix = '' )
  {
      $this->db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'nexo_sliders` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `TITLE` varchar(200) NOT NULL,
        `DESCRIPTION` text NOT NULL,
        `AUTHOR` int(11) NOT NULL,
        `DATE_CREATION` datetime NOT NULL,
        `DATE_MOD` datetime NOT NULL,
        PRIMARY KEY (`ID`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

      $this->db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'nexo_slides` (
        `ID` int(11) NOT NULL AUTO_INCREMENT,
        `REF_SLIDER` int(11) NOT NULL,
        `TYPE` int(11) NOT NULL,
        `URL` varchar(200) NOT NULL,
        `TITLE` varchar(200) NOT NULL,
        `DESCRIPTION` text NOT NULL,
        PRIMARY KEY (`ID`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
  }

  /**
   * Delete tables
   *
   * @param string/array $scope
   * @param string table prefix
   * @return void
  **/

  public function delete_table( $scope = '', $prefix = '')
  {
      $this->db->query('DROP TABLE IF EXISTS `'.$prefix.'nexo_sliders`;');
      $this->db->query('DROP TABLE IF EXISTS `'.$prefix.'nexo_slides`;');
      $this->options->delete( 'nexo_customer_display_installed' );
  }
}
