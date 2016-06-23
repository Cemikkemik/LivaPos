<?php
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
        $query    =    $this->db->get('nexo_commandes');
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
            'DATE_CREATION'        =>    $this->post('DATE_CREATION')
        );
        
        // Order Type
        if (intval($this->post('SOMME_PERCU')) >= intval($this->post('TOTAL'))) {
            $order_details[ 'TYPE' ]    =    'nexo_order_comptant'; // Comptant
        } elseif (intval($this->post('SOMME_PERCU')) == 0) {
            $order_details[ 'TYPE' ]    =   'nexo_order_devis'; // Devis
        } elseif (intval($this->post('SOMME_PERCU')) < intval($this->post('TOTAL')) && intval($this->post('SOMME_PERCU')) > 0) {
            $order_details[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
        }
        
        // Increase customers purchases
        $query                        =    $this->db->where('ID', $this->post('REF_CLIENT'))->get('nexo_clients');
        $result                        =    $query->result_array();
        $total_commands                =    intval($result[0][ 'NBR_COMMANDES' ]) + 1;
        $overal_commands            =    intval($result[0][ 'OVERALL_COMMANDES' ]) + 1;
        
        $this->db->set('NBR_COMMANDES', $total_commands);
        $this->db->set('OVERALL_COMMANDES', $overal_commands);
        
        // Disable automatic discount
        if ($this->post('REF_CLIENT') != $this->post('DEFAULT_CUSTOMER')) {
        
            // Verifie si le client doit profiter de la réduction
            if ($this->post('DISCOUNT_TYPE') != 'disable') {
                // On définie si en fonction des réglages, l'on peut accorder une réduction au client
                if ($total_commands >= intval($this->post('HMB_DISCOUNT')) - 1 && $result[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                    $this->db->set('DISCOUNT_ACTIVE', 1);
                } elseif ($total_commands >= $this->post('HMB_DISCOUNT') && $result[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                    $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                    $this->db->set('NBR_COMMANDES', 1); // le nombre de commande est également désactivé
                }
            }
        }
        // fin désactivation réduction auto pour le client par défaut
        $this->db->where('ID', $this->post('REF_CLIENT'))
        ->update('nexo_clients');
        
        // Save Order items

        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE );
        **/
        
        foreach ($this->post('ITEMS') as $item) {
            $this->db->where('CODEBAR', $item[2])->update('nexo_articles', array(
                'QUANTITE_RESTANTE'        =>    intval($item[5]) - intval($item[1]),
                'QUANTITE_VENDU'        =>    intval($item[4]) + intval($item[1])
            ));
            
            // Adding to order product
            $this->db->insert('nexo_commandes_produits', array(
                'REF_PRODUCT_CODEBAR'        =>    $item[2],
                'REF_COMMAND_CODE'            =>    $order_details[ 'CODE' ],
                'QUANTITE'                    =>    $item[1],
                'PRIX'                        =>    $item[3],
                'PRIX_TOTAL'                =>    intval($item[1]) * intval($item[3])
            ));
        }
        
        $this->db->insert('nexo_commandes', $order_details);
        
        $current_order    =    $this->db->where('CODE', $order_details[ 'CODE' ])
                            ->get('nexo_commandes')
                            ->result_array();
        
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
                            ->get('nexo_commandes')
                            ->result_array();

        // Only incomplete order can be edited
        if ($current_order[0][ 'TYPE' ] != 'nexo_order_devis') {
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
            'DATE_MOD'            =>    $this->put('DATE_CREATION')
        );
        
        // Order Type
        if (intval($this->put('SOMME_PERCU')) >= intval($this->put('TOTAL'))) {
            $order_details[ 'TYPE' ]    =    'nexo_order_comptant'; // Comptant
        } elseif (intval($this->put('SOMME_PERCU')) == 0) {
            $order_details[ 'TYPE' ]    =   'nexo_order_devis'; // Devis
        } elseif (intval($this->put('SOMME_PERCU')) < intval($this->put('TOTAL')) && intval($this->put('SOMME_PERCU')) > 0) {
            $order_details[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
        }
                
        // If customer has changed
        if ($this->put('REF_CLIENT') != $old_order['order'][0][ 'REF_CLIENT' ]) {
            
            // Increase customers purchases
            $query                        =    $this->db->where('ID', $this->put('REF_CLIENT'))->get('nexo_clients');
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
                    if ($total_commands >= intval($this->put('HMB_DISCOUNT')) - 1 && $client[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                        $this->db->set('DISCOUNT_ACTIVE', 1);
                    } elseif ($total_commands >= $this->put('HMB_DISCOUNT') && $client[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                        $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                        $this->db->set('NBR_COMMANDES', 0); // le nombre de commande est également désactivé
                    }
                }
            }
            
            $this->db->where('ID', $this->put('REF_CLIENT'))
            ->update('nexo_clients');
            
            // Reduce for the previous customer
            $query                    =    $this->db->where('ID',  $old_order['order'][0][ 'REF_CLIENT' ])->get('nexo_clients');
            $old_customer             =    $query->result_array();
            
            // Le nombre de commande ne peut pas être inférieur à 0;
            $this->db
            ->set('NBR_COMMANDES',  intval($old_customer[0][ 'NBR_COMMANDES' ]) == 0 ? 0 : intval($old_customer[0][ 'NBR_COMMANDES' ]) - 1)
            ->set('OVERALL_COMMANDES',  intval($old_customer[0][ 'OVERALL_COMMANDES' ]) == 0 ? 0 : intval($old_customer[0][ 'OVERALL_COMMANDES' ]) - 1)
            ->where('ID', $old_order['order'][0][ 'REF_CLIENT' ])
            ->update('nexo_clients');
        }
        
        // Restore Bought items
        foreach ($old_order[ 'products' ] as $product) {
            $this->db
            ->set('QUANTITE_RESTANTE', '`QUANTITE_RESTANTE` + ' . intval($product[ 'QUANTITE' ]), false)
            ->set('QUANTITE_VENDU', '`QUANTITE_VENDU` - ' . intval($product[ 'QUANTITE' ]), false)
            ->where('CODEBAR', $product[ 'REF_PRODUCT_CODEBAR' ])
            ->update('nexo_articles');
        }
        
        // Delete item from order
        $this->db->where('REF_COMMAND_CODE', $old_order[ 'order' ][0][ 'CODE' ])->delete('nexo_commandes_produits');
        
        // Save Order items		
        /**
         * Item structure
         * array( ID, QUANTITY_ADDED, BARCODE, PRICE, QTE_SOLD, LEFT_QTE );
        **/
        
        foreach ($this->put('ITEMS') as $item) {
            
            // Get Items 
            $fresh_items    =    $this->db->where('CODEBAR', $item[2])->get('nexo_articles')->result_array();
            
            $this->db->where('CODEBAR', $item[2])->update('nexo_articles', array(
                'QUANTITE_RESTANTE'        =>    intval($fresh_items[0][ 'QUANTITE_RESTANTE' ]) - intval($item[1]),
                'QUANTITE_VENDU'        =>    intval($fresh_items[0][ 'QUANTITE_VENDU' ]) + intval($item[1])
            ));
            
            // Adding to order product
            $this->db->insert('nexo_commandes_produits', array(
                'REF_PRODUCT_CODEBAR'        =>    $item[2],
                'REF_COMMAND_CODE'            =>    $old_order[ 'order' ][0][ 'CODE' ],
                'QUANTITE'                    =>    $item[1],
                'PRIX'                        =>    $item[3],
                'PRIX_TOTAL'                =>    intval($item[1]) * intval($item[3])
            ));
        }
        
        $this->db->where('ID', $order_id)->update('nexo_commandes', $order_details);
        
        $this->response(array(
            'order_id'        =>    $order_id,
            'order_type'    =>    $order_details[ 'TYPE' ],
            'order_code'    =>    $current_order[0][ 'CODE' ]
        ), 200);
    }
    
    /**
     * Get order using dates
     *
     * @params string datetime
     * @params string datetime
     * @return json
    **/
    
    public function order_by_dates_post($order_type = 'all')
    {
        $this->db->where('DATE_CREATION >=', $this->post('start'));
        $this->db->where('DATE_CREATION <=', $this->post('end'));
        
        if ($order_type != 'all') {
            $this->db->where('TYPE', $order_type);
        }
        
        $query    =    $this->db->get('nexo_commandes');
        $this->response($query->result(), 200);
    }
}
