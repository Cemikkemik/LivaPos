<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class nexoCustomerDisplayModel extends CI_Model{

  public function __construct()
  {
        parent::__construct();
        //Codeigniter : Write Less Do More
  }

  /**
  *
  * Get customer Sliders
  *
  * @param int/null slider id
  * @return array
  */

  public function get_sliders( $param = null )
  {
      if( $param != null ){
          $this->db->where( 'ID', $param );
      }

      $query    =   $this->db->get( store_prefix() . 'nexo_sliders' );

      return $query->result_array();
  }

  /**
  *
  * Get registers_list
  *
  * @return array
  */

  public function registers_list( $status = 'opened' )
  {
      $query        =   $this->db->where( 'STATUS', $status )->get( store_prefix() . 'nexo_registers' );
      return $query->result_array();
  }

}
