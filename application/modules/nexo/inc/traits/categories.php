<?php
include_once(APPPATH . '/modules/nexo/vendor/autoload.php');

trait Nexo_categories
{
	/**
	 * Get Categories
	 *
	 * @param int/string item 
	 * @param string filter
	 * @return json
	**/
	
    public function category_get( $item = null, $filter = null )
    {
		if( $filter != null ) {
			$this->db->where( $filter, $item );
		}
		
		$this->response( $this->db->get( store_prefix() . 'nexo_categories' )->result(), 200 );
    }
	
	/**
	 * Post Categories
	 * @return json
	**/
	
	public function category_post()
	{
		// Check if name already exists
		if( $filter != null ) {
			$this->db->where( 'LOWER(NOM)', strtolower( $this->post( 'name' ) ) );
		}
		
		$result					=		$this->db->get( store_prefix() . 'nexo_categories' )->result_array();
		
		if( $result ) {
			$this->__failed();
			return;
		}
		
		$data			=	array(
			'NOM'				=>		$this->post( 'name' ),
			'DESCRIPTION'		=>		$this->post( 'description' ),
			// 'DATE_CREATION'	=>		$
			'AUTHOR'			=>		$this->oauthlibrary->getKeyOwnerId(),
			'PARENT_REF_ID'		=>		$this->post( 'parent' ),
		);
		// ID is optional
		if( $this->post( 'id' ) ) {
			$data[ 'ID' ]	=	$this->post( 'id' );	
		}
		
		// Thumb
		if( $this->post( 'image' ) ) {
			$image				=	$this->post( 'image' );
			$image_name			=	basename( $image[ 'src' ] );
			$data[ 'THUMB' ]	=	$image_name;
			// Copy Thumb
			$this->load->library( 'simplefilemanager' );
			$this->simplefilemanager->file_copy( $image[ 'src' ], UPLOAD_PATH . '/categories/' );
		}
		
		$this->db->insert( store_prefix() . 'nexo_categories', $data );
		
		$this->__success();
	}
	
	/**
	 * Put Categories
	 * @return json
	**/
	
	public function category_put( $id )
	{
		$data					=	array(
			'NOM'				=>		$this->post( 'name' ),
			'DESCRIPTION'		=>		$tihs->post( 'description' ),
			// 'DATE_CREATION'	=>		$
			'AUTHOR'			=>		$this->oauthlibrary->getKeyOwnerId(),
			'PARENT_REF_ID'		=>		$this->post( 'parent' )
		);
		
		// Thumb
		if( $this->post( 'image' ) ) {
			$image				=	$this->post( 'image' );
			$image_name			=	basename( $image[ 'src' ] );
			$data[ 'THUMB' ]	=	$image_name;
			// Copy Thumb
			$this->load->library( 'simplefilemanager' );
			$this->simplefilemanager->file_copy( $image[ 'src' ], UPLOAD_PATH . '/categories/' );
		}
		
		if( $this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_categories', $data ) ) {		
			$this->__success();
		} else {
			$this->__failed();
		}
	}
	
	/**
	 * Delete Categories
	 * @return json
	**/
	
	public function category_delete( $id )
	{
		$this->db->where( 'ID', $id )->delete( store_prefix() . 'nexo_categories' );
		$this->__success();
	}
	
	/**
	 * Delete all
	 * @return json
	**/
	
	public function category_all_delete()
	{
		$this->db->where( 'ID >', 0 )->delete( store_prefix() . 'nexo_categories' );
		$this->__success();
	}
}
