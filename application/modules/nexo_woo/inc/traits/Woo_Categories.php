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
	 * Launch Category Syncing
	**/
	
	public function sync_categories_get()
	{
		$merged_categories		=	array();		
		$woo_categories			=	$this->Woo->get('products/categories');
		
		if( $woo_categories ) {
			$Batch			=	array( 'delete'	=>	array() );
			foreach( $woo_categories as $woo_category ) {
				$merged_categories[ strtolower( trim( $woo_category[ 'name' ] ) ) ]	=	array(
					'ID'			=>	$woo_category[ 'id' ],
					'NOM'			=>	$woo_category[ 'name' ],
					'PARENT_REF_ID'	=>	$woo_category[ 'parent' ],
					'DESCRIPTION'	=>	$woo_category[ 'description' ]
				);
				// Delete all category
				$Batch[ 'delete' ][]		=	$woo_category[ 'id' ];
				// $this->Woo->delete('products/categories/' . $woo_category[ 'id' ], array( 'force'	=>	true ) );
			}
			// Batch delete
			$this->Woo->post( 'products/categories/batch', $Batch );
		}
		
		$this->Curl					=	new Curl;
		$this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
		$nexo_categories			=	$this->Curl->get( site_url( 'rest/nexo/category' ) );
		
		if( $nexo_categories ) {
			// $this->Curl			=	new Curl;
			foreach( $nexo_categories as $nexo_category ) {
				$merged_categories[ strtolower( trim( $nexo_category->NOM ) ) ]	=	( array )$nexo_category;
				$this->Curl->setHeader( 'X-API-KEY', $_SERVER[ 'HTTP_X_API_KEY' ] );
				$this->Curl->delete( site_url( 'rest/nexo/category/' . $nexo_category->ID ) );
			}
		}
		
		usort( $merged_categories, array( $this, 'compare' ) );
		
		// Fill back categories
		foreach( $merged_categories as $category ) {
			// Woo Categories
			if( $category[ 'PARENT_REF_ID' ] == '0' ){
				
				$woo_data					=	array(
					'name'				=>	$category[ 'NOM' ],
					'parent'			=>	0,
					'description'		=>	$category[ 'DESCRIPTION' ]
				);
				
				if( $category[ 'THUMB' ] != '' ) {
					$woo_data[ 'image' ]=	array( 'src' => upload_url() . 'categories/' . $category[ 'THUMB' ] );
				}
				
				$this->Woo->post('products/categories', $woo_data );
				
			} else {
				// Form now we fetch once
				if( ! isset( $parent_categories ) ) {
					$parent_categories	=	$this->Woo->get('products/categories');
				}
				
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
					
					$woo_data[ 'image' ]=	array( 'src' => upload_url() . 'categories/' . $category[ 'THUMB' ] );
				}
				
				$this->Woo->post('products/categories', $woo_data);
			}
			
			// Save category on NexoPOS.
			$woo_data[ 'id' ]				=	$category[ 'ID' ];
			$woo_data[ 'parent' ]			=	$category[ 'PARENT_REF_ID' ];
			$this->Curl->post( site_url( 'rest/nexo/category' ), $woo_data );
		}
				
		$this->__success();
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