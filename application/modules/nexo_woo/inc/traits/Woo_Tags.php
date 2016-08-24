<?php
use Curl\Curl;
use Carbon\Carbon;

// Unfinished

trait Woo_Tags
{
	public function product_category_get()
	{
		$this->response( $this->Woo->get('products/categories'), 200 );
	}
	
	/**
	 * Récupération categories WooCommerce
	 * 
	**/
	
	public function sync_woo_categories_get()
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
				
				$category_name		=	preg_replace('/\s+/', '_', strtolower( trim( $woo_category[ 'name' ] ) ) );
				
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
			$this->Woo->post( 'products/categories/batch', $Batch );
		}
		
		$this->response( $merged_categories, 200 );
	}
	
	/**
	 * NexoPOS categories 
	 *
	**/
	
	public function sync_nexopos_categories_get()
	{
		/**
		 * NexoPOS Part
		**/
		
		$merged_categories			=	array();
				
		$this->Curl					=	new Curl;
		$this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
		$nexo_categories			=	$this->Curl->get( site_url( 'rest/nexo/category' ) );
		
		if( $nexo_categories ) {
			// $this->Curl			=	new Curl;
			foreach( $nexo_categories as $nexo_category ) {
				$category_name		=	preg_replace('/\s+/', '_', strtolower( trim( $nexo_category->NOM ) ) );
				$merged_categories[ $category_name ]	=	( array )$nexo_category;
				// $this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
				// $this->Curl->delete( site_url( 'rest/nexo/category/' . $nexo_category->ID ) );
			}
			// Delete All Categories
			// $this->Curl->delete( site_url( 'rest/nexo/category_all' ) );
		}
		
		$this->response( $merged_categories, 200 );
	}
	
	/**
	 * Category Sync Phase 2
	 *
	**/
	
	public function sync_categories_post()
	{
		$merged_categories			=	json_decode( $this->post( 'merged_categories' ), true );
		// var_dump( $all_categories );die;
		$pushed_category			=	array();
		$woo_categories				=	array();
		
		while( count( $merged_categories ) > 0 ) {
			// Fill back categories		
			foreach( $merged_categories as $index	=> $category ) {
				// First Categories
				if( $category[ 'PARENT_REF_ID' ] == '0' ) {
					
					$woo_data				=	array(
						'name'				=>	$category[ 'NOM' ],
						'description'		=>	$category[ 'DESCRIPTION' ],
					);
					
					// Retreive category details on WooCommerce and save it
					$woo_categories[ $category[ 'ID' ] ]	=	$this->Woo->post('products/categories', $woo_data );
					
					// Save category on NexoPOS.
					$woo_data[ 'id' ]				=	$category[ 'ID' ];
					$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
					
					$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
					
					// Unset category
					unset( $merged_categories[ $index ] );
					
				} else {
					
					/**
					 * If category pulled from NexoPOS exists, it means it has been firstly saved on Woo Shop.
					 * Then we have a WooShop category details
					**/
					
					if( @$woo_categories[ $category[ 'PARENT_REF_ID' ] ] != null ) {
						
						$woo_data				=	array(
							'name'				=>	$category[ 'NOM' ],
							'description'		=>	$category[ 'DESCRIPTION' ],
							'parent'			=>	$woo_categories[ $category[ 'PARENT_REF_ID' ] ][ 'id' ]
						);
						
						// Retreive category details on WooCommerce and save it
						$woo_categories[ $category[ 'ID' ] ]	=	$this->Woo->post('products/categories', $woo_data );
						
						// Save category on NexoPOS.
						$woo_data[ 'id' ]				=	$category[ 'ID' ];
						$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
						
						$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
						
						// Unset category
						unset( $merged_categories[ $index ] );
					}
					
				}
			}
		}
		
		// Fill back categories		
		/** foreach( $merged_categories as $category ) {
			// Woo Categories
			if( $category[ 'PARENT_REF_ID' ] == '0' ){
				
				$woo_data					=	array(
					'name'					=>	$category[ 'NOM' ],
					'parent'				=>	0,
					'description'			=>	$category[ 'DESCRIPTION' ]
				);
				
				if( $category[ 'THUMB' ] != '' ) {
					// $woo_data[ 'image' ]=	array( 'src' => upload_url() . 'categories/' . $category[ 'THUMB' ] );
				}
				
				$this->Woo->post( 'products/categories', $woo_data );
				
			} else {
				
				// Form now we fetch once
				// if( ! isset( $parent_categories ) ) {
				$parent_categories	=	$this->Woo->get('products/categories');
				// }
				
				$old_parent_id			=	$category[ 'PARENT_REF_ID' ];
				$old_parent_name		=	null;
				foreach( $merged_categories as $_x_category ) {
					if( $_x_category[ 'ID' ] == $old_parent_id ) {
						$old_parent_name	=	$_x_category[ 'NOM' ];
						break;
					}
				}
				
				$parent_id				=	0;
				foreach( $parent_categories as $_category ) {
					if( $_category[ 'name' ] == $old_parent_name ) {
						$parent_id		=	$_category[ 'id' ];
						break;
					}
				}
				
				$woo_data					=	array(
					'name'				=>	$category[ 'NOM' ],
					'parent'			=>	$parent_id,
					'description'		=>	$category[ 'DESCRIPTION' ],
				);
				
				if( $category[ 'THUMB' ] != '' ) {
					
					// $woo_data[ 'image' ]=	array( 'src' => upload_url() . 'categories/' . $category[ 'THUMB' ] );
				}
				
				$this->Woo->post('products/categories', $woo_data);
			}
			
			// Save category on NexoPOS.
			$woo_data[ 'id' ]				=	$category[ 'ID' ];
			$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
			
			$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
		} **/
				
		$this->__success();
	}
	
	/**
	 * Save Category to WooCommerce
	**/
	
	public function sync_to_woo_post()
	{
		$category_array			=	array(
			'name'			=>		$this->post( 'name' ),
			'description'	=>		$this->post( 'description' )
		);
		
		if( $this->post( 'parent' ) ) {
			$category_array[ 'parent' ]		=	$this->post( 'parent' );
		}
		
		$this->response( $this->Woo->post('products/categories', $category_array ), 200 );
	}
	
	/**
	 * Sync to NexoPOS
	**/
	
	public function sync_to_nexopos_post()
	{
		$category_array			=	array(
			'id'				=>	$this->post( 'id' ),
			'parent'			=>	$this->post( 'parent' ),
			'description'		=>	$this->post( 'description' ),
			'name'				=>	$this->post( 'name' )
		);
		
		$this->Curl->post( site_url( 'rest/nexo/category' ), $category_array );
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