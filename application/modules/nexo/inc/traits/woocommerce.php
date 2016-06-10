<?php
include_once(APPPATH . '/modules/nexo/vendor/autoload.php');

use Automattic\WooCommerce\Client;
use \Curl\Curl;

trait Nexo_WooCommerce
{
	/**
	 * Connect to WooCommerce API
	 *
	 * @params key
	 * @params secret
	 * @params URL
	**/
	
	private function _construct( $key, $secret, $encoded_url )
	{
		$this->curl			=	new Curl;
		$this->WooCommerce	=	new Client(
			urldecode( $encoded_url ), 
			$key, 
			$secret,
			array(
				'version' => 'v3',
			)
		);
	}
	
	/**
	 * Woo Item
	 * Retreive item from a WooCommerce shop
	 * Syncing down will delete all item available on NexoPOS to use those on WooCommerce.
	 * NexoPOS orders may no longer be valid
	 * @params string key
	 * @params string secret
	 * @params string encoded url
	 * @return json
	**/
	
    public function woo_items_sync_down_get( $key, $secret, $encoded_url = 'http://localhost/wordpress' )
    {
		$this->load->helper( 'url_slug' );
        $this->_construct( $key, $secret, $encoded_url );
		
		// Get products
		$WooCommerceItems	=	$this->WooCommerce->get( 'products' );
		// Delete Items
		$this->curl->delete( site_url( array( 'rest', 'nexo', 'item', 'all' ) ) );
		// Post WooCommerce Items
		$skiped				=	0;
		
		if( count( $WooCommerceItems ) > 0 && is_array( $WooCommerceItems ) ) {
			foreach( $WooCommerceItems[ 'products' ] as $item ) {
				// $item[ 'downloadable' ] == false && $item[ 'virtual' ]
				if( true ) {
					// Copy Item image to upload dir
					
					// Generat SKU
					$array_title								=	explode( ' ', $item[ 'title' ] );
					$sku										=	implode( array_map( function( $index ) {
						$index	=	ucwords( $index );
						return $index[0];
					}, $array_title ) );

					// Post Item
					$this->curl->post( site_url( array( 'rest', 'nexo', 'item' ) ), array(
						'design'								=>	$item[ 'title' ],
						'date_creation'							=> 	$item[ 'created_at' ],
						'prix_de_vente'							=>	$item[ 'regular_price' ],
						'prix_promotionel'						=>	$item[ 'price' ],
						'apercu'								=>	$item[ 'images' ][0][ 'title' ],
						'description'							=>	$item[ 'description' ],
						'poids'									=>	empty( $item[ 'weight' ] ) ? 0 : $item[ 'weight' ],
						'ref_rayon'								=>	'1', // $item[ 'tags' ][0]
						'ref_shipping'							=>	'1',
						'ref_categorie'							=>	'1',
						'quantity'								=>	$item[ 'stock_quantity' ] == null ? 999 : $item[ 'stock_quantity' ],
						'quantite_restante'						=>	$item[ 'stock_quantity' ] == null ? 999 : $item[ 'stock_quantity' ],
						'quantite_vendue'						=>	0,
						'defectueux'							=>	0,
						'prix_dachat'							=>	$item[ 'price' ],
						'frais_accessoire'						=>	0,
						'cout_dachat'							=>	0,
						'taux_de_marge'							=>	0,
						'sku'									=>	$item[ 'sku' ] == '' ? $sku . date( 'His' )  : $item[ 'sku' ]
					) );
					
				} else {
					$skiped++;
				}
			}
			
			$this->response( array(
				'status'	=>	'success',
				'skiped'	=>	$skiped
			), 200 );
		}		
		
		$this->__failed();
    }
	
	/**
	 * Woo Sync Categories
	 * Retreive categories from a WooShop
	 * @params string key
	 * @params string secret
	 * @params string encoded url
	 * @return json
	**/
	
	public function woo_categories_sync_down_get( $key, $secret, $encoded_url = 'http://localhost/wordpress' )
	{
		$this->_construct( $key, $secret, $encoded_url );
		$WooCategories		=	$this->WooCommerce->get('products/categories');
		
		// Delete All categories		
		var_dump( $this->curl->delete( site_url( array( 'rest', 'nexo', 'category', 'all' ) ) ) );
		
		// Add categories
		if( is_array( $WooCategories ) ) {
			foreach( $WooCategories[ 'product_categories' ] as $categorie ) {
				var_dump( $categorie );
				var_dump( $this->curl->post( site_url( array( 'rest', 'nexo', 'category' ) ), array(
					'id'				=>	$categorie[ 'id' ],
					'name'				=>	$categorie[ 'name' ],
					'description'		=>	$categorie[ 'description' ],
					'author_id'			=>	1,
					'date_creation'		=>	date( DATE_ATOM, time() ), // change after
					'parent_id'			=>	$categorie[ 'parent' ]
				) ) );
			}
		}
	}
}
