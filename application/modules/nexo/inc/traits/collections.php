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
}
