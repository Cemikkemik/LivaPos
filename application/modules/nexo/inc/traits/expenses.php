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
        $this->db->select( 
            '*,' . 
            store_prefix() . 'nexo_premium_factures_categories.NAME as CATEGORY_NAME'
        );

        $this->db->from( store_prefix() . 'nexo_premium_factures' );
        $this->db->join( store_prefix() . 'nexo_premium_factures_categories', store_prefix() . 'nexo_premium_factures_categories.ID = ' . store_prefix() . 'nexo_premium_factures.REF_CATEGORY' );

        if( $this->post( 'start_date' ) && $this->post( 'end_date' ) ) {
            $start_date         =   Carbon::parse( $this->post( 'start_date' ) )->startOfDay()->toDateTimeString();
            $end_date           =   Carbon::parse( $this->post( 'end_date' ) )->endOfDay()->toDateTimeString();

            $this->db->where( store_prefix() . 'nexo_premium_factures.DATE_CREATION >=', $start_date );
            $this->db->where( store_prefix() . 'nexo_premium_factures.DATE_CREATION <=', $end_date );
        }
        
        $query      =   $this->db->get();
        $this->response( $query->result(), 200 );
    }
}
