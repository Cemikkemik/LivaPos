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
          $date          =    date_parse_from_format( 'd/m/Y H:i', $this->input->post( 'date' ) );
          $carbon        =    Carbon::now();
          foreach( $date as $key => $value ) {
               if( in_array( $key, [ 'year', 'month', 'hour', 'day', 'minute' ] ) ) {
                    $carbon->$key       =    $value;
               }
          }

          $this->db->where( 'ID', $this->input->post( 'order' ) )
          ->update( store_prefix() . 'nexo_commandes', [
               'DATE_CREATION'     =>   $carbon->toDateTimeString(),
               'DATE_MOD'          =>   $carbon->toDateTimeString()
          ]);

          echo json_encode([
               'status'  =>   'success',
               'message' =>   'date_saved'
          ]);
     }
}