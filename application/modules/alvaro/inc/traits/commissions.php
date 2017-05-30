<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

Trait Commissions {
    /**
     *  submit Commissions
     *  @param  void
     *  @return void
    **/

    public function submit_commission_post()
    {
        $this->load->model( 'Nexo_Checkout' );

        $query              =      $this->db->where( 'ref_order', $this->post( 'ref_order' ) )->get( store_prefix() . 'alvaro_commissions' )->result_array();

        if( $query ) {
            return $this->__forbidden();
        }

        $commission         =   0;
        $commission_amount  =   0;
        $total_time         =   0;
        $order_title        =   '';
        $item_names         =   [];

        foreach( $this->post( 'items' ) as $item ) {
            $raw_metas   =   $this->db->where( 'REF_ARTICLE', $item[ 'id' ] )->get( store_prefix() . 'nexo_articles_meta' )->result_array();

            $item_data  =   $this->db->where( 'ID', $item[ 'id' ] )->get( store_prefix() . 'nexo_articles' )->result_array();

            $item_names[]   =   $item_data[0][ 'DESIGN' ];

            $metas      =   [];
            foreach( $raw_metas as $meta) {
                $metas[ $meta[ 'KEY' ] ]      =  $meta[ 'VALUE' ];
            }

            if( $metas ) {
                $unique_commission  =   floatval( @$metas[ 'COMMISSION' ] )  * intval( $item[ 'qte' ] );
                $commission         +=       $unique_commission;
                $total_time         +=      floatval( @$metas[ 'TIME' ] ) * intval( $item[ 'qte' ] );
                $commission_amount  +=      ( floatval( $unique_commission ) * floatval( @$item[ 'price' ] ) ) / 100;
            }
        }

        // Customer Name
        $customer       =   $this->db->where( 'ID', $this->post( 'ref_client' ) )->get( store_prefix() . 'nexo_clients' )->result_array();
        $beautican      =   $this->db->where( 'id', $this->post( 'ref_author' ) )->get( 'aauth_users' )->result_array();

        // Update POST Title
        $this->db->where( 'CODE', $this->post( 'order_code' ) )->update( store_prefix() . 'nexo_commandes', [
            'TITRE'     =>      $customer[0][ 'NOM' ] . ' — ' . implode( ',', $item_names ) . ' — ' . $beautican[0][ 'name' ]
        ]);

        if( $this->post( 'is_appointment' ) == 'yes' || $this->post( 'loaded_order' ) ) {

            // when we're loading an order
            if( $this->post( 'loaded_order' ) != '' ) {
                $data                   =   
                $this->db->select( '*' )
                ->from( store_prefix() . 'nexo_commandes' )
                ->join( store_prefix() . 'alvaro_appointments', store_prefix() . 'alvaro_appointments.ref_order = ' . store_prefix() . 'nexo_commandes.ID' )
                ->where( store_prefix() . 'nexo_commandes.ID', $this->post( 'loaded_order' ) )
                ->get()->result();
            } else {
                $data                   =   $this->db->where( 'id', $this->post( 'appointment_id' ) )
                ->get( store_prefix() . 'alvaro_appointments' )->result();
            }

            $endsAt                 =   Carbon::parse( $data[0]->startsAt )
            ->addMinutes( $total_time == 0 ? 60 : $total_time )->toDateTimeString();

            $this->db
            ->where( store_prefix() . 'alvaro_appointments.id', $this->post( 'appointment_id' ) )
            ->where( 'ref_order', 0 )
            ->update( store_prefix() . 'alvaro_appointments', [
                'startsAt'          =>  $data[0]->startsAt,
                'endsAt'            =>  $endsAt,
                'ref_order'         =>  $this->post( 'ref_order' ),
                'title'             =>  $this->post( 'title' ),
                'date_creation'     =>  $this->post( 'date_creation' ),
            ]);

            // Commission only for complete order
            if( $this->post( 'order_type' ) == 'nexo_order_comptant' ) {
                $this->db->insert( store_prefix() . 'alvaro_commissions', [
                    'commission_amount'         =>  $commission_amount,
                    'commission_percentage'     =>  $commission,
                    'date_creation'             =>  $this->post( 'date_creation' ),
                    'ref_author'                =>  $data[0]->beautican,
                    'ref_order'                 =>  $this->post( 'ref_order' )
                ]);

                // Update the order date to the date of payment
                $this->db->where( 'CODE', $this->post( 'order_code' ) )->update( store_prefix() . 'nexo_commandes', [
                    'DATE_CREATION'     =>  $endsAt
                ]);
            }
        }

        $this->__success();
    }

    /**
     *  post appointent
     *  @param
     *  @return
    **/

    public function appointments_post()
    {
        if( $this->post( 'is_appointment' ) == 'yes' ) {

            $query  =   $this->db
            ->query( 'SELECT * FROM `' . $this->db->dbprefix . store_prefix() . "alvaro_appointments` WHERE ( (
                startsAt <= '" . $this->post( 'startsAt' ) . "' and
                endsAt >= '" . $this->post( 'endsAt' ) . "'
            ) or (
                startsAt <= '" . $this->post( 'startsAt' ) . "' and
                endsAt <= '" . $this->post( 'endsAt' ) . "' and
                endsAt > '" . $this->post( 'startsAt' ) . "'
            ) or (
                startsAt >= '" . $this->post( 'startsAt' ) . "' and
                endsAt >= '" . $this->post( 'endsAt' ) . "' and
                startsAt < '" . $this->post( 'endsAt' ) . "'
            ) ) and beautican = '" . $this->post( 'beautican' ) . "'" );

            $data   =   $query->result();

            if( $data ) {
                return $this->__failed();
            }

            $this->db->insert( store_prefix() . 'alvaro_appointments', [
                'startsAt'         =>  $this->post( 'startsAt' ),
                'endsAt'           =>  $this->post( 'endsAt' ),
                'ref_order'         =>  $this->post( 'ref_order' ),
                'title'             =>  $this->post( 'title' ),
                'beautican'         =>  $this->post( 'beautican' ),
                'date_creation'     =>  $this->post( 'date_creation' ),
                'author'            =>  $this->post( 'author' ),
            ]);

            $query  =   $this->db->order_by( 'id', 'desc' )->get( store_prefix() . 'alvaro_appointments' )->result();

            return $this->response( $query[0], 200 );
        }
        return $this->__failed();
    }

    /**
     *  appointment delete
     *  @param int appointment id
     *  @return json
    **/

    public function appointments_delete( $id )
    {
        $this->load->model( 'Nexo_Checkout' );

        $appointment    =   $this->db->where( 'id', $id )->get( store_prefix() . 'alvaro_appointments' )->result_array();

        if( $appointment ) {
            $this->db->where( 'ID', $appointment[0][ 'ref_order' ] )->delete( store_prefix() . 'nexo_commandes' );
            $this->Nexo_Checkout->commandes_delete( $appointment[0][ 'ref_order' ] );
        }

        $this->db->where( 'id', $id )->delete( store_prefix() . 'alvaro_appointments' );
        return $this->__success();
    }

    /**
     *  Log
     *  @param void
     *  @return void
    **/

    public function log_post()
    {
        $this->db->insert( store_prefix() . 'alvaro_log', [
            'description'       =>  $this->post( 'description' ),
            'author'            =>  $this->post( 'author' ),
            'date_creation'     =>  $this->post( 'date_creation' ),
            'title'             =>  $this->post( 'title' ),
            'ref_appointment'   =>  $this->post( 'ref_appointment' ),
        ]);

        $this->__success();
    }

}
