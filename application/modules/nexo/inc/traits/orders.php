<?php

use Curl\Curl;

trait Nexo_orders
{
    /**
     * Get Order
     * @params string/int
     * @params string
     * @return json
    **/
    
    public function order_get($var = null, $filter = 'ID')
    {
        if ($var != null) {
            $this->db->where($filter, $var);
        }
        $query    =    $this->db->get( store_prefix() . 'nexo_commandes');
        $this->response($query->result(), 200);
    }
    
    /**
     * Post Order
     * @params int Author id
     * @return json
    **/
    
    public function order_post($author_id)
    {
        $this->load->model('Nexo_Checkout');
        
        $order_details            =    array();
        
        $order_details            =    array(
            'RISTOURNE'            =>    $this->post('RISTOURNE'),
            'REMISE'            =>    $this->post('REMISE'),
            'RABAIS'            =>    $this->post('RABAIS'),
            'GROUP_DISCOUNT'    =>    $this->post('GROUP_DISCOUNT'),
            'TOTAL'                =>    $this->post('TOTAL'),
            'AUTHOR'            =>    $author_id,
            'PAYMENT_TYPE'        =>    $this->post('PAYMENT_TYPE'),
            'REF_CLIENT'        =>    $this->post('REF_CLIENT'),
            'TVA'                =>    $this->post('TVA'),
            'SOMME_PERCU'        =>    $this->post('SOMME_PERCU'),
            'CODE'                =>    $this->Nexo_Checkout->shuffle_code(),
            'DATE_CREATION'        =>    $this->post('DATE_CREATION'),
			'DESCRIPTION'			=>	$this->post( 'DESCRIPTION' ),
			'REF_REGISTER'		=>	$this->post( 'REGISTER_ID' ),
			// @since 2.7.10
			'TITRE'				=>	$this->post( 'TITRE' ) != null ? $this->post( 'TITRE' ) : ''
        );
		
        // Order Type
		// @since 2.7.1 if a custom type is submited this type replace default order type
		if( ! $this->post( 'TYPE' ) ) {
			if (__floatval($this->post('SOMME_PERCU')) >= __floatval($this->post('TOTAL'))) {
				$order_details[ 'TYPE' ]    =    'nexo_order_comptant'; // Comptant
			} elseif (__floatval($this->post('SOMME_PERCU')) == 0) {
				$order_details[ 'TYPE' ]    =   'nexo_order_devis'; // Devis
			} elseif (__floatval($this->post('SOMME_PERCU')) < __floatval($this->post('TOTAL')) && __floatval($this->post('SOMME_PERCU')) > 0) {
				$order_details[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
			}
		} else {
			$order_details[ 'TYPE' ]		=	$this->post( 'TYPE' );
		}
        
        // Increase customers purchases
        $query                        	=    $this->db->where('ID', $this->post('REF_CLIENT'))->get( store_prefix() . 'nexo_clients');
        $result                        	=    $query->result_array();
        $total_commands                	=    intval($result[0][ 'NBR_COMMANDES' ]) + 1;
        $overal_commands            	=    intval($result[0][ 'OVERALL_COMMANDES' ]) + 1;
        
        $this->db->set('NBR_COMMANDES', $total_commands);
        $this->db->set('OVERALL_COMMANDES', $overal_commands);
        
        // Disable automatic discount
        if ($this->post('REF_CLIENT') != $this->post('DEFAULT_CUSTOMER')) {
        
            // Verifie si le client doit profiter de la réduction
            if ($this->post('DISCOUNT_TYPE') != 'disable') {
                // On définie si en fonction des réglages, l'on peut accorder une réduction au client
                if ($total_commands >= __floatval($this->post('HMB_DISCOUNT')) - 1 && $result[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                    $this->db->set('DISCOUNT_ACTIVE', 1);
                } elseif ($total_commands >= $this->post('HMB_DISCOUNT') && $result[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                    $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                    $this->db->set('NBR_COMMANDES', 1); // le nombre de commande est également désactivé
                }
            }
        }
        // fin désactivation réduction auto pour le client par défaut
        $this->db->where('ID', $this->post('REF_CLIENT'))
        ->update( store_prefix() . 'nexo_clients');
        
        // Save Order items

        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE, STOCK_ENABLED );
        **/
        
        foreach ($this->post('ITEMS') as $item) {
			
			/**
			 * If Stock Enabled is active
			**/
			
			if( intval( $item[6] ) == 1 ) {
			
				$this->db->where('CODEBAR', $item[2])->update( store_prefix() . 'nexo_articles', array(
					'QUANTITE_RESTANTE'        	=>    intval($item[5]) - intval($item[1]),
					'QUANTITE_VENDU'        	=>    intval($item[4]) + intval($item[1])
				));
							
			}
			
			// Adding to order product
			$item_data		=	array(
				'REF_PRODUCT_CODEBAR'  =>    $item[2],
				'REF_COMMAND_CODE'     =>    $order_details[ 'CODE' ],
				'QUANTITE'             =>    $item[1],
				'PRIX'                 =>    $item[3],
				'PRIX_TOTAL'           =>    __floatval($item[1]) * __floatval($item[3])
			);
						
			$this->db->insert( store_prefix() . 'nexo_commandes_produits', $item_data );
        }
        
        $this->db->insert( store_prefix() . 'nexo_commandes', $order_details);
        
        $current_order    =    $this->db->where('CODE', $order_details[ 'CODE' ])
                            ->get( store_prefix() . 'nexo_commandes')
                            ->result_array();
							
		// @since 2.8.2
		/**
		 * Save order meta
		**/
		
		$metas					=	json_decode( $this->post( 'METAS' ) );
		
		if( $metas ) {
		
			foreach( $metas as $key => $value ) {
				$meta_data		=	array(
					'REF_ORDER_ID'	=>	$current_order[0][ 'ID' ],
					'KEY'			=>	$key,
					'VALUE'			=>	$value,
					'AUTHOR'		=>	$author_id,
					'DATE_CREATION'	=>	$this->post('DATE_CREATION')
				);
				
				$this->db->insert( store_prefix() . 'nexo_commandes_meta', $meta_data );
			}
		
		}
		
		// @since 2.9 
		// Save order payment
		$this->load->config( 'rest' );
		$Curl			=	new Curl;
        //$header_key		=	$this->config->item( 'rest_header_key' );
		//$header_value	=	$_SERVER[ 'HTTP_' .$this->config->item( 'rest_header_key' ) ];
		// $Curl->setHeader( $header_key, $header_value );
		$Curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);
		
		$Curl->post( site_url( array( 'rest', 'nexo', 'order_payment', store_get_param( '?' ) ) ), array(
			'author'		=>	$author_id,
			'date'			=>	$this->post('DATE_CREATION'),
			'payment_type'	=>	$this->post('PAYMENT_TYPE'),
			'amount'		=>	$this->post( 'SOMME_PERCU' ),
			'order_code'	=>	$current_order[0][ 'CODE' ]
		) );
		
        $this->response(array(
            'order_id'        =>    $current_order[0][ 'ID' ],
            'order_type'    =>    $order_details[ 'TYPE' ],
            'order_code'    =>    $current_order[0][ 'CODE' ]
        ), 200);
    }
    
    /**
     * Update Order
     * @params int Author id
     * @params int order id
     * @return json
    **/
    
    public function order_put($author_id, $order_id)
    {
        $this->load->model('Nexo_Checkout');
        $this->load->model('Options');
        // Get old order details with his items	
        $old_order                =    $this->Nexo_Checkout->get_order_products($order_id, true);
        
        $current_order    =    $this->db->where('ID', $order_id)
                            ->get( store_prefix() . 'nexo_commandes')
                            ->result_array();

        // Only incomplete order can be edited
        if ( ! in_array( $current_order[0][ 'TYPE' ], $this->put( 'EDITABLE_ORDERS' ) ) ) {
            $this->__failed();
        }
        
        $order_details            =    array();
        
        $order_details            =    array(
            'RISTOURNE'            =>    $this->put('RISTOURNE'),
            'REMISE'            =>    $this->put('REMISE'),
            'RABAIS'            =>    $this->put('RABAIS'),
            'GROUP_DISCOUNT'    =>    $this->put('GROUP_DISCOUNT'),
            'TOTAL'                =>    $this->put('TOTAL'),
            'AUTHOR'            =>    $author_id,
            'PAYMENT_TYPE'        =>    $this->put('PAYMENT_TYPE'),
            'REF_CLIENT'        =>    $this->put('REF_CLIENT'),
            'TVA'                =>    $this->put('TVA'),
            'SOMME_PERCU'        =>    $this->put('SOMME_PERCU'),
            //'CODE'			=>	$this->Nexo_Checkout->shuffle_code(),
            'DATE_MOD'            =>    $this->put('DATE_CREATION'),
			'DESCRIPTION'			=>	$this->put( 'DESCRIPTION' ),
			'REF_REGISTER'		=>	$this->put( 'REGISTER_ID' ),
			'TITRE'				=>	$this->put( 'TITRE' ) != null ? $this->put( 'TITRE' ) : ''
        );
		        
        // Order Type
		// @since 2.7.1 if a custom type is submited this type replace default order type
		if( ! $this->put( 'TYPE' ) ) {
			if (__floatval($this->put('SOMME_PERCU')) >= __floatval($this->put('TOTAL'))) {
				$order_details[ 'TYPE' ]    =    'nexo_order_comptant'; // Comptant
			} elseif (__floatval($this->put('SOMME_PERCU')) == 0) {
				$order_details[ 'TYPE' ]    =   'nexo_order_devis'; // Devis
			} elseif (__floatval($this->put('SOMME_PERCU')) < __floatval($this->put('TOTAL')) && __floatval($this->put('SOMME_PERCU')) > 0) {
				$order_details[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
			}
		} else {
			$order_details[ 'TYPE' ]		=	$this->put( 'TYPE' );
		}
                
        // If customer has changed
        if ($this->put('REF_CLIENT') != $old_order['order'][0][ 'REF_CLIENT' ]) {
            
            // Increase customers purchases
            $query                        =    $this->db->where('ID', $this->put('REF_CLIENT'))->get( store_prefix() . 'nexo_clients');
            $client                        =    $query->result_array();
            
            $total_commands         =    intval($client[0][ 'NBR_COMMANDES' ]) + 1;
            $overal_commands           =    intval($client[0][ 'OVERALL_COMMANDES' ]) + 1;
            
            $this->db->set('NBR_COMMANDES', $total_commands);
            $this->db->set('OVERALL_COMMANDES', $overal_commands);
            
            // Disable automatic discount
            if ($this->put('REF_CLIENT') != $this->put('DEFAULT_CUSTOMER')) {
            
                // Verifie si le client doit profiter de la réduction
                if ($this->put('DISCOUNT_TYPE') != 'disable') {
                    // On définie si en fonction des réglages, l'on peut accorder une réduction au client
                    if ($total_commands >= __floatval($this->put('HMB_DISCOUNT')) - 1 && $client[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                        $this->db->set('DISCOUNT_ACTIVE', 1);
                    } elseif ($total_commands >= $this->put('HMB_DISCOUNT') && $client[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                        $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                        $this->db->set('NBR_COMMANDES', 0); // le nombre de commande est également désactivé
                    }
                }
            }
            
            $this->db->where('ID', $this->put('REF_CLIENT'))
            ->update( store_prefix() . 'nexo_clients');
            
            // Reduce for the previous customer
            $query                    =    $this->db->where('ID',  $old_order['order'][0][ 'REF_CLIENT' ])->get( store_prefix() . 'nexo_clients');
            $old_customer             =    $query->result_array();
            
            // Le nombre de commande ne peut pas être inférieur à 0;
            $this->db
            ->set('NBR_COMMANDES',  intval($old_customer[0][ 'NBR_COMMANDES' ]) == 0 ? 0 : intval($old_customer[0][ 'NBR_COMMANDES' ]) - 1)
            ->set('OVERALL_COMMANDES',  intval($old_customer[0][ 'OVERALL_COMMANDES' ]) == 0 ? 0 : intval($old_customer[0][ 'OVERALL_COMMANDES' ]) - 1)
            ->where('ID', $old_order['order'][0][ 'REF_CLIENT' ])
            ->update( store_prefix() . 'nexo_clients');
        }
        
        // Restore Bought items
        foreach ($old_order[ 'products' ] as $product) {
            $this->db
            ->set('QUANTITE_RESTANTE', '`QUANTITE_RESTANTE` + ' . intval($product[ 'QUANTITE' ]), false)
            ->set('QUANTITE_VENDU', '`QUANTITE_VENDU` - ' . intval($product[ 'QUANTITE' ]), false)
            ->where('CODEBAR', $product[ 'REF_PRODUCT_CODEBAR' ])
            ->update( store_prefix() . 'nexo_articles');
        }
        
        // Delete item from order
        $this->db->where('REF_COMMAND_CODE', $old_order[ 'order' ][0][ 'CODE' ])->delete( store_prefix() . 'nexo_commandes_produits');
        
        // Save Order items		
        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE, STOCK_ENABLED );
        **/
        
        foreach ($this->put('ITEMS') as $item) {
            
            // Get Items 
            $fresh_items    =    $this->db->where('CODEBAR', $item[2])->get( store_prefix() . 'nexo_articles')->result_array();
			
			/**
			 * If Stock Enabled is active
			**/
			
			if( intval( $item[6] ) == 1 ) {
			
				$this->db->where('CODEBAR', $item[2])->update( store_prefix() . 'nexo_articles', array(
					'QUANTITE_RESTANTE'        =>    intval($fresh_items[0][ 'QUANTITE_RESTANTE' ]) - intval($item[1]),
					'QUANTITE_VENDU'        =>    intval($fresh_items[0][ 'QUANTITE_VENDU' ]) + intval($item[1])
				));
							
			}
            
            // Adding to order product
			$item_data			=	array(
                'REF_PRODUCT_CODEBAR'        =>    $item[2],
                'REF_COMMAND_CODE'            =>    $old_order[ 'order' ][0][ 'CODE' ],
                'QUANTITE'                    =>    $item[1],
                'PRIX'                        =>    $item[3],
                'PRIX_TOTAL'                =>    intval($item[1]) * intval($item[3])
            );
			
			
            $this->db->insert( store_prefix() . 'nexo_commandes_produits', $item_data );
        }
        
        $this->db->where('ID', $order_id)->update( store_prefix() . 'nexo_commandes', $order_details);
		
		// @since 2.8.2
		/**
		 * Save order meta
		**/
		
		// Delete first all meta
		$this->db->where( 'REF_ORDER_ID', $order_id )->delete( store_prefix() . 'nexo_commandes_meta' );
		
		$metas					=	json_decode( $this->post( 'METAS' ) );
		
		if( $metas ) {
		
			foreach( $metas as $key => $value ) {
				
				$meta_data			=	array(
					'REF_ORDER_ID'	=>	$order_id,
					'KEY'			=>	$key,
					'VALUE'			=>	$value,
					'AUTHOR'		=>	$author_id,
					'DATE_CREATION'	=>	$this->post('DATE_CREATION')
				);
								
				$this->db->insert( store_prefix() . 'nexo_commandes_meta', $meta_data );
			}
			
		}
		
		// @since 2.9 
		// Save order payment
		$this->load->config( 'rest' );
		$Curl			=	new Curl;
        // $header_key		=	$this->config->item( 'rest_key_name' );
		// $header_value	=	$_SERVER[ 'HTTP_' . $this->config->item( 'rest_key_name' ) ];
		$Curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);
		
		$Curl->post( site_url( array( 'rest', 'nexo', 'order_payment', store_get_param( '?' ) ) ), array(
			'author'	=>	$author_id,
			'date'		=>	$this->put('DATE_CREATION'),
			'payment_type'	=>	$this->put('PAYMENT_TYPE'),
			'amount'	=>	$this->put( 'SOMME_PERCU' ),
			'order_code'	=>	$current_order[0][ 'CODE' ]
		) );
        
        $this->response(array(
            'order_id'        =>    $order_id,
            'order_type'    =>    $order_details[ 'TYPE' ],
            'order_code'    =>    $current_order[0][ 'CODE' ]
        ), 200);
    }
    
    /**
     * Get order using dates
     * 
	 * @params string order type
	 * @params int register id
     * @return json
    **/
    
    public function order_by_dates_post($order_type = 'all', $register = null )
    {
		// @since 2.7.5
		if( $register != null ) {
			$this->db->where('REF_REGISTER', $register );	
		}
		
        $this->db->where('DATE_CREATION >=', $this->post('start'));
        $this->db->where('DATE_CREATION <=', $this->post('end'));
        
        if ($order_type != 'all') {
            $this->db->where('TYPE', $order_type);
        }
        
        $query    =    $this->db->get( store_prefix() . 'nexo_commandes' );
        $this->response($query->result(), 200);
    }
	
	/**
	 * Get Order with his item
	 * @params int order id
	 * @return json
	**/
	
	public function order_with_item_get( $order_id )
	{		
		$order		=	$this->db->select( '*,
		' . store_prefix() .'nexo_commandes.ID as ID,
		' . store_prefix() .'nexo_clients.NOM as CLIENT_NAME,
		aauth_users.name as AUTHOR_NAME,
		' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
		' )
		
		->from( store_prefix() . 'nexo_commandes' )
		
		->join( 
			store_prefix() . 'nexo_clients', 
			store_prefix() . 'nexo_clients.ID = ' . store_prefix() . 'nexo_commandes.REF_CLIENT', 
			'left' 
		)
		
		->join( 
			'aauth_users', 
			store_prefix() . 'nexo_commandes.AUTHOR = aauth_users.id', 
			'left' 
		)
		
		->where( store_prefix() . 'nexo_commandes.ID', $order_id )
		
		->get()->result();
		
		$items		=	$this->db->select( '*' )
		
		->from( store_prefix() . 'nexo_commandes_produits' )
		
		->join( 
			store_prefix() . 'nexo_articles', 
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 
			'left' 
		)
		
		->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order[0]->CODE )
		
		->get()->result();
		
		if( $order && $items ) {
			$this->response( array(
				'order'		=>	$order[0],
				'items'		=>	$items
			), 200 );
		}
		
		$this->__empty();
	}
	
	/**
	 * Order Products
	 * @params string order code
	 * @return json
	**/
	
	public function order_items_dual_item_post( )
	{
		if( is_array( $this->post( 'orders_code' ) ) ) {
			
			foreach( $this->post( 'orders_code' ) as $code ) {
				$this->db->or_where( 'REF_COMMAND_CODE', $code );
			}
			
			$data[ 'order_items' ]	=	$this->db->get( store_prefix() . 'nexo_commandes_produits' )->result();
			
			$data[ 'items' ]		=	$this->db->select( '*' )
			->from( store_prefix() . 'nexo_commandes_produits' )
			->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner' )
			->get()->result();		
			
			$this->response( $data, 200 );
		}
		$this->__empty();
	}
	
	/**
	 * Proceed Payment
	 * @params int order id
	 * @return json
	**/
	
	public function order_payment_post( $order_id ) 
	{
		$order	=	$this->db->where( 'ID', $order_id )->get( store_prefix() . 'nexo_commandes' )->result();
		
		if( $order[0]->TYPE != 'nexo_commande_comptant' ) {
			
			if( floatval( $order[0]->TOTAL ) <= ( floatval( $order[0]->SOMME_PERCU ) + floatval( $this->post( 'amount' ) ) ) ) {
				$this->db->where( 'ID', $order_id )->update( store_prefix() . 'nexo_commandes', array(
					'AUTHOR'				=>	$this->post( 'author' ),
					'DATE_MOD'				=>	$this->post( 'date' ),
					'TYPE'					=>	'nexo_order_comptant',
					'SOMME_PERCU'			=>	floatval( $order[0]->SOMME_PERCU ) + floatval( $this->post( 'amount' ) ),
					'PAYMENT_TYPE'			=>	$this->post( 'payment_type' )
				) );
			} else {
				$this->db->where( 'ID', $order_id )->update( store_prefix() . 'nexo_commandes', array(
					'AUTHOR'				=>	$this->post( 'author' ),
					'DATE_MOD'				=>	$this->post( 'date' ),
					'TYPE'					=>	'nexo_order_advance',
					'SOMME_PERCU'			=>	floatval( $order[0]->SOMME_PERCU ) + floatval( $this->post( 'amount' ) ),
					'PAYMENT_TYPE'			=>	$this->post( 'payment_type' )
				) );
			}
			
			$this->db->insert( store_prefix() . 'nexo_commandes_paiements', array(
				'REF_COMMAND_CODE'		=>	$this->post( 'order_code' ),
				'AUTHOR'				=>	$this->post( 'author' ),
				'DATE_CREATION'			=>	$this->post( 'date' ),
				'PAYMENT_TYPE'			=>	$this->post( 'payment_type' ),
				'MONTANT'				=>	$this->post( 'amount' )
			) ); 
			
			$this->__success();
			
		} else {
			$this->__forbidden();
		}
	}
	
	/**
	 * Get Order Payments
	 * @param int order id
	 * @return json
	**/
	
	public function order_payment_get( $order_code )
	{
		$this->response( 
			$this->db
			->select( '*,aauth_users.name as AUTHOR_NAME' )
			->join( 'aauth_users', 'aauth_users.id = ' . store_prefix() . 'nexo_commandes_paiements.AUTHOR', 'right' )
			->from( store_prefix() . 'nexo_commandes_paiements' )
			->where( 'REF_COMMAND_CODE', $order_code )
			->get()->result(),
			200 
		);
	}


}


