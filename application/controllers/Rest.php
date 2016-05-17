<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once( APPPATH . '/libraries/REST_Controller.php' );

class Rest extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();		
		$this->load->library( 'session' );	
		$this->load->database();		
	}
	public function user_get( $id = NULL )
	{
		if( $id != NULL ) {
			$result			=	$this->db->where( 'id', $id )->get( 'aauth_users' )->result();
			if( $result ) {
				$this->response( $result, 200 );	
			} else {
				$this->response( $result, 404 );	
			}
			
		} else {
			$this->response( $this->db->get( 'aauth_users' )->result(), 200 );
		}
	}
	
	public function user_post()
	{
		$this->load->library( 'Aauth' );
		
		if( $this->aauth->create_user( $this->post( 'email' ), $this->post( 'pass' ), $this->post( 'name' ) ) ) {
			$this->response( array( 
				'status' => 'success'
			) );
		} else {
			$this->response( array( 
				'status' => 'failed'
			) );
		}		
	}
	
	public function user_put()
	{		
		$this->load->library( 'Aauth' );
		
		$request	=	$this->db->where( 'id', $this->put( 'id' ) )
		->set( 'email', $this->put( 'email' ) )
		->set( 'pass',	$this->aauth->hash_password( $this->put( 'pass' ), $this->put( 'id' ) ) )
		->set( 'name', 	$this->put( 'name' ) )
		->update( 'aauth_users' );
		
		if( $request ) {
			$this->response( array( 
				'status' => 'success'
			) );
		} else {
			$this->response( array( 
				'status' => 'failed'
			) );
		}
	}
	
	public function user_delete()
	{
		$this->load->library( 'Aauth' );
		
		if( $result		=	$this->aauth->delete_user( $this->post( 'id' ) ) ) {
			$this->response( $result, 200 );
		} else {
			$this->response( $result, 404 );
		}
	}
}