<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

Trait Reports {
    public function get_commission_report_post()
    {
        $startDate      =   Carbon::parse( $this->post( 'start_date' ) )->startOfDay()->toDateTimeString();

        $endDate        =   Carbon::parse( $this->post( 'end_date' ) )
        ->endOfDay()->toDateTimeString();

        $this->db->select( '*' )->from( store_prefix() . 'alvaro_commissions' )
        ->join( store_prefix() . 'nexo_commandes', store_prefix() . 'alvaro_commissions.ref_order = ' . store_prefix() . 'nexo_commandes.ID' );

        $query  =   $this->db->where( 'ref_author', $this->post( 'beautican' ) )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $startDate )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $endDate )
        ->get();

        $this->response( $query->result_array(), 200 );
    }
}
