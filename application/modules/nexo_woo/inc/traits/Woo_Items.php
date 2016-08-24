<?php
use Curl\Curl;
use Carbon\Carbon;

trait Woo_Items
{
	public function items_get()
	{
		$this->response( $this->Woo->get( 'products', array(
			'per_page'			=>	0 // big
		) ), 200 );
	}
	
	/**
	 * Récupération categories WooCommerce
	 * 
	**/
	
	public function sync_woo_items_get( $delete = true )
	{
		/**
		 * WooCommerce Part
		**/
		
		$merged_items		=	array();
		
		$woo_items			=	$this->WooCommerce->products->get();
		
		if( $woo_items[ 'products' ] ) {
			
			$Batch			=	array( 'delete'	=>	array() );
			
			foreach( $woo_items[ 'products' ] as $woo_item ) {
				
				/**
				 ** Sync from Woo disabled
				**/
				
				$item_name					=	preg_replace('/\s+/', '_', iconv('utf-8','ASCII//IGNORE//TRANSLIT', strtolower( trim( $woo_item[ 'title' ] ) ) ) );
				
				$merged_items[ $item_name ]	=	array(
					'ID'				=>	$woo_item[ 'id' ],
					'DESIGN'			=>	$woo_item[ 'title' ],
					'SKU'				=>	$woo_item[ 'sku' ],
					'DATE_CREATION'		=>	$woo_item[ 'created_at' ],
					'DATE_MOD'			=>	$woo_item[ 'updated_at' ],
					'PRIX_DE_VENTE'		=>	$woo_item[ 'price' ],
					'SHADOW_PRICE'		=>	$woo_item[ 'price' ],
					'PRIX_PROMOTIONEL'	=>	$woo_item[ 'regular_price' ],
					'QUANTITY'			=>	$woo_item[ 'stock_quantity' ],
					'QUANTITE_RESTANTE'	=>	$woo_item[ 'stock_quantity' ],
					'REF_CATEGORIE'		=>	@$woo_item[ 'categories' ][0],
					'DESCRIPTION'		=>	$woo_item[ 'description' ],
					'THUMB'				=>	'',
					'REF_STORE'		=>	0
				);				
			}
		}
		
		$this->response( $merged_items, 200 );
	}
	
	/**
	 * Get WooCommerce Items
	**/
	
	public function sync_get_woo_items_get()
	{
		$this->response( 
			$this->WooCommerce->products->get(),
			200 
		);
	}
	
	
	/**
	 * Delete Item
	**/
	
	function sync_delete_woo_item_get( $item_id )
	{
		$this->response( 
			$this->WooCommerce->products->delete( $item_id, true ), 
			200 
		);
	}
	
	/**
	 * NexoPOS categories 
	 *
	**/
	
	public function sync_nexopos_items_get( $delete = false )
	{
		/**
		 * NexoPOS Part
		**/
		
		$merged_items			=	array();
				
		$this->Curl					=	new Curl;
		$this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
		$nexo_items				=	$this->Curl->get( site_url( 'rest/nexo/item' ) );
		
		if( $nexo_items ) {
			// $this->Curl			=	new Curl;
			foreach( $nexo_items as $nexo_item ) {
				$item_name					=	preg_replace('/\s+/', '_', iconv('utf-8','ASCII//IGNORE//TRANSLIT', strtolower( trim( $nexo_item->DESIGN ) ) ) );
				$merged_items[ $item_name ]	=	( array )$nexo_item;
				// $this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
				// $this->Curl->delete( site_url( 'rest/nexo/category/' . $nexo_category->ID ) );
			}
			// Delete All Categories
			if( $delete == 'clear' ) {
				// $this->Curl->delete( site_url( 'rest/nexo/category_all' ) );
			}
		}
		
		$this->response( $merged_items, 200 );
	}
	
	/**
	 * Category Sync Phase 2
	 *
	**/
	
	public function sync_items_post()
	{
		$woo_categories			=	json_decode( $this->post( 'woo_categories' ), true );
		
		$this->WooCommerce->products->create( array(
			'title'				=>	$this->post( 'DESIGN' ),
			'type'				=>	'simple',
			'sku'				=>	$this->post( 'SKU' ),
			'regular_price'		=>	$this->post( 'PRIX_DE_VENTE' ),
			'manage_stock'		=>	true,
			'in_stock'			=>	intval( $this->post( 'QUANTITE_RESTANTE' ) ) > 0 ? true : false,
			'stock_quantity'	=>	$this->post( 'QUANTITE_RESTANTE' ),
			'categories'		=>	array( $woo_categories[ $this->post( 'REF_CATEGORIE' ) ][ 'id' ] ), 
		) );
						
		$this->__success();
	}
}