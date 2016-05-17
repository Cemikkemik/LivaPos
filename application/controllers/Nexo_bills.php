<?php

defined('BASEPATH') OR exit('No direct script access allowed');

! is_file( APPPATH . '/libraries/REST_Controller.php' ) ? die( 'CodeIgniter RestServer is missing' ) : NULL;

include_once( APPPATH . '/libraries/REST_Controller.php' );

class Nexo_bills extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library( 'session' );	
		$this->load->database();
	}
	
	private function __success()
	{
		$this->response( array(
			'status'		=>	'success'
		), 200 );
	}
	
	/**
	 * Display a error json status
	 *
	 * @return json status
	**/
	
	private function __failed()
	{
		$this->response( array(
			'status'		=>	'failed'
		), 403 );
	}
	
	/**
	 * Return Empty
	 *
	**/
	
	private function __empty()
	{
		$this->response( array(
		), 200 );
	}
	
	/**
	 * Not found
	 *
	 *
	**/
	
	private function __404()
	{
		$this->response( array(
			'status'		=>	'404'
		), 404 );
	}
	
	/**
	 * Orders
	 *
	**/
	
	/**
	 * Order get
	 *
	 * @return json 
	**/
	
	function bills_get( $id = NULL, $filter = 'ID' )
	{
		// fetch product using an interval time
		if( $id != NULL ) {
			$this->db->where( $filter, $id );
		}
		
		$query	=	$this->db->get( 'nexo_commandes' );
		$result	=	$query->result();
		
		if( $result ) {
			$this->response( $result, 200 );
		} elseif( $id != NULL ) {
			$this->__404();
		} else {
			$this->__empty();
		}
	}
	
	/**
	 * Order delete
	 *
	**/
	
	function bills_delete()
	{
		$this->__failed();
	}
	
	/**
	 * Order put
	 *
	**/
	
	function bills_put()
	{
		$this->__failed();
	}
	
	/**
	 * Order insert
	 *
	**/
	
	function bills_post()
	{
		$this->__failed();
	}
	
	/**
	 * Order Advanced
	 *
	**/
	
	function bills_by_date_post( $filter = 'DATE_CREATION' )
	{
		$this->db->where( $filter . '>=', $this->post( 'start' ) );
		$this->db->where( $filter . '<=', $this->post( 'end' ) );
		
		$query	=	$this->db->get( 'nexo_premium_factures' );
		$result	=	$query->result();
		
		if( $result ) {
			$this->response( $result, 200 );
		} else {
			$this->__empty();
		}
	}
}