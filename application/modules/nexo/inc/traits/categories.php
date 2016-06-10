<?php
include_once(APPPATH . '/modules/nexo/vendor/autoload.php');

use Automattic\WooCommerce\Client;

trait Nexo_Categories
{
	/** 
	 * Create categories
	 *
	 * @params string name
	 * @params string description
	 * @params string date_creation
	 * @params string date_mod
	 * @params int author id
	 * @params int parent id
	**/
	
	public function category_post()
	{
		$data		=	array(			
			'NOM'			=>		$this->post( 'name' ),
			'DESCRIPTION'	=>		$this->post( 'description' ),
			'DATE_CREATION'	=>		$this->post( 'date_creation' ),
			'AUTHOR'		=>		$this->post( 'author_id' ),
			'PARENT_REF_ID'	=>		$this->post( 'parent_id' )
		);
		
		// Add index if isset
		if( $this->post( 'id' ) ) {
			$data[ 'ID' ]	=		$this->post( 'id' );
		}
		
		$this->db->insert( 'nexo_categories', $data );
		
		$this->__success();
	}
	
	/**
	 * Delete Category
	 *
	 * @params int category id
	**/
	
	public function category_delete( $id ) 
	{
		if( $id == 'all' ) {
			$this->db->where( 'ID >', 0 );
		} else {
			$this->db->where( 'ID', $id );
		}
		$this->db->delete( 'nexo_categories' );
		
		$this->__success();
	}
	 
}
