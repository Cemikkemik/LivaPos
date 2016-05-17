<?php
class Nexo_Categories extends CI_Model
{
	/**
	 * Get categories
	 *
	 * @param int
	 * @return array/bool
	**/
	
	public function get( $id = NULL, $filter = 'as_id' ) 
	{
		if( $id != NULL ) {
			if( $filter == 'as_id' ) {
				$this->db->where( 'ID', $id );
			} else if( $filter == 'as_nom' ){
				$this->db->where( 'NOM', $id );
			}
		}
		
		$query	=	$this->db->get( 'nexo_categories' );
		return $query->result_array();
	}
}