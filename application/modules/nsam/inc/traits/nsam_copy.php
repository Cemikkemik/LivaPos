<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait Nsam_Copy
{
    /**
     *  Nsam Copy
     *  @return json
    **/

    public function copy_post()
    {
        $this->load->model( 'Nexo_Stores' );
        $this->load->dbforge();
        $store      =   $this->Nexo_Stores->get( $this->post( 'store_id' ) );

        if( $store ) {
            foreach( $this->post( 'elements' ) as $element ) {
                if( $element == 'articles' ) {
                    // Droping Item
                    foreach( array( '', '_defectueux', '_meta', '_variations' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo_articles' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '', '_defectueux', '_meta', '_variations' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo_articles' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo_articles' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'customers' ) {
                    // Droping Item
                    foreach( array( '_clients' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_clients' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'customers_g' ) {
                    // Droping Item
                    foreach( array( '_clients_groups' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_clients_groups' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'categories' ) {
                    // Droping Item
                    foreach( array( '_categories' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_categories' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'suppliers' ) {
                    // Droping Item
                    foreach( array( '_fournisseurs' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_fournisseurs' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'deliveries' ) {
                    // Droping Item
                    foreach( array( '_arrivages' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_arrivages' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                } else if( $element == 'radius' ) {
                    // Droping Item
                    foreach( array( '_rayons' ) as $table ) {
                        $this->db->query( 'TRUNCATE `' . $this->db->dbprefix . store_prefix() . 'nexo' . $table . '`;');
                    }

                    // Droping Item
                    foreach( array( '_rayons' ) as $table ) {
                        $data   =   $this
                        ->db->get( store_prefix( $this->post( 'store_id' ) ) . 'nexo' . $table )
                        ->result_array();

                        foreach( $data as $input ){
                            $this->db->insert( store_prefix() . 'nexo' . $table, $input );
                        }
                        unset( $data );
                    }
                }
            }

            $this->load->library( 'SimpleFileManager' );
            $SimpleFileManager  =   new SimpleFileManager;
            $SimpleFileManager->copy(
                'public/upload/store_' . $this->post( 'store_id' ),
                'public/upload/store_' . get_store_id()
            );

            return $this->__success();
        }
        return $this->__failed();
    }
}
