<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

trait Nexo_Expenses
{
    /**
    *
    * Exoense Listing post
    *
    * @return json object
    */

    public function expenses_from_timeinterval_post()
    {
        if( $this->post( 'start_date' ) && $this->post( 'end_date' ) ) {
            $start_date         =   Carbon::parse( $this->post( 'start_date' ) )->startOfDay()->toDateTimeString();
            $end_date           =   Carbon::parse( $this->post( 'end_date' ) )->endOfDay()->toDateTimeString();

            $this->db->where( 'DATE_CREATION >=', $start_date );
            $this->db->where( 'DATE_CREATION <=', $end_date );
        }
        $query      =   $this->db->get( store_prefix() . 'nexo_premium_factures' );
        $this->response( $query->result(), 200 );
    }
}
