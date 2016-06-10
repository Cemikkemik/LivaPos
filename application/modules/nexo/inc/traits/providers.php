<?php
trait Nexo_providers
{
	/**
	 * Create Shippings
	 * @return json
	**/
	
	public function provider_create()
	{
		$this->db->insert( 'nexo_fournisseurs', array(
			'NOM'            =>    $this->post( 'name' ),
			'BP'   			 =>    $this->post( 'bp' ),
			'EMAIL'    		=>    $this->post( 'email' ),
			'DATE_CREATION'    =>    $this->post( 'date_creation' ),
			'DATE_MOD'        =>    $this->post( 'date_mod' ),
			'AUTHOR'    =>    $this->post( 'author' ),
			'DESCRIPTION'    =>    $this->post( 'description' )
		) );
		
		$this->__success();
	}
}
