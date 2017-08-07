<?php
use Carbon\Carbon;

trait Nexo_collection
{
	/** 
	 * Get Collection
	**/
	
	public function collection_get( $id = null )
	{
		if( $id != null ) {
			$this->db->where( 'ID', $id );
		}
		
		$this->response( $this->db->get( store_prefix() . 'nexo_arrivages' )->result(), 200 );
	}

	/**
	 * Submit Delivery
	 * @param string shipping title
	 * @param string shipping description
	 * @return json response
	**/

	public function deliveries_post()
	{
		$this->db->insert( store_prefix() . 'nexo_arrivages', [
			'TITRE'		 		=>		$this->post( 'title' ),
			'DESCRIPTION' 		=>		$this->post( 'description' ),
			'AUTHOR'			=>		User::id(),
			'DATE_CREATION' 	=>		date_now()
		]);

		return $this->__success();
	}

	/**
	 * Edit Delivery
	 * @param int delivery id
	 * @param string delivery title
	 * @param string delivery description
	 * @return json
	**/

	public function deliveries_put( $id )
	{
		$this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_arrivages', [
			'TITRE'		 			=>		$this->put( 'title' ),
			'DESCRIPTION' 			=>		$this->put( 'description' ),
			'AUTHOR'				=>		User::id(),
			'DATE_MOD' 				=>		date_now()
		]);

		return $this->__success();
	}

	public function deliveries_get( $id = null )
	{
		if( $id != null ) {
			$this->db->where( 'ID', $id );
		}
		
		$this->response( $this->db->get( store_prefix() . 'nexo_arrivages' )->result(), 200 );
	}
}	
