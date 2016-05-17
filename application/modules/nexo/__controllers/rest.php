<?php
class Nexo_Rest extends CI_Model
{
	function __construct( $args )
	{
		parent::__construct();
		if( is_array( $args ) && count( $args ) > 1 ) {
			if( method_exists( $this, $args[1] ) ){
				call_user_func_array( array( $this, $args[1] ), array_slice( $args, 2 ) ); 
			} 		
		}
	}
	
	/**
	 * Deprecated
	**/
	
	function get( $element, $as = 'ID', $action = 'default', $order = NULL, $table = NULL )
	{
		if( $as == 'null' ){
			
			if( $order != NULL && $table != NULL ) {
				if( in_array( $order, array( 'asc', 'desc' ) ) ) {
					$this->db->order_by( $table, $order );
				}
			}
			
			$query	=	$this->db->get( $element );
			echo json_encode( $query->result_array() );
		} else {
			if( $action == 'default' ) {
				$query	=	$this->db->where( $as, $this->input->post( 'key' ) )->get( $element );
			} else if( $action == 'filter_date_interval' ) {
				$query	=	$this->db
					->where( $as . '>=', $this->input->post( 'key' ) . ' 00:00:00' )
					->where( $as . '<=', $this->input->post( 'key' ) . ' 23:59:59' );
			} else if( $action == 'lt' ) {
				$query	=	$this->db
						->where( $as . ' <', $this->input->post( 'key' ) );
			} else if( $action == 'lte' ) {
				$query	=	$this->db
						->where( $as . ' <=', $this->input->post( 'key' ) );
			} else if( $action == 'gt' ) {
				$query	=	$this->db
						->where( $as . ' >', $this->input->post( 'key' ) );
			} else if( $action == 'gte' ) {
				$query	=	$this->db
						->where( $as . ' >=', $this->input->post( 'key' ) );
						
			}
			
			$query	=	$this->db->get( $element );
			echo json_encode( $query->result_array() );
		}
	}
	
	/** 
	 * Should return JSON format
	**/
	
	function trigger( $context, $class, $method )
	{
		$args	=	func_get_args();
		$this->load->$context( $class );
		echo call_user_func_array( array( $this->$class, $method ), array_slice( $args, 3 ) );		
	}
	
	/**
	 * Trigger Post
	**/
	
	function post( $context, $class, $method )
	{
		$args	=	array_values( $_POST );
		$this->load->$context( $class );
		echo call_user_func_array( array( $this->$class, $method ), $args );		
	}
	
}
new Nexo_Rest( $this->args );