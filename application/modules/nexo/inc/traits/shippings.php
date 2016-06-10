<?php
trait Nexo_shippings
{
	/**
	 * Create Shippings
	 * @return json
	**/
	
	public function shipping_create()
	{
		$this->db->insert( 'nexo_arrivages', array(
			'TITRE'            =>    $this->post( 'name' ),
			'DESCRIPTION'    =>    $this->post( 'description' ),
			'DATE_CREATION'    =>    $this->post( 'date_creation' ),
			'DATE_MOD'    =>    $this->post( 'date_edition' ),
			'AUTHOR'        =>    $this->post( 'author' ),
			'FOURNISSEUR_REF_ID'    =>    $this->post( 'provider' )
		) );
		
		$this->__success();
	}
}
