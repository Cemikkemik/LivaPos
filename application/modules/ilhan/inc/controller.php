<?php
use Carbon\Carbon;

class ilhanCTRL extends Tendoo_Module
{
     /**
      * Constructor
      * @return void
      */
     public function __construct()
     {
          parent::__construct();
     }

     /**
      * Change Order Date
      * @param POST details
      * @return json response
      */
     public function change_date()
     {
          $this->db->where( 'ID', $this->input->post( 'order' ) )
          ->update( store_prefix() . 'nexo_commandes', [
               'DATE_CREATION'     =>   Carbon::parse( $this->input->post( 'date' ) )->toDateTimeString()
          ]);

          echo json_encode([
               'status'  =>   'success',
               'message' =>   'date_saved'
          ]);
     }
}