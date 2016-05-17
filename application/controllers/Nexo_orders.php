<?php

defined('BASEPATH') OR exit('No direct script access allowed');

! is_file( APPPATH . '/libraries/REST_Controller.php' ) ? die( 'CodeIgniter RestServer is missing' ) : NULL;

include_once( APPPATH . '/libraries/REST_Controller.php' );

class Nexo_orders extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library( 'session' );	
		$this->load->database();
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
	 * Success
	 *
	**/
	
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
	
	function orders_get( $id = NULL, $filter = 'ID' )
	{
		// fetch product using an interval time
		if( $id != NULL ) {
			$this->db->where( $filter, $id );
		}
		
		$query	=	$this->db->get( 'nexo_commandes' );
		$result	=	$query->result();
		
		if( $result ) {
			$this->response( $result, 200 );
		} else if( $id != NULL ) {
			$this->__404();
		} else {
			$this->__empty();
		}
	}
	
	/**
	 * Order delete
	 *
	**/
	
	function orders_delete()
	{
		$this->__failed();
	}
	
	/**
	 * Order put
	 *
	**/
	
	function orders_put()
	{
		$this->__failed();
	}
	
	/**
	 * Order insert
	 *
	**/
	
	function orders_insert()
	{
		$this->__failed();
	}
	
	/**
	 * Order Advanced
	 *
	**/
	
	function orders_by_date_post( $filter = 'DATE_CREATION' )
	{
		$this->db->where( $filter . '>=', $this->post( 'start' ) );
		$this->db->where( $filter . '<=', $this->post( 'end' ) );
		
		$query	=	$this->db->get( 'nexo_commandes' );
		$result	=	$query->result();
		
		if( $result ) {
			$this->response( $result, 200 );
		} else {
			$this->__empty();
		}
	}
	
	/**
	 * Get order items
	 *
	**/
	
	function order_items_get( $order_code ) {
		$this->db->where( 'REF_COMMAND_CODE', $order_code );
		$query		=	$this->db->get( 'nexo_commandes_produits' );
		$result		=	$query->result();
		if( $result ) {
			$this->response( $result, 200 );
		} else {
			$this->__empty();
		}
	}
}