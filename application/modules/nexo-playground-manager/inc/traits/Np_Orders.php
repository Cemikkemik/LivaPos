<?php
Trait Np_Orders
{
	/**
	 * Search Order
	**/
	
	public function orders_get( $code = null, $action = 'search' ) 
	{
		$this->db
		->select( '*' )
		->from( store_prefix() . 'nexo_commandes_produits' )
		->join( store_prefix() . 'nexo_commandes', 
			store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner' 
		)
		->join( store_prefix() . 'nexo_articles', 
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner' 
		)
		->join( store_prefix() . 'nexo_commandes_meta', 
			store_prefix() . 'nexo_commandes_meta.REF_ORDER_ID = ' . store_prefix() . 'nexo_commandes.ID', 'left outer' 
		)
		->join( store_prefix() . 'nexo_articles_meta', 
			store_prefix() . 'nexo_articles_meta.REF_ARTICLE = ' . store_prefix() . 'nexo_articles.ID', 'inner' 
		)		
		->group_by( store_prefix() . 'nexo_commandes.ID' );
		
		if( $code != null ) {
			if( $action == 'search' ) {
				$this->db->like( store_prefix() . 'nexo_commandes.CODE', $code );
			} else {
				$this->db->where( store_prefix() . 'nexo_commandes.CODE', $code );
			}
		}
		
		$this->db->limit( 10 );
		
		$query	=	$this->db->get();
		
		$this->response( $query->result_array(), 200 );		
	}
	
	/**
	 * Order Details
	**/
	
	public function orders_items_get( $code ) 
	{
		$this->db
		->select( '*' )
		->from( store_prefix() . 'nexo_commandes_produits' )
		->join( store_prefix() . 'nexo_commandes', 
			store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner' 
		)
		->join( store_prefix() . 'nexo_articles', 
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner' 
		)
		->join( store_prefix() . 'nexo_articles_meta', 
			store_prefix() . 'nexo_articles_meta.REF_ARTICLE = ' . store_prefix() . 'nexo_articles.ID', 'inner' 
		)		
		
		db->like( store_prefix() . 'nexo_commandes.CODE', $CODE );
		
		$this->db->limit( 10 );
		
		$query	=	$this->db->get();
		
		$this->response( $query->result_array(), 200 );		
	}
}