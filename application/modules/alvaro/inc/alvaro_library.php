<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alvaro_Library extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Get Appointments
     *  @param int appointment id (optional)
     *  @return array
    **/

    public function get_appointments( $id = null )
    {
        $this->load->model( 'Nexo_Checkout' );
        if( $id != null ) {
            $this->db->where( 'alvaro_appoinments.id', $îd );
        }

        $query          =   $this->db
        ->select( '*,'
        . store_prefix() . 'alvaro_appointments.id as id,        
        aauth_users.name as beautican_name')
        ->from(  store_prefix() . 'alvaro_appointments' )
        ->join( 'aauth_users', store_prefix() . 'alvaro_appointments.beautican = aauth_users.id' )
        ->get();
        $appointments   =   $query->result_array();

        foreach( $appointments  as $key => $appointment ) {
            if( $appointment[ 'ref_order' ] != '0' ) {
                $order_details   =   $this->Nexo_Checkout->get_order_products( $appointment[ 'ref_order' ], true );
                $appointments[ $key ]    =   array_merge( $appointments[ $key ],  ( Array ) $order_details );
            } else {
                $appointments[ $key ]    =   array_merge( $appointments[ $key ], [
                    'order'     =>  [],
                    'products'  =>  []
                ]);
            }
        }

        return $appointments;
    }
}
