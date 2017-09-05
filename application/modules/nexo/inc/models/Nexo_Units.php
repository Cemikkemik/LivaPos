<?php
class Nexo_Units extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
     }

     /**
      * Get Unit
      * @return units
     **/

     public function get()
     {
          return $this->db->get( store_prefix() . 'nexo_units' )
          ->result();
     }
}