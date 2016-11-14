<?php
class Nexo_Restaurant extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/** 
	 * Get Kitchens
	 * @param int/void kitchen id
	 * @return array
	**/
	
	public function get_kitchen( $id = null )
	{
		if( $id != null ){
			$this->db->where( 'ID', $id );
		}
		
		return $this->db->get( 'nexo_restaurant_kitchens' )
		->result_array();
	}
	
	/**
	 * Get Printer
	 *
	 * @return Array
	**/
	
	public function get_printer()
	{
		if( function_exists( 'printer_list' ) ) {
			
			// ob_start();
			
			$printers				=	printer_list( PRINTER_ENUM_LOCAL );
			
			$fake_printers			=	$printers;
		
			$printers_namespaces	=	array();
						
			if( is_array( $printers ) ) {
				foreach( $printers as $printer ) {
					$printers_namespaces[ $printer[ 'NAME' ] ] 	=	$printer[ 'NAME' ];
				}
			}

			return $printers_namespaces;
		}
		return array();
	}
}
