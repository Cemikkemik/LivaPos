<?php

use	Dompdf\Dompdf;

trait Nexo_Restaurant_Tables_Trait
{
	/**
	 * Get Table
	**/
	
	public function tables_get( $id = null ) 
	{
		if( $id != null ) {
			$this->db->where( 'ID', $id );
		}
		$this->response( $this->db->get( store_prefix() . 'nexo_restaurant_tables' )->result() );
	}
	
	/**
	 * Edit Table
	**/
	
	public function tables_put( $id ) 
	{
		$data		=	array();
		
		$this->put( 'NAME' ) 			? $data[ 'NAME' ]				= $this->put( 'NAME' ) : null;
		$this->put( 'DESCRIPTION' ) 	? $data[ 'DESCRIPTION' ]		= $this->put( 'DESCRIPTION' ) : null;
		$this->put( 'AUTHOR' ) 			? $data[ 'AUTHOR' ]				= $this->put( 'AUTHOR' ) : null;
		$this->put( 'DATE_CREATION' ) 	? $data[ 'DATE_CREATION' ]		= $this->put( 'DATE_CREATION' ) : null;
		$this->put( 'DATE_MOD' ) 		? $data[ 'DATE_MOD' ]			= $this->put( 'DATE_MOD' ) : null;
		$this->put( 'STATUS' ) 			? $data[ 'STATUS' ]				= $this->put( 'STATUS' ) : null;
		$this->put( 'REF_GROUP' ) 		? $data[ 'REF_GROUP' ]			= $this->put( 'REF_GROUP' ) : null;
		$this->put( 'NAME' ) 			? $data[ 'NAME' ]				= $this->put( 'NAME' ) : null;
		
		$this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_restaurant_tables', $data );
		
		$this->__success();
	}
}