<?php
trait Nar_Reports
{
	/**
	 * Get Summary Report for categories
	**/
	
	public function categories_recap_post()
	{
		if( is_array( $this->post( 'categories_id' ) ) ) {
			
			// ,			SUM(' . $this->db->dbprefix . 'nexo_commandes_produits.PRIX_TOTAL) as TOTAL_SALES
			$this->db->select( '*,
			nexo_articles.ID as ITEM_ID,
			nexo_categories.ID as CAT_ID' )
			
			->from( 'nexo_articles' )
			->join( 'nexo_categories', 'nexo_categories.ID = nexo_articles.REF_CATEGORIE', 'inner' );
			
			foreach( $this->post( 'categories_id' ) as $key => $cat_id ) {			
				if( $key == 0 ) {
					$this->db->where( 'nexo_articles.REF_CATEGORIE', $cat_id );			
				} else {
					$this->db->or_where( 'nexo_articles.REF_CATEGORIE', $cat_id );			
				}
			}
			 
			$query	=	$this->db
			->get();
			
			$this->response( $query->result(), 200 );
		}
	}
	
	/**
	 * Get Purchase Price
	**/
	
	public function categories_purchase_price_post()
	{
		if( is_array( $this->post( 'categories_id' ) ) ) {
			
			$this->db->select( '*,
			nexo_articles.ID as ITEM_ID,
			nexo_categories.ID as CAT_ID' )
			
			->from( 'nexo_articles' )
			->join( 'nexo_categories', 'nexo_categories.ID = nexo_articles.REF_CATEGORIE', 'inner' )
			->join( 'nexo_commandes_produits', 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = nexo_articles.CODEBAR', 'inner' );
		
			$this->db->where( 'nexo_categories.ID', $this->post( 'cat_id' ) );
						 
			$query	=	$this->db
			->get();
			
			$this->response( $query->result(), 200 );
		}
	}
}
