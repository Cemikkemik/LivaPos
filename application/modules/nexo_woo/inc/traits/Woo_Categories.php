<?php
use Curl\Curl;
use Carbon\Carbon;

trait Woo_Categories 
{
	public function product_category_get()
	{
		$this->response( $this->Woo->get('products/categories'), 200 );
	}
	
	/**
	 * Récupération categories WooCommerce
	 * 
	**/
	
	public function sync_woo_categories_get( $delete = false )
	{
		/**
		 * WooCommerce Part
		**/
		
		$merged_categories		=	array();
		
		$woo_categories			=	$this->Woo->get('products/categories', array(
			'per_page'			=>	99 // big
		) );
		
		if( $woo_categories ) {
			$Batch			=	array( 'delete'	=>	array() );
			foreach( $woo_categories as $woo_category ) {
				
				/**
				 ** Sync from Woo disabled
				**/
				
				$category_name		=	preg_replace('/\s+/', '_', iconv('utf-8','ASCII//IGNORE//TRANSLIT', strtolower( trim( $woo_category[ 'name' ] ) ) ) );
				
				$merged_categories[ $category_name ]	=	array(
					'ID'			=>	$woo_category[ 'id' ],
					'NOM'			=>	$woo_category[ 'name' ],
					'PARENT_REF_ID'	=>	$woo_category[ 'parent' ],
					'DESCRIPTION'	=>	$woo_category[ 'description' ],
					'THUMB'			=>	'',
					'REF_STORE'		=>	0
				);				
				
				// Delete all category
				$Batch[ 'delete' ][]		=	$woo_category[ 'id' ];
				
				// $this->Woo->delete('products/categories/' . $woo_category[ 'id' ], array( 'force'	=>	true ) );
			}
			
			// var_dump( $Batch );
			// Batch delete
			if( $delete == 'clear' ) {
				$this->Woo->post( 'products/categories/batch', $Batch );
			}
		}
		
		$this->response( $merged_categories, 200 );
	}	
	
	/**
	 * NexoPOS categories 
	 *
	**/
	
	public function sync_nexopos_categories_get( $delete = false )
	{
		/**
		 * NexoPOS Part
		**/
		
		$merged_categories			=	array();
				
		$this->Curl					=	new Curl;
		$this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
		$nexo_categories			=	$this->Curl->get( site_url( 'rest/nexo/category' ) );
		
		if( $nexo_categories ) {

			foreach( $nexo_categories as &$nexo_category ) {
				$nexo_category->DESCRIPTION	=	''; // htmlentities( $nexo_category->DESCRIPTION );
				$category_name		=	preg_replace('/\s+/', '_', iconv('utf-8','ASCII//IGNORE//TRANSLIT', strtolower( trim( $nexo_category->NOM ) ) ) );
				$merged_categories[ $category_name ]	=	( array )$nexo_category;
			}
			
			// Delete All Categories
			if( $delete == 'clear' ) {
				$this->Curl->delete( site_url( 'rest/nexo/category_all' ) );
			}
		}
				
		$this->response( ( array ) $merged_categories, 200 );
	}
	
	/**
	 * Category Sync Phase 2
	 *
	**/
	
	public function sync_categories_post( $type )
	{
		$merged_categories			=	json_decode( $this->post( 'merged_categories' ), true );
		$woo_categories				=	json_decode( $this->post( 'woo_categories' ), true );
		
		while( count( $merged_categories ) > 0 ) {
			// Fill back categories		
			foreach( $merged_categories as $index	=> $category ) {
				// First Categories
				if( $category[ 'PARENT_REF_ID' ] == '0' ) {
					
					/**
					 * Sync comes from NexoPOS to WooCommerce
					**/
					
					if( $type == 'nexopos_to_woocommerce' ) {
					
						$woo_data				=	array(
							'name'				=>	$category[ 'NOM' ],
							'description'		=>	$category[ 'DESCRIPTION' ],
						);
						
						// Retreive category details on WooCommerce and save it
						$woo_categories[ $category[ 'ID' ] ]	=	$this->Woo->post('products/categories', $woo_data );
					
					} elseif( $type == 'woocommerce_to_nexopos' ) {
						
						// Save category on NexoPOS.
						$woo_data[ 'id' ]				=	$category[ 'ID' ];
						$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
						
						$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
						
					}					
					
					// Unset category
					unset( $merged_categories[ $index ] );
					
					// Send to Client
					$this->response( array(
						'merged_categories'		=>	$merged_categories,
						'woo_categories'		=>	$woo_categories
					), 200 );
					
				} else {
					
					/**
					 * If category pulled from NexoPOS exists, it means it has been firstly saved on Woo Shop.
					 * Then we have a WooShop category details
					**/
					
					if( @$woo_categories[ $category[ 'PARENT_REF_ID' ] ] != null ) {
						
						/**
						 * Sync comes from NexoPOS to WooCommerce
						**/
						
						if( $type == 'nexopos_to_woocommerce' ) {
						
							$woo_data				=	array(
								'name'				=>	$category[ 'NOM' ],
								'description'		=>	$category[ 'DESCRIPTION' ],
								'parent'			=>	$woo_categories[ $category[ 'PARENT_REF_ID' ] ][ 'id' ]
							);
							
							// Retreive category details on WooCommerce and save it
							$woo_categories[ $category[ 'ID' ] ]	=	$this->Woo->post('products/categories', $woo_data );
						
						} else if( $type == 'woocommerce_to_nexopos' ) {
						
							// Save category on NexoPOS.
							$woo_data[ 'id' ]				=	$category[ 'ID' ];
							$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
							
							$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
						
						}
						
						// Unset category
						unset( $merged_categories[ $index ] );
						
						// Send to Client
						$this->response( array(
							'merged_categories'		=>	$merged_categories,
							'woo_categories'		=>	$woo_categories
						), 200 );
					}
					
				}
			}
		}
						
		$this->__success();
	}
	
	/**
	 * Get WooCommerce Items
	**/
	
	public function sync_get_woo_categories_get()
	{
		$this->response( 
			$woo_categories			=	$this->Woo->get('products/categories', array(
				'per_page'			=>	99 // big
			) ),
			200 
		);
	}
	
	
	/**
	 * Delete Item
	**/
	
	function sync_delete_woo_categories_get( $cat_id )
	{
		$this->response( 
			$this->Woo->delete('products/categories/' . $cat_id, array( 'force'	=>	true ) ), 
			200 
		);
	}
	
	/**
	 * Compare
	 * @param object
	 * @param object
	 * @return int
	**/
	
	private function compare( $a, $b )
	{
		return strcmp($a[ 'PARENT_REF_ID' ], $b[ 'PARENT_REF_ID' ]);
	}
}