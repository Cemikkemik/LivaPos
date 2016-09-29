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
		
		$returned				=	$this->WooCommerce->products->create( array(
			'title'				=>	$this->post( 'DESIGN' ),
			'type'				=>	'simple',
			'sku'				=>	$this->post( 'SKU' ),
			'regular_price'		=>	$this->post( 'PRIX_DE_VENTE' ),
			'manage_stock'		=>	true,
			'in_stock'			=>	intval( $this->post( 'QUANTITE_RESTANTE' ) ) > 0 ? true : false,
			'stock_quantity'	=>	$this->post( 'QUANTITE_RESTANTE' ),
			'categories'		=>	array( $woo_categories[ $this->post( 'REF_CATEGORIE' ) ][ 'id' ] ), 
			'images'			=>	array( 
				array(
					'src'			=>	upload_url() . $this->post( 'APERCU' ), // 
					'position'		=>	0,
					'title'			=>	$this->post( 'DESIGN' ) . ' image',
					'alt'			=>	$this->post( 'DESIGN' ) . ' image'
				)
			)
		) );
		
		
		// var_dump( $returned );
		
		/**
		array(2) {
  ["product"]=>
  array(58) {
    ["title"]=>
    string(9) "Article 1"
    ["id"]=>
    int(470)
    ["created_at"]=>
    string(20) "2016-09-12T12:09:56Z"
    ["updated_at"]=>
    string(20) "2016-09-12T12:09:56Z"
    ["type"]=>
    string(6) "simple"
    ["status"]=>
    string(7) "publish"
    ["downloadable"]=>
    bool(false)
    ["virtual"]=>
    bool(false)
    ["permalink"]=>
    string(45) "http://localhost/wordpress/produit/article-1/"
    ["sku"]=>
    string(4) "UGS1"
    ["price"]=>
    string(6) "100.00"
    ["regular_price"]=>
    string(6) "100.00"
    ["sale_price"]=>
    NULL
    ["price_html"]=>
    string(120) "<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&pound;</span>100.00</span>"
    ["taxable"]=>
    bool(false)
    ["tax_status"]=>
    string(7) "taxable"
    ["tax_class"]=>
    string(0) ""
    ["managing_stock"]=>
    bool(false)
    ["stock_quantity"]=>
    int(0)
    ["in_stock"]=>
    bool(true)
    ["backorders_allowed"]=>
    bool(false)
    ["backordered"]=>
    bool(false)
    ["sold_individually"]=>
    bool(false)
    ["purchaseable"]=>
    bool(true)
    ["featured"]=>
    bool(false)
    ["visible"]=>
    bool(true)
    ["catalog_visibility"]=>
    string(7) "visible"
    ["on_sale"]=>
    bool(false)
    ["product_url"]=>
    string(0) ""
    ["button_text"]=>
    string(0) ""
    ["weight"]=>
    NULL
    ["dimensions"]=>
    array(4) {
      ["length"]=>
      string(0) ""
      ["width"]=>
      string(0) ""
      ["height"]=>
      string(0) ""
      ["unit"]=>
      string(2) "cm"
    }
    ["shipping_required"]=>
    bool(true)
    ["shipping_taxable"]=>
    bool(true)
    ["shipping_class"]=>
    string(0) ""
    ["shipping_class_id"]=>
    NULL
    ["description"]=>
    string(0) ""
    ["short_description"]=>
    string(0) ""
    ["reviews_allowed"]=>
    bool(true)
    ["average_rating"]=>
    string(4) "0.00"
    ["rating_count"]=>
    int(0)
    ["related_ids"]=>
    array(0) {
    }
    ["upsell_ids"]=>
    array(0) {
    }
    ["cross_sell_ids"]=>
    array(0) {
    }
    ["parent_id"]=>
    int(0)
    ["categories"]=>
    array(1) {
      [0]=>
      string(7) "For Men"
    }
    ["tags"]=>
    array(0) {
    }
    ["images"]=>
    array(1) {
      [0]=>
      array(7) {
        ["id"]=>
        int(0)
        ["created_at"]=>
        string(20) "2016-09-12T12:09:59Z"
        ["updated_at"]=>
        string(20) "2016-09-12T12:09:59Z"
        ["src"]=>
        string(87) "http://localhost/wordpress/wp-content/plugins/woocommerce/assets/images/placeholder.png"
        ["title"]=>
        string(9) "Etiquette"
        ["alt"]=>
        string(9) "Etiquette"
        ["position"]=>
        int(0)
      }
    }
    ["featured_src"]=>
    string(0) ""
    ["attributes"]=>
    array(0) {
    }
    ["downloads"]=>
    array(0) {
    }
    ["download_limit"]=>
    int(0)
    ["download_expiry"]=>
    int(0)
    ["download_type"]=>
    string(0) ""
    ["purchase_note"]=>
    string(0) ""
    ["total_sales"]=>
    int(0)
    ["variations"]=>
    array(0) {
    }
    ["parent"]=>
    array(0) {
    }
  }
  ["http"]=>
  array(2) {
    ["request"]=>
    array(7) {
      ["headers"]=>
      array(3) {
        [0]=>
        string(24) "Accept: application/json"
        [1]=>
        string(30) "Content-Type: application/json"
        [2]=>
        string(44) "User-Agent: WooCommerce API Client-PHP/2.0.1"
      }
      ["method"]=>
      string(4) "POST"
      ["url"]=>
      string(288) "http://localhost/wordpress/wc-api/v2/products?oauth_consumer_key=ck_5bad8dfa13f50277e0dc2c44437aecc5780e1623&oauth_timestamp=1473682195&oauth_nonce=69b766a7871ec22f66b3c3fb4daefaa4b5afe96f&oauth_signature_method=HMAC-SHA256&oauth_signature=VT2PgVkGT7S80G4Wn8F3gJ3gT%2BsfGp6KHTGxiyRsgnk%3D"
      ["params"]=>
      array(5) {
        ["oauth_consumer_key"]=>
        string(43) "ck_5bad8dfa13f50277e0dc2c44437aecc5780e1623"
        ["oauth_timestamp"]=>
        int(1473682195)
        ["oauth_nonce"]=>
        string(40) "69b766a7871ec22f66b3c3fb4daefaa4b5afe96f"
        ["oauth_signature_method"]=>
        string(11) "HMAC-SHA256"
        ["oauth_signature"]=>
        string(44) "VT2PgVkGT7S80G4Wn8F3gJ3gT+sfGp6KHTGxiyRsgnk="
      }
      ["data"]=>
      array(1) {
        ["product"]=>
        array(8) {
          ["title"]=>
          string(9) "Article 1"
          ["type"]=>
          string(6) "simple"
          ["sku"]=>
          string(4) "UGS1"
          ["regular_price"]=>
          string(3) "100"
          ["manage_stock"]=>
          bool(true)
          ["in_stock"]=>
          bool(true)
          ["stock_quantity"]=>
          string(5) "80548"
          ["categories"]=>
          array(1) {
            [0]=>
            int(57)
          }
        }
      }
      ["body"]=>
      string(163) "{"product":{"title":"Article 1","type":"simple","sku":"UGS1","regular_price":"100","manage_stock":true,"in_stock":true,"stock_quantity":"80548","categories":[57]}}"
      ["duration"]=>
      float(3.78369)
    }
    ["response"]=>
    array(3) {
      ["body"]=>
      string(1579) "{"product":{"title":"Article 1","id":470,"created_at":"2016-09-12T12:09:56Z","updated_at":"2016-09-12T12:09:56Z","type":"simple","status":"publish","downloadable":false,"virtual":false,"permalink":"http:\/\/localhost\/wordpress\/produit\/article-1\/","sku":"UGS1","price":"100.00","regular_price":"100.00","sale_price":null,"price_html":"<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">&pound;<\/span>100.00<\/span>","taxable":false,"tax_status":"taxable","tax_class":"","managing_stock":false,"stock_quantity":0,"in_stock":true,"backorders_allowed":false,"backordered":false,"sold_individually":false,"purchaseable":true,"featured":false,"visible":true,"catalog_visibility":"visible","on_sale":false,"product_url":"","button_text":"","weight":null,"dimensions":{"length":"","width":"","height":"","unit":"cm"},"shipping_required":true,"shipping_taxable":true,"shipping_class":"","shipping_class_id":null,"description":"","short_description":"","reviews_allowed":true,"average_rating":"0.00","rating_count":0,"related_ids":[],"upsell_ids":[],"cross_sell_ids":[],"parent_id":0,"categories":["For Men"],"tags":[],"images":[{"id":0,"created_at":"2016-09-12T12:09:59Z","updated_at":"2016-09-12T12:09:59Z","src":"http:\/\/localhost\/wordpress\/wp-content\/plugins\/woocommerce\/assets\/images\/placeholder.png","title":"Etiquette","alt":"Etiquette","position":0}],"featured_src":"","attributes":[],"downloads":[],"download_limit":0,"download_expiry":0,"download_type":"","purchase_note":"","total_sales":0,"variations":[],"parent":[]}}"
      ["code"]=>
      int(201)
      ["headers"]=>
      array(5) {
        ["Date"]=>
        string(30) " Mon, 12 Sep 2016 12:09:55 GMT"
        ["Server"]=>
        string(48) " Apache/2.4.17 (Win32) OpenSSL/1.0.2d PHP/5.6.23"
        ["X-Powered-By"]=>
        string(11) " PHP/5.6.23"
        ["Content-Length"]=>
        string(5) " 1579"
        ["Content-Type"]=>
        string(32) " application/json; charset=UTF-8"
      }
    }
  }
		}
		**/
		
		// var_dump( upload_url() . $this->post( 'APERCU' ) );
		
		/**
		$data					=	array(
			'name'				=>	$this->post( 'DESIGN' ),
			'type'				=>	'simple',
			'sku'				=>	$this->post( 'SKU' ),
			'regular_price'		=>	$this->post( 'PRIX_DE_VENTE' ),
			'manage_stock'		=>	true,
			'in_stock'			=>	intval( $this->post( 'QUANTITE_RESTANTE' ) ) > 0 ? true : false,
			'stock_quantity'	=>	$this->post( 'QUANTITE_RESTANTE' ),
			'categories'		=>	array( $woo_categories[ $this->post( 'REF_CATEGORIE' ) ][ 'id' ] ), 
			'images'			=>	array( 
				'src'			=>	'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg', // upload_url() . $this->post( 'APERCU' )
				'position'		=>	1,
				'title'			=>	$this->post( 'DESIGN' ) . ' image',
				'alt'			=>	$this->post( 'DESIGN' ) . ' image'
			)
		);
		
		// /' . $returned[ 'product' ][ 'id' ]
		
		$this->Woo->post( 'products', $data );
		**/
						
		$this->__success();
	}
}