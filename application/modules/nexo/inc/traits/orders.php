<?php

use Carbon\Carbon;
use Curl\Curl;

trait Nexo_orders
{
    /**
     * Get Order
     * @param string/int
     * @param string
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
     * @param int Author id
     * @return json
    **/

    public function order_post($author_id)
    {
        $this->load->model('Nexo_Checkout');

        $order_details              =    array();

        $shipping                   =   ( array ) $this->post( 'shipping' );

        $order_details              =    array(
            'RISTOURNE'             =>      $this->post('RISTOURNE'),
            'REMISE'                =>      $this->post('REMISE'),
            // @since 2.9.6
            'REMISE_PERCENT'        =>      $this->post( 'REMISE_PERCENT' ),
            'REMISE_TYPE'           =>      $this->post( 'REMISE_TYPE' ),

            'RABAIS'                =>      $this->post('RABAIS'),
            'GROUP_DISCOUNT'        =>      $this->post('GROUP_DISCOUNT'),
            'TOTAL'                 =>      $this->post('TOTAL'),
            'AUTHOR'                =>      $author_id,
            'PAYMENT_TYPE'          =>      $this->post('PAYMENT_TYPE'),
            'REF_CLIENT'            =>      $this->post('REF_CLIENT'),
            'TVA'                   =>      $this->post('TVA'),
            // 'SOMME_PERCU'           =>      $this->post('SOMME_PERCU'),
            'CODE'                  =>      $this->Nexo_Checkout->shuffle_code(),
            'DATE_CREATION'         =>      date_now(),
            'DATE_MOD'              =>      date_now(), // @since 3.8.0
			'DESCRIPTION'			=>      $this->post( 'DESCRIPTION' ),
			'REF_REGISTER'		    =>      $this->post( 'REGISTER_ID' ),
			// @since 2.7.10
			'TITRE'                 =>      $this->post( 'TITRE' ) != null ? $this->post( 'TITRE' ) : '',
            // @since 3.1
            'SHIPPING_AMOUNT'       =>      floatval( @$shipping[ 'price' ] ),
            'REF_SHIPPING_ADDRESS'  =>      @$shipping[ 'id' ]
        );

        // filter order details
        $order_details          =   $this->events->apply_filters( 'post_order_details', $order_details );

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

        // only if customer has been found
        if( $result ) {
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
        } else {
            return $this->response(array(
                'message'   =>  __( 'Impossible d\'identifier le client', 'nexo' ),
                'status'    =>  'failed'
            ), 403 );
        }
        

        // Save Order items

        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE, STOCK_ENABLED );
        **/

        foreach ( $this->post( 'ITEMS' ) as $item) {

            $fresh_item       =   $this->db->where( 'CODEBAR', $item[ 'codebar' ] )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

			/**
             * If Stock Enabled is active
             * stock enable is not checked for inline items
            **/
            
			if( intval( $item[ 'stock_enabled' ] ) == '1' && $item[ 'inline' ] != '1' ) {

                $data                       =   [];
                $data[ 'QUANTITE_VENDU' ]   =   intval( $fresh_item[0][ 'QUANTITE_VENDU' ]) + intval($item[ 'qte_added' ]);

                // if item type belongs to type which allow to decrease remaning quantity
                if( in_array( intval( $fresh_item[0][ 'TYPE' ] ), $this->events->apply_filters( 'treat_as_sold_quantity', [ 1 ] ) ) ) {
                    $data[ 'QUANTITE_RESTANTE' ]   =   intval($fresh_item[0][ 'QUANTITE_RESTANTE' ]) - intval($item[ 'qte_added' ]);
                }

                // update item stock
                $this->db->where('CODEBAR', $item[ 'codebar' ])
                ->update( store_prefix() . 'nexo_articles', $data );
			}

			// Adding to order product
			if( $item[ 'discount_type' ] == 'percentage' && $item[ 'discount_percent' ] != '0' ) {
				$discount_amount		=	__floatval( ( __floatval($item[ 'qte_added' ]) * __floatval($item['sale_price']) ) * floatval( $item[ 'discount_percent' ] ) / 100 );
			} elseif( $item[ 'discount_type' ] == 'flat' ) {
				$discount_amount		=	__floatval( $item[ 'discount_amount' ] );
			} else {
				$discount_amount		=	0;
			}

			$item_data		=	array(
				'REF_PRODUCT_CODEBAR'  =>    $item[ 'codebar' ],
				'REF_COMMAND_CODE'     =>    $order_details[ 'CODE' ],
				'QUANTITE'             =>    $item[ 'qte_added' ],
				'PRIX'                 =>    floatval( $item[ 'sale_price' ] ) - $discount_amount,
				'PRIX_TOTAL'           =>    ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
				// @since 2.9.0
				'DISCOUNT_TYPE'			=>	$item['discount_type'],
				'DISCOUNT_AMOUNT'		=>	$item['discount_amount'],
				'DISCOUNT_PERCENT'		=>	$item['discount_percent'],
                // @since 3.1
                'NAME'                      =>    $item[ 'name' ],
                'INLINE'                    =>    $item[ 'inline' ]
            );
            
            $item_data                      =     $this->events->apply_filters_ref_array( 'post_order_item', [$item_data, $item] );

			$this->db->insert( store_prefix() . 'nexo_commandes_produits', $item_data );

            // getcommande product id
            $insert_id = $this->db->insert_id();

            // Saving item metas
            $meta_array         =   array();
            foreach( ( array ) @$item[ 'metas' ] as $key => $value ) {
                $meta_array[]     =   [
                    'REF_COMMAND_PRODUCT'   =>  $insert_id,
                    'REF_COMMAND_CODE'      =>  $order_details[ 'CODE' ],
                    'KEY'                   =>  $key,
                    'VALUE'                 =>  is_array( $value ) ? json_encode( $value ) : $value,
                    'DATE_CREATION'         =>  date_now()
                ];
            }

            // If item has metas, we just save it
            if( $meta_array ) {
                $this->db->insert_batch( store_prefix() . 'nexo_commandes_produits_meta', $meta_array );
            }

            // Add history for this item on stock flow
            $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                'REF_ARTICLE_BARCODE'       =>  $item[ 'codebar' ],
                'QUANTITE'                  =>  $item[ 'qte_added' ],
                'UNIT_PRICE'                =>  $item[ 'sale_price' ],
                'TOTAL_PRICE'               =>  ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
                'REF_COMMAND_CODE'          =>  $order_details[ 'CODE' ],
                'AUTHOR'                    =>  User::id(),
                'DATE_CREATION'             =>  date_now(),
                'TYPE'                      =>  'sale'
            ]);

            // update current remaining quantity

        }

        $this->db->insert( store_prefix() . 'nexo_commandes', $order_details);

        $current_order    =    $this->db->where('CODE', $order_details[ 'CODE' ])
        ->get( store_prefix() . 'nexo_commandes')
        ->result_array();

		// @since 2.8.2
		/**
		 * Save order meta
		**/

		$metas					=	$this->post( 'metas' );

		if( $metas ) {

			foreach( $metas as $key => $value ) {
				$meta_data		=	array(
					'REF_ORDER_ID'	=>	$current_order[0][ 'ID' ],
					'KEY'			=>	$key,
					'VALUE'			=>	is_array( $value ) ? json_encode( $value ) : $value,
					'AUTHOR'		=>	$author_id,
					'DATE_CREATION'	=>	date_now()
				);

				$this->db->insert( store_prefix() . 'nexo_commandes_meta', $meta_data );
			}

		}

		// @since 2.9
		// Save order payment
		$this->load->config( 'rest' );

		if( is_array( $this->post( 'payments' ) ) ) {
			foreach( $this->post( 'payments' ) as $payment ) {

				$request    =   Requests::post( site_url( array( 'rest', 'nexo', 'order_payment', $current_order[0][ 'ID' ], store_get_param( '?' ) ) ), [
                    $this->config->item('rest_key_name')    =>  $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]
                ], array(
					'author'		=>	$author_id,
					'date'			=>	date_now(),
					'payment_type'	=>	$payment[ 'namespace' ],
					'amount'		=>	$payment[ 'amount' ],
					'order_code'	=>	$current_order[0][ 'CODE' ]
                ) );

                // @since 3.1
                // if the payment is a coupon, then we'll increase his usage
                if( $payment[ 'namespace' ] == 'coupon' ) {
                    
                    $coupon         =   $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->get( store_prefix() . 'nexo_coupons' )
                    ->result_array();

                    $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->update( store_prefix() . 'nexo_coupons', [
                        'USAGE_COUNT'   =>  intval( $coupon[0][ 'USAGE_COUNT' ] ) + 1
                    ]);
                }
			}
		}

        /**
         * Add shipping informations
         * @since 3.1
        **/

        if( $this->post( 'shipping' ) && ! empty( $shipping[ 'title' ] ) ) {
            // fetch ref shipping for the selected customers
            $shipping       =   $this->post( 'shipping' );
            // edit shipping id to ref_shipping
            $shipping[ 'ref_shipping' ]     =   $shipping[ 'id' ];
            $shipping[ 'ref_order' ]        =   $current_order[0][ 'ID' ];
            unset( $shipping[ 'id' ] );

            $this->db->insert( store_prefix() . 'nexo_commandes_shippings', $shipping );
        }

        $this->response(array(
            'order_id'          =>    $current_order[0][ 'ID' ],
            'order_type'        =>    $order_details[ 'TYPE' ],
            'order_code'        =>    $current_order[0][ 'CODE' ]
        ), 200);
    }

    /**
     * Update Order
     * @param int Author id
     * @param int order id
     * @return json
    **/

    public function order_put($author_id, $order_id)
    {
        $this->load->model('Nexo_Checkout');
        $this->load->model('Options');
        // Get old order details with his items
        $old_order              =    $this->Nexo_Checkout->get_order_products($order_id, true);
        $current_order          =    $this->db->where('ID', $order_id)
        ->get( store_prefix() . 'nexo_commandes')
        ->result_array();

        // Only incomplete order can be edited
        if ( ! in_array( $current_order[0][ 'TYPE' ], $this->events->apply_filters( 'order_editable', [ 'nexo_order_devis' ] ) ) ) { // $this->put( 'EDITABLE_ORDERS' )
            $this->__failed();
        }

        $shipping                   =   ( array ) $this->put( 'shipping' );

        $order_details            =    array(
            'RISTOURNE'         =>    $this->put('RISTOURNE'),
            'REMISE'            =>    $this->put('REMISE'),
            // @since 2.9.6
            'REMISE_PERCENT'    =>    $this->put( 'REMISE_PERCENT' ),
            'REMISE_TYPE'       =>    $this->put( 'REMISE_TYPE' ),
            // @endSince
            'RABAIS'            =>    $this->put('RABAIS'),
            'GROUP_DISCOUNT'    =>    $this->put('GROUP_DISCOUNT'),
            'TOTAL'             =>    $this->put('TOTAL'),
            'AUTHOR'            =>    $author_id,
            'PAYMENT_TYPE'      =>    $this->put('PAYMENT_TYPE'),
            'REF_CLIENT'        =>    $this->put('REF_CLIENT'),
            'TVA'               =>    $this->put('TVA'),
            // 'SOMME_PERCU'       =>    $this->put('SOMME_PERCU'),
            //'CODE'			=>	$this->Nexo_Checkout->shuffle_code(),
            'DATE_MOD'          =>    date_now(),
            'DESCRIPTION'       =>	  $this->put( 'DESCRIPTION' ),
            'REF_REGISTER'      =>	  $this->put( 'REGISTER_ID' ),
            // @since 3.1
            'SHIPPING_AMOUNT'       =>      floatval( @$shipping[ 'price' ] ),
            'REF_SHIPPING_ADDRESS'  =>      @$shipping[ 'id' ],
            // @since 3.1.0
            'TITRE'             =>  $this->put( 'TITRE' )
        );

        // filter order details
        $order_details          =   $this->events->apply_filters( 'put_order_details', $order_details );

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
            $query                  =    $this->db->where('ID', $this->put('REF_CLIENT'))->get( store_prefix() . 'nexo_clients');
            $client                 =    $query->result_array();

            $total_commands         =    intval($client[0][ 'NBR_COMMANDES' ]) + 1;
            $overal_commands        =    intval($client[0][ 'OVERALL_COMMANDES' ]) + 1;

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
        // only if the stock management is enabled
        foreach ( $old_order[ 'products' ] as $product ) {
            if( intval( $product[ 'STOCK_ENABLED' ] ) == 1 ) {
                // to avoid saving negative values
                // pull fresh value a make a comparison
                // @since 3.7.5
                $fresh_product      =   $this->db->where( 'ID', $product[ 'ID' ] )->get( store_prefix() . 'nexo_articles' )
                ->result_array();

                if( intval( $fresh_product[0][ 'QUANTITE_RESTANTE' ] ) - intval($product[ 'QUANTITE' ]) > 0 ) {
                    $this->db
                    ->set('QUANTITE_RESTANTE', '`QUANTITE_RESTANTE` + ' . intval($product[ 'QUANTITE' ]), false)
                    ->set('QUANTITE_VENDU', '`QUANTITE_VENDU` - ' . intval($product[ 'QUANTITE' ]), false)
                    ->where('CODEBAR', $product[ 'REF_PRODUCT_CODEBAR' ])
                    ->update( store_prefix() . 'nexo_articles');        
                }
            }
        }

        // Delete item from order
        $this->db->where( 'REF_COMMAND_CODE', $old_order[ 'order' ][0][ 'CODE' ] )->delete( store_prefix() . 'nexo_commandes_produits');

        // Delete item metas
        $this->db->where( 'REF_COMMAND_CODE', $old_order[ 'order' ][0][ 'CODE' ] )->delete( store_prefix() . 'nexo_commandes_produits_meta');

        // Save Order items
        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE, STOCK_ENABLED );
        **/

        foreach ( $this->put('ITEMS') as $item ) {

            // Get Items
            $fresh_items    =    $this->db->where('CODEBAR', $item[ 'codebar' ])
            ->get( store_prefix() . 'nexo_articles')
            ->result_array();

			/**
			 * If Stock Enabled is active
			**/

			if( intval( $item['stock_enabled'] ) == 1 && $item[ 'inline' ] != '1' ) {
				$this->db->where('CODEBAR', $item['codebar'])->update( store_prefix() . 'nexo_articles', array(
					'QUANTITE_RESTANTE'        =>    intval($fresh_items[0][ 'QUANTITE_RESTANTE' ]) - intval($item['qte_added']),
					'QUANTITE_VENDU'        =>    intval($fresh_items[0][ 'QUANTITE_VENDU' ]) + intval($item['qte_added'])
				) );
			}

			// Adding to order product
			if( $item[ 'discount_type' ] == 'percentage' && $item[ 'discount_percent' ] != '0' ) {
				$discount_amount		=	__floatval( ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) * floatval( $item[ 'discount_percent' ] ) / 100 );
			} elseif( $item[ 'discount_type' ] == 'flat' ) {
				$discount_amount		=	__floatval( $item[ 'discount_amount' ] );
			} else {
				$discount_amount		=	0;
			}

            // Adding to order product
			$item_data			=	array(
                'REF_PRODUCT_CODEBAR'       =>    $item[ 'codebar' ],
                'REF_COMMAND_CODE'          =>    $old_order[ 'order' ][0][ 'CODE' ],
                'QUANTITE'                  =>    $item[ 'qte_added' ],
                'PRIX'                      =>    floatval( $item[ 'sale_price' ] ) - $discount_amount,
                'PRIX_TOTAL'                =>    ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
				// @since 2.9.0
				'DISCOUNT_TYPE'             =>    $item[ 'discount_type' ],
				'DISCOUNT_AMOUNT'           =>    $item[ 'discount_amount' ],
                'DISCOUNT_PERCENT'          =>    $item[ 'discount_percent' ],
                // @since 3.1
                'NAME'                      =>    $item[ 'name' ],
                'INLINE'                    =>    $item[ 'inline' ]
            );

            // filter item
            $item_data                      =     $this->events->apply_filters_ref_array( 'put_order_item', [$item_data, $item] );

            $this->db->insert( store_prefix() . 'nexo_commandes_produits', $item_data );

            $command_product_id             =   $this->db->insert_id();

            // Saving item metas
            foreach( ( array ) @$item[ 'metas' ] as $key => $value ) {
                $meta     =   [
                    'VALUE'                 =>  is_array( $value ) ? json_encode( $value ) : $value,
                    'DATE_MODIFICATION'     =>  date_now(),
                    'REF_COMMAND_CODE'      =>  $old_order[ 'order' ][0][ 'CODE' ],
                    'KEY'                   =>  $key,
                    'REF_COMMAND_PRODUCT'   =>  $command_product_id
                ];

                $this->db->where( 'REF_COMMAND_PRODUCT', $item[ 'id' ] )
                ->insert( store_prefix() . 'nexo_commandes_produits_meta', $meta );
            }

            // Add history for this item on stock flow
            $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                'REF_ARTICLE_BARCODE'       =>  $item[ 'codebar' ],
                'QUANTITE'                  =>  $item[ 'qte_added' ],
                'UNIT_PRICE'                =>  $item[ 'sale_price' ],
                'TOTAL_PRICE'               =>  ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
                'REF_COMMAND_CODE'          =>  $old_order[ 'order' ][0][ 'CODE' ],
                'AUTHOR'                    =>  User::id(),
                'DATE_CREATION'             =>  date_now(),
                'TYPE'                      =>  'sale'
            ]);
        }

        $this->db->where( 'ID', $order_id )
        ->update( store_prefix() . 'nexo_commandes', $order_details);

		// @since 2.8.2
		/**
		 * Save order meta
		**/

		// Delete first all meta
		/** $this->db->where( 'REF_ORDER_ID', $order_id )
        ->delete( store_prefix() . 'nexo_commandes_meta' ); **/

		$metas        =	      $this->put( 'metas' );

		if( $metas ) {

			foreach( $metas as $key => $value ) {

				$meta_data			=	array(
					'REF_ORDER_ID'	=>	$order_id,
					'KEY'			=>	$key,
					'VALUE'			=>	is_array( $value ) ? json_encode( $value ) : $value,
					'AUTHOR'		=>	$author_id,
					'DATE_CREATION'	=>	date_now()
				);

				$this->db->where( 'REF_ORDER_ID', $order_id )
                ->where( 'KEY', $key )
                ->update( store_prefix() . 'nexo_commandes_meta', $meta_data );
			}
		}

		// @since 2.9
		// Save order payment
		$this->load->config( 'rest' );
		$Curl			=	new Curl;
        // $header_key		=	$this->config->item( 'rest_key_name' );
		// $header_value	=	$_SERVER[ 'HTTP_' . $this->config->item( 'rest_key_name' ) ];
		// $Curl->setHeader($this->config->item('rest_key_name'), $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]);

		if( is_array( $this->put( 'payments' ) ) ) {
			foreach( $this->put( 'payments' ) as $payment ) {

				$request    =   Requests::post( site_url( array( 'rest', 'nexo', 'order_payment', $current_order[0][ 'ID' ], store_get_param( '?' ) ) ), [
                    $this->config->item('rest_key_name')    =>  $_SERVER[ 'HTTP_' . $this->config->item('rest_header_key') ]
                ], array(
					'author'		=>	$author_id,
					'date'			=>	date_now(),
					'payment_type'	=>	$payment[ 'namespace' ],
					'amount'		=>	$payment[ 'amount' ],
					'order_code'	=>	$current_order[0][ 'CODE' ]
                ) );

                // @since 3.1
                // if the payment is a coupon, then we'll increase his usage
                if( $payment[ 'namespace' ] == 'coupon' ) {
                    
                    $coupon         =   $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->get( store_prefix() . 'nexo_coupons' )
                    ->result_array();

                    $this->db->where( 'ID', $payment[ 'meta' ][ 'coupon_id' ] )
                    ->update( store_prefix() . 'nexo_coupons', [
                        'USAGE_COUNT'   =>  intval( $coupon[0][ 'USAGE_COUNT' ] ) + 1
                    ]);
                }
			}
		}

        /**
         * Add shipping informations
         * @since 3.1
        **/

        if( $this->put( 'shipping' ) && ! empty( $shipping[ 'title' ] ) ) {
            // fetch ref shipping for the selected customers
            $shipping       =   $this->put( 'shipping' );
            // edit shipping id to ref_shipping
            $shipping[ 'ref_shipping' ]     =   $shipping[ 'id' ];
            $shipping[ 'ref_order' ]        =   $order_id;
            unset( $shipping[ 'id' ] );

            $this->db->where( 'ref_order', $order_id )->update( store_prefix() . 'nexo_commandes_shippings', $shipping );
        }

        $this->response(array(
            'order_id'          =>    $order_id,
            'order_type'        =>    $order_details[ 'TYPE' ],
            'order_code'        =>    $current_order[0][ 'CODE' ]
        ), 200);
    }

    /**
     * Get order using dates
     *
	 * @param string order type
	 * @param int register id
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

        if ( $order_type != 'all') {
            $this->db->where('TYPE', $order_type);
        }

        $query    =    $this->db->get( store_prefix() . 'nexo_commandes' );
        $this->response($query->result(), 200);
    }

	/**
	 * Get Order with his item
	 * @param int order id
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

		$items		=	$this->db->select( '*, '
        . store_prefix() . 'nexo_commandes_produits.ID as ID' )

		->from( store_prefix() . 'nexo_commandes_produits' )

		->join(
			store_prefix() . 'nexo_articles',
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR',
			'left'
		)

		->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $order[0]->CODE )

		->get()->result();

        // load items meta
        foreach( $items as $key => $item ) {
            $metas      =   $this->db->where( store_prefix() . 'nexo_commandes_produits_meta.REF_COMMAND_PRODUCT', $item->ID )
            ->get( store_prefix() . 'nexo_commandes_produits_meta' )->result();

            if( $metas ) {
                $items[ $key ]->metas    =   [];
            }

            foreach( $metas as $meta ) {
                $items[ $key ]->metas[ $meta->KEY ]      =   $meta->VALUE;
            }
        }

        // load shippings
        /** 
         * get shippings linked to that order
         * @since 3.1
        **/

        foreach( ( array ) $order as &$_order ) {
            $shippings   =   $this->db->where( 'ref_order', $_order->ID )
            ->get( store_prefix() . 'nexo_commandes_shippings' )
            ->result_array();

            if( $shippings ) {
                $_order->shipping   =   $shippings[0];
            }
        }

		if( $order && $items ) {
			$this->response( array(
				'order'		=>	$order[0],
				'items'		=>	$items
			), 200 );
		}

		$this->__empty();
	}

    /**
    *
    * Get Order with item made during a time range
    *
    * @param  int order id
    * @return json object
    */

    public function order_with_item_post( $order_id = null )
    {
        $order		=	$this->db->select( '*,
        ' . store_prefix() .'nexo_commandes.ID as ID,
        ' . store_prefix() .'nexo_commandes.DATE_CREATION as DATE_CREATION,
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

        ->join(
            store_prefix() . 'nexo_commandes_produits',
            store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . store_prefix() . 'nexo_commandes.CODE',
            'left'
        )

        ->join(
            store_prefix() . 'nexo_articles',
            store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR',
            'left'
        );

        if( $order_id != null ) {
            $this->db->where( store_prefix() . 'nexo_commandes.ID', $order_id );
        }

        if( $this->post( 'start_date' ) && $this->post( 'end_date' ) ) {
            $start_date         =   Carbon::parse( $this->post( 'start_date' ) )->startOfDay()->toDateTimeString();
            $end_date           =   Carbon::parse( $this->post( 'end_date' ) )->endOfDay()->toDateTimeString();

            $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $start_date );
            $this->db->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $end_date );
        }

        $result     =   $this->db
        ->get()->result();

        if( $result ) {
            $this->response( $result, 200 );
        }

        $this->__empty();
    }

	/**
	 * Order With Status
	 * @param string order status
	 * @return json
	**/

	public function order_with_status_get( $status )
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

		->where( store_prefix() . 'nexo_commandes.TYPE', $status )

		->get()->result();

        // pending review
        // /** 
        //  * get shippings linked to that order
        //  * @since 3.1
        // **/

        // foreach( ( array ) $order as &$_order ) {
        //     $shippings   =   $this->db->where( 'ref_order', $_order->ID )
        //     ->get( store_prefix() . 'nexo_commandes_shippings' )
        //     ->result_array();

        //     if( $shippings ) {
        //         $_order->shipping   =   $shippings[0];
        //     }
        // }

		$this->response( $order, 200 );

		$this->__empty();
	}

    /**
     *  Order with all stock defective and usable
     *  @param int order id
     *  @return json
    **/

    public function order_with_stock_get( $order_code )
    {
        $this->db->select(
            'aauth_users.name as ORDER_CASHIER, ' .
            store_prefix() . 'nexo_articles.DESIGN, ' .
            store_prefix() . 'nexo_commandes.TOTAL as TOTAL,' .
            store_prefix() . 'nexo_commandes.ID as ID,' .
            store_prefix() . 'nexo_commandes.CODE as CODE , ' .            
            store_prefix() . 'nexo_commandes.PAYMENT_TYPE , ' .            
            store_prefix() . 'nexo_commandes.SOMME_PERCU , ' .       
            store_prefix() . 'nexo_commandes.TYPE , ' .            
            store_prefix() . 'nexo_commandes.PAYMENT_TYPE , ' .            
            store_prefix() . 'nexo_commandes.DESCRIPTION , ' .            
            store_prefix() . 'nexo_commandes.DATE_CREATION as DATE , ' .      
            store_prefix() . 'nexo_commandes.DATE_MOD as DATE_MOD , ' .            
            store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE, ' .
            store_prefix() . 'nexo_articles_stock_flow.UNIT_PRICE as PRIX, ' .
            store_prefix() . 'nexo_articles_stock_flow.QUANTITE as QUANTITE, ' .
            store_prefix() . 'nexo_articles_stock_flow.TYPE as TYPE'
        );

        $this->db->from( store_prefix() . 'nexo_commandes' );

        $this->db->join(
            store_prefix() . 'nexo_articles_stock_flow',
            store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE = ' .
            store_prefix() . 'nexo_commandes.CODE', 'left'
        );

        $this->db->join(
            'aauth_users',
            'aauth_users.id = ' .
            store_prefix() . 'nexo_commandes.AUTHOR', 'left'
        );

        $this->db->join(
            store_prefix() . 'nexo_articles',
            store_prefix() . 'nexo_articles.CODEBAR = ' .
            store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE', 'left'
        );

        $this->db->where( store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE', $order_code );
        $this->db->where_in( store_prefix() . 'nexo_articles_stock_flow.TYPE', [ 'defective', 'usable' ]);
        // $this->db->group_by( store_prefix() . 'nexo_articles_stock_flow.REF_ARTICLE_BARCODE' );

        // $this->db->or_where(
        //     store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE',
        //     $order_code
        // );

        $order      =   $this->db->get()->result();

        // pending
        // /** 
        //  * get shippings linked to that order
        //  * @since 3.1
        // **/

        // foreach( ( array ) $order as &$_order ) {
        //     $shippings   =   $this->db->where( 'ID', $_order->ID )
        //     ->get( store_prefix() . 'nexo_commandes_shippings' )
        //     ->result();

        //     $_order[ 'shipping' ]   =   $shippings[0];
        // }

        return $this->response(
            $order,
            200
        );
    }

	/**
	 * Order Products
	 * @param string order code
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
	 * @param int order id
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
				'DATE_CREATION'			=>	date_now(),
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

	/**
	 * Get order item with their defective stock
	 * @param order code
	**/

	public function order_items_defectives_get( $order_code )
	{
		$this->db->select( '*' )
		->from( store_prefix() . 'nexo_articles' )
		->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' . store_prefix() . 'nexo_articles.CODEBAR', 'left' )
		->join( store_prefix() . 'nexo_commandes', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner' )
		->where( store_prefix() . 'nexo_commandes.CODE', $order_code );
		// ->where( store_prefix() . 'nexo_articles_defectueux.REF_COMMAND_CODE', $order_code );

		$query	=	$this->db->get();

		$this->response( $query->result(), 200 );
	}

	/**
	 * Refund Order
	**/

	public function order_refund_post( $order_code )
	{
		$toRefund				=	0;

		foreach( $this->post( 'items' ) as $item ) {

			$this->db->where( 'REF_PRODUCT_CODEBAR', $item[ 'CODEBAR' ] )
            ->update( store_prefix() . 'nexo_commandes_produits', array(
				'QUANTITE'	=>	$item[ 'QUANTITE' ],
            ) ); // Correcte
            
            $freshItem           =   $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

            $total                          =   0;

			// If a defective stock exists
			if( $item[ 'CURRENT_DEFECTIVE_QTE' ] > 0 ) {

                // Whether the item is a digital item, it need to be refundable

                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', array(
                    'REF_ARTICLE_BARCODE'	=>	$item[ 'REF_PRODUCT_CODEBAR' ],
                    'QUANTITE'				=>	$item[ 'CURRENT_DEFECTIVE_QTE' ],
                    'UNIT_PRICE'            =>  $item[ 'PRIX'],
                    'TOTAL_PRICE'           =>  floatval( $item[ 'PRIX'] ) * floatval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ),
                    'AUTHOR'				=>	$this->post( 'author' ),
                    'DATE_CREATION'			=>	date_now(),
                    'REF_COMMAND_CODE'		=>	$order_code,
                    'TYPE'                  =>  'defective'
                ) );

                // quantity stock is active when stock management is enabled
                if( intval( $freshItem[0][ 'STOCK_ENABLED' ] ) == 1 ) {
                    
                    $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )
                    // a defective item can't be considered as sold item
                    ->set( 'QUANTITE_VENDU', 'QUANTITE_VENDU - ' . intval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ), false )
                    // Increase defective item in stock
                    ->set( 'DEFECTUEUX', 'DEFECTUEUX+' . intval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ), false )
                    ->update( store_prefix() . 'nexo_articles' );
                }				

                $total					+=	floatval( $item[ 'PRIX' ] ) * floatval( $item[ 'CURRENT_DEFECTIVE_QTE' ] );
			}

            if( $item[ 'CURRENT_USABLE_QTE' ] > 0 ) {

                // Whether the item is a digital item, it need to be refundable

                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', array(
                    'REF_ARTICLE_BARCODE'	=>	$item[ 'REF_PRODUCT_CODEBAR' ],
                    'QUANTITE'				=>	$item[ 'CURRENT_USABLE_QTE' ],
                    'UNIT_PRICE'            =>  $item[ 'PRIX'],
                    'TOTAL_PRICE'           =>  floatval( $item[ 'PRIX'] ) * floatval( $item[ 'CURRENT_USABLE_QTE' ] ),
                    'AUTHOR'				=>	$this->post( 'author' ),
                    'DATE_CREATION'			=>	date_now(),
                    'REF_COMMAND_CODE'		=>	$order_code,
                    'TYPE'                  =>  'usable'
                ) );

                // quantity stock is active when stock management is enabled
                if( intval( $freshItem[0][ 'STOCK_ENABLED' ] ) == 1 ) {

                    // Increase defective item in stock
                    $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )
                    ->set( 'QUANTITE_RESTANTE', 'QUANTITE_RESTANTE+' . intval( $item[ 'CURRENT_USABLE_QTE' ] ), false )
                    ->set( 'QUANTITE_VENDU', 'QUANTITE_VENDU-' . intval( $item[ 'CURRENT_USABLE_QTE' ] ), false )
                    ->update( store_prefix() . 'nexo_articles' );
                }

                $total					+=	floatval( $item[ 'PRIX' ] ) * floatval( $item[ 'CURRENT_USABLE_QTE' ] );
            }

			// get discount
			if( $item[ 'DISCOUNT_TYPE' ] == 'percent' ) {
				$percentage			=	( floatval( $item[ 'DISCOUNT_PERCENT' ] ) * $total ) / 100;
			} else {
				$percentage			=	floatval( $item[ 'DISCOUNT_AMOUNT' ] );
			}

			// Refund
			$toRefund	+=	$total - $percentage;
		}

		// add to order payment
		$this->db->insert( store_prefix() . 'nexo_commandes_paiements', array(
			'REF_COMMAND_CODE'		=>	$order_code,
			'MONTANT'				=>	- floatval( $toRefund ),
			'AUTHOR'				=>	$this->post( 'author' ),
			'DATE_CREATION'			=> 	date_now(),
            'PAYMENT_TYPE'          =>  'cash' // cash payment is set as default refund payment
		) );

		// Edit order status
		$query			=	$this->db->where( 'CODE', $order_code )->get( store_prefix() . 'nexo_commandes' );
		$order			=	$query->result_array();

		// Completely refunded
		// Changing order status
		if( $toRefund == $order[0][ 'TOTAL' ] ) {
			$data		        =	array(
				'TYPE'		    =>		'nexo_order_refunded'
			);
		} else if( $toRefund < floatval( $order[0][ 'TOTAL' ] ) ) { // partial refund
			$data		        =	array(
				'TYPE'		    =>		'nexo_order_partialy_refunded'
			);
		}

		// Set new Total for this order
        $data[ 'TOTAL' ]	    =	floatval( $order[0][ 'TOTAL' ] ) - $toRefund;
        $data[ 'DATE_MOD' ]     =   date_now(); // @since 3.8.0

		$this->db->where( 'CODE', $order_code )->update( store_prefix() . 'nexo_commandes', $data );

		$this->__success();
	}

    /**
     *  Sales Details
     *  @param
     *  @return
    **/

    public function sales_detailed_post()
    {
        $startOfDay         =   Carbon::parse( $this->post( 'start_date' ) )->startOfDay()->toDateTimeString();
        $endOfDay           =   Carbon::parse( $this->post( 'end_date' ) )->endOfDay()->toDateTimeString();
        $query              =   $this->db->select( '
            ' . store_prefix() . 'nexo_commandes.ID as ID,
            ' . store_prefix() . 'nexo_commandes.TOTAL as TOTAL,
            ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE,
            ' . store_prefix() . 'nexo_commandes.CODE as CODE,
            ' . store_prefix() . 'nexo_commandes_produits.QUANTITE,
            ' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
            ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX,
            ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
            ' . store_prefix() . 'nexo_commandes.REMISE_TYPE as REMISE_TYPE,
            ' . store_prefix() . 'nexo_commandes.REMISE,
            ' . store_prefix() . 'nexo_commandes.REMISE_PERCENT,
            ' . store_prefix() . 'nexo_commandes.PAYMENT_TYPE,
            aauth_users.name as AUTHOR_NAME,
            aauth_users.id as AUTHOR_ID,
        ' )
        ->from( store_prefix() . 'nexo_commandes' )
        ->join(
            store_prefix() . 'nexo_commandes_produits',
            store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . store_prefix() . 'nexo_commandes.CODE'
        )
        ->join(
            'aauth_users',
            'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR'
        )
        ->join(
            store_prefix() . 'nexo_articles',
            store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR'
        )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $startOfDay )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $endOfDay )
        ->where( store_prefix() . 'nexo_commandes.TYPE', 'nexo_order_comptant' )
        ->where( store_prefix() . 'nexo_commandes.TOTAL >', 0 )
        ->get();

        $this->response( $query->result(), 200 );

    }

}
