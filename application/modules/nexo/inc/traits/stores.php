<?php
trait Nexo_stores
{
    /**
	 * Get Stores
	 * @param int store id
	 * @return json object
	**/
	
	public function stores_get( $id = null ) 
	{
		if( $id != null ) {
			$this->db->where( 'ID', $id );
		}
		
		$this->response( $this->db->get( 'nexo_stores' )->result(), 200 );
	}
}
