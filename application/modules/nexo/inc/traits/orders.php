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

        $order_details              =    array(
            'RISTOURNE'             =>    $this->post('RISTOURNE'),
            'REMISE'                =>    $this->post('REMISE'),
            // @since 2.9.6
            'REMISE_PERCENT'        =>    $this->post( 'REMISE_PERCENT' ),
            'REMISE_TYPE'           =>    $this->post( 'REMISE_TYPE' ),
            // @endSince
            'RABAIS'                =>    $this->post('RABAIS'),
            'GROUP_DISCOUNT'        =>    $this->post('GROUP_DISCOUNT'),
            'TOTAL'                 =>    $this->post('TOTAL'),
            'AUTHOR'                =>    $author_id,
            'PAYMENT_TYPE'          =>    $this->post('PAYMENT_TYPE'),
            'REF_CLIENT'            =>    $this->post('REF_CLIENT'),
            'TVA'                   =>    $this->post('TVA'),
            'SOMME_PERCU'           =>    $this->post('SOMME_PERCU'),
            'CODE'                  =>    $this->Nexo_Checkout->shuffle_code(),
            'DATE_CREATION'         =>    $this->post('DATE_CREATION'),
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
			if( $item[7] == 'percentage' && $item[9] != '0' ) {
				$discount_amount		=	__floatval( ( __floatval($item[1]) * __floatval($item[3]) ) * floatval( $item[9] ) / 100 );
			} elseif( $item[7] == 'flat' ) {
				$discount_amount		=	__floatval( $item[8] );
			} else {
				$discount_amount		=	0;
			}

			$item_data		=	array(
				'REF_PRODUCT_CODEBAR'  =>    $item[2],
				'REF_COMMAND_CODE'     =>    $order_details[ 'CODE' ],
				'QUANTITE'             =>    $item[1],
				'PRIX'                 =>    $item[3],
				'PRIX_TOTAL'           =>    ( __floatval($item[1]) * __floatval($item[3]) ) - $discount_amount,
				// @since 2.9.0
				'DISCOUNT_TYPE'			=>	$item[7],
				'DISCOUNT_AMOUNT'		=>	$item[8],
				'DISCOUNT_PERCENT'		=>	$item[9]
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

		if( is_array( $this->post( 'payments' ) ) ) {
			foreach( $this->post( 'payments' ) as $payment ) {

				$Curl->post( site_url( array( 'rest', 'nexo', 'order_payment', store_get_param( '?' ) ) ), array(
					'author'		=>	$author_id,
					'date'			=>	$this->post('DATE_CREATION'),
					'payment_type'	=>	$payment[ 'namespace' ],
					'amount'		=>	$payment[ 'amount' ],
					'order_code'	=>	$current_order[0][ 'CODE' ]
				) );

			}
		}

        $this->response(array(
            'order_id'        =>    $current_order[0][ 'ID' ],
            'order_type'    =>    $order_details[ 'TYPE' ],
            'order_code'    =>    $current_order[0][ 'CODE' ]
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
            // @since 2.9.6
            'REMISE_PERCENT'        =>    $this->put( 'REMISE_PERCENT' ),
            'REMISE_TYPE'           =>    $this->put( 'REMISE_TYPE' ),
            // @endSince
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
			if( $item[7] == 'percentage' && $item[9] != '0' ) {
				$discount_amount		=	__floatval( ( __floatval($item[1]) * __floatval($item[3]) ) * floatval( $item[9] ) / 100 );
			} elseif( $item[7] == 'flat' ) {
				$discount_amount		=	__floatval( $item[8] );
			} else {
				$discount_amount		=	0;
			}

            // Adding to order product
			$item_data			=	array(
                'REF_PRODUCT_CODEBAR'        =>    $item[2],
                'REF_COMMAND_CODE'            =>    $old_order[ 'order' ][0][ 'CODE' ],
                'QUANTITE'                    =>    $item[1],
                'PRIX'                        =>    $item[3],
                'PRIX_TOTAL'                =>    ( __floatval($item[1]) * __floatval($item[3]) ) - $discount_amount,
				// @since 2.9.0
				'DISCOUNT_TYPE'			=>	$item[7],
				'DISCOUNT_AMOUNT'		=>	$item[8],
				'DISCOUNT_PERCENT'		=>	$item[9]
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

		if( is_array( $this->put( 'payments' ) ) ) {
			foreach( $this->put( 'payments' ) as $payment ) {

				$Curl->post( site_url( array( 'rest', 'nexo', 'order_payment', store_get_param( '?' ) ) ), array(
					'author'		=>	$author_id,
					'date'			=>	$this->put('DATE_CREATION'),
					'payment_type'	=>	$payment[ 'namespace' ],
					'amount'		=>	$payment[ 'amount' ],
					'order_code'	=>	$current_order[0][ 'CODE' ]
				) );
			}
		}

        $this->response(array(
            'order_id'        =>    $order_id,
            'order_type'    =>    $order_details[ 'TYPE' ],
            'order_code'    =>    $current_order[0][ 'CODE' ]
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

        if ($order_type != 'all') {
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

		$this->response( $order, 200 );

		$this->__empty();
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

	/**
	 * Get order item with their defective stock
	 * @param order code
	**/

	public function order_items_defectives_get( $order_code )
	{
		$this->db->select( '*,' .
		store_prefix() . 'nexo_articles_defectueux.QUANTITE as CURRENT_DEFECTIVE_QTE' )
		->from( store_prefix() . 'nexo_articles' )
		->join( store_prefix() . 'nexo_articles_defectueux', store_prefix() . 'nexo_articles_defectueux.REF_ARTICLE_BARCODE = ' . store_prefix() . 'nexo_articles.CODEBAR', 'left' )
		->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR = ' . store_prefix() . 'nexo_articles.CODEBAR', 'inner' )
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

			$this->db->where( 'REF_PRODUCT_CODEBAR', $item[ 'REF_PRODUCT_CODEBAR' ] )->update( store_prefix() . 'nexo_commandes_produits', array(
				'QUANTITE'	=>	$item[ 'QUANTITE' ],
			) );

			// If a defective stock exists
			if( intval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ) ) {

				$this->db->insert( store_prefix() . 'nexo_articles_defectueux', array(
					'REF_ARTICLE_BARCODE'	=>	$item[ 'REF_PRODUCT_CODEBAR' ],
					'QUANTITE'				=>	$item[ 'CURRENT_DEFECTIVE_QTE' ],
					'AUTHOR'				=>	$this->post( 'author' ),
					'DATE_CREATION'			=>	$this->post( 'date' ),
					'REF_COMMAND_CODE'		=>	$order_code
				) );

				// Increase defective item in stock
				$this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )->update( store_prefix() . 'nexo_articles', array(
					'DEFECTUEUX'				=>	intval( $item[ 'DEFECTUEUX' ] ) + intval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ),
				) );

                // a defective item can't be considered as sold item
                $this->db->where( 'CODEBAR', $item[ 'CODEBAR' ] )->set( 'QUANTITE_VENDUE', 'QUANTITE_VENDUE - ' . intval( $item[ 'CURRENT_DEFECTIVE_QTE' ] ) );
			}

			$total					=	floatval( $item[ 'PRIX' ] ) * floatval( $item[ 'CURRENT_DEFECTIVE_QTE' ] );

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
			'MONTANT'				=>	- intval( $toRefund ),
			'AUTHOR'				=>	$this->post( 'author' ),
			'DATE_CREATION'			=> 	$this->post( 'date' ),
            'PAYMENT_TYPE'          =>  'cash' // cash payment is set as default refund payment
		) );

		// Edit order status
		$query			=	$this->db->where( 'CODE', $order_code )->get( store_prefix() . 'nexo_commandes' );
		$order			=	$query->result_array();

		// Completely refunded
		// Changing order status
		if( $toRefund == $order[0][ 'TOTAL' ] ) {
			$data		=	array(
				'TYPE'		=>		'nexo_order_refunded'
			);
		} else if( $toRefund < floatval( $order[0][ 'TOTAL' ] ) ) { // partial refund
			$data		=	array(
				'TYPE'		=>		'nexo_order_partialy_refunded'
			);
		}

		// Set new Total for this order
		$data[ 'TOTAL' ]	=	floatval( $order[0][ 'TOTAL' ] ) - $toRefund;

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
            ' . store_prefix() . 'nexo_commandes.TOTAL as TOTAL,
            ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE,
            ' . store_prefix() . 'nexo_commandes.CODE as CODE,
            ' . store_prefix() . 'nexo_commandes_produits.QUANTITE,
            ' . store_prefix() . 'nexo_articles.DESIGN as DESIGN,
            ' . store_prefix() . 'nexo_commandes_produits.PRIX as PRIX,
            ' . store_prefix() . 'nexo_commandes.TYPE as TYPE,
            ' . store_prefix() . 'nexo_commandes.ID as ID,
            ' . store_prefix() . 'nexo_commandes.REMISE_TYPE as REMISE_TYPE,
            ' . store_prefix() . 'nexo_commandes.REMISE,
            ' . store_prefix() . 'nexo_commandes.REMISE_PERCENT,
            ' . store_prefix() . 'nexo_commandes.PAYMENT_TYPE,
            ' . store_prefix() . 'aauth_users.name as AUTHOR_NAME,
            ' . store_prefix() . 'aauth_users.id as AUTHOR_ID,
        ' )
        ->from( 'nexo_commandes' )
        ->join(
            store_prefix() . 'nexo_commandes_produits',
            store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' . store_prefix() . 'nexo_commandes.CODE'
        )
        ->join(
            store_prefix() . 'aauth_users',
            store_prefix() . 'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR'
        )
        ->join(
            store_prefix() . 'nexo_articles',
            store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR'
        )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION >=', $startOfDay )
        ->where( store_prefix() . 'nexo_commandes.DATE_CREATION <=', $endOfDay )
        ->get();

        $this->response( $query->result(), 200 );

    }

}
