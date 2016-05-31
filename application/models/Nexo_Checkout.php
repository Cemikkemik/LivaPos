<?php
// Load Carbon Library Namespace
use Carbon\Carbon;

class Nexo_Checkout extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Command Save
     * @access public
     * @return string
     * @param post
    **/
    
    public function commandes_save($post)
    {
        // Protecting
        if (! User::can('create_shop_orders')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        global $Options;
        /**
         * Bug, en cas de réduction, le total de la commande affiche un montant inexacte
        **/
        
        // Selon les options, le champ des remise peut en pas être définit
        $post[ 'REMISE' ]        =    isset($post[ 'REMISE' ]) ? $post[ 'REMISE' ] : 0;
        $post[ 'REF_CLIENT' ]    =    isset($post[ 'REF_CLIENT' ]) ? $post[ 'REF_CLIENT' ] : 0;
        $post[ 'SOMME_PERCU' ]    =    isset($post[ 'SOMME_PERCU' ]) ? $post[ 'SOMME_PERCU' ] : 0;
        $post[ 'PAYMENT_TYPE' ] =    isset($post[ 'PAYMENT_TYPE' ]) ? $post[ 'PAYMENT_TYPE' ] : 0;
        
        $client                    =    riake('REF_CLIENT', $post);
        $payment                =    riake('PAYMENT_TYPE', $post);
        $post[ 'SOMME_PERCU' ]    =    intval(riake('SOMME_PERCU', $post));
        $somme_percu            =    intval($post[ 'SOMME_PERCU' ]);
        $remise                    =    intval(riake('REMISE', $post));
        $produits                =    riake('order_products', $post);
        $othercharge            =    intval(riake('other_charge', $post));
        $ttWithCharge            =    intval(riake('total_value_with_charge', $post)) ;
        $total                    =    intval(riake('order_total', $post)) ;
        $vat                    =    floatval(riake('order_vat', $post));
        
        /**
         * Définir le type 
        **/
        
        // @since 2.6 , added VAT
		// $this->load->config( 'nexo' );

        if ($somme_percu >= $ttWithCharge + $vat) {
            $post[ 'TYPE' ]    =   	'nexo_order_comptant'; // Comptant
        } elseif ($somme_percu == 0) {
            $post[ 'TYPE' ] =    	'nexo_order_devis'; // Devis
        } elseif ($somme_percu < ($ttWithCharge + $vat) && $somme_percu > 0) {
            $post[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
        }
        
        // Other: Ristourne

        $post[ 'RISTOURNE' ] = $othercharge;
                
        // Calcul Total	

        $post[ 'TOTAL' ]    =    $total; // - ( $othercharge + intval( @$post[ 'REMISE' ] ) );

        // Author

        $post[ 'AUTHOR' ]    =    User::id();
        
        // Saving discount type

        $post[ 'DISCOUNT_TYPE' ]    = @$Options[ 'discount_type' ];
        
        // VAT

        $post[ 'TVA' ]                =    $vat;
        
        /**
         * First Index is set as payment type
        **/
        
        $post[ 'PAYMENT_TYPE' ]    =    $post[ 'PAYMENT_TYPE' ] == '' ?
            // Default paiement type
            is_numeric(@$Options[ 'default_payment_means' ]) ? $Options[ 'default_payment_means' ] : 1
            // end default paiement type
        : $post[ 'PAYMENT_TYPE' ];
        
        // Date

        $post[ 'DATE_CREATION' ]=    date_now();
        
        // Code

        $post[ 'CODE' ]            =    $this->random_code();
        
        // Client
        /**
         * Increate Client Product
        **/
        
        $post[ 'REF_CLIENT' ]    =    $post[ 'REF_CLIENT' ] == '' ?
            // Start Loop for Default Compte client
            is_numeric(@$Options[ 'default_compte_client' ]) ? $Options[ 'default_compte_client' ] : 1
            // End loop for default compte client
            : $post[ 'REF_CLIENT' ];
        // Augmenter la quantité de produit du client

        $query                    =    $this->db->where('ID', $post[ 'REF_CLIENT' ])->get('nexo_clients');
        $result                    =    $query->result_array();
        $total_commands            =    intval($result[0][ 'NBR_COMMANDES' ]) + 1;
        $overal_commands        =    intval($result[0][ 'OVERALL_COMMANDES' ]) + 1;
        
        $this->db->set('NBR_COMMANDES', $total_commands);
        $this->db->set('OVERALL_COMMANDES', $overal_commands);
        
        // Désactivation des réduction auto pour le client par défaut
        if ($post[ 'REF_CLIENT' ] != @$Options[ 'default_compte_client' ]) {
        
            // Verifie si le client doit profiter de la réduction
            if (@$Options[ 'discount_type' ] != 'disable') {
                // On définie si en fonction des réglages, l'on peut accorder une réduction au client
                if ($total_commands >= intval(@$Options[ 'how_many_before_discount' ]) - 1 && $result[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                    $this->db->set('DISCOUNT_ACTIVE', 1);
                } elseif ($total_commands >= @$Options[ 'how_many_before_discount' ] && $result[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                    $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                    $this->db->set('NBR_COMMANDES', 1); // le nombre de commande est également désactivé
                }
            }
        } // fin désactivation réduction auto pour le client par défaut

        $this->db->where('ID', $post[ 'REF_CLIENT' ])
        ->update('nexo_clients');
        
        /**
         * Reducing Qte
        **/
        
        foreach (force_array(riake('order_products', $post)) as $prod) {
            $json    =    json_decode($prod);
            $this->db->where('CODEBAR', $json->codebar)->update('nexo_articles', array(
                'QUANTITE_RESTANTE'    =>    intval($json->quantite_restante) - intval($json->qte),
                'QUANTITE_VENDU'    =>    intval($json->quantite_vendu) + intval($json->qte)
            ));
            
            // Adding to order product
            $this->db->insert('nexo_commandes_produits', array(
                'REF_PRODUCT_CODEBAR'    =>    $json->codebar,
                'REF_COMMAND_CODE'        =>    $post[ 'CODE' ],
                'QUANTITE'                =>    $json->qte,
                'PRIX'                    =>    $json->price,
                'PRIX_TOTAL'            =>    intval($json->qte) * intval($json->price)
            ));
        }
        
        // New Action
        $this->events->do_action('nexo_create_order', $post);
        
        return $post;
    }
    
    /**
     * Create random Code
     * 
     * @param Int length
     * @return String
    **/
    
    public function random_code($length = 6)
    {
        $allCode    =    $this->options->get('order_code');
        /**
         * Count product to increase length
        **/
        do {
            // abcdefghijklmnopqrstuvwxyz
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        } while (in_array($randomString, force_array($allCode)));
        
        $allCode[]    =    $randomString;
        $this->options->set('order_code', $allCode);
        
        return $randomString;
    }
    
    /**
     * Command Update
     * Update a command
     * [new permission ready]
     *
     * @param Array
     * @return Array
    **/
    
    public function commandes_update($post)
    {
        // Protecting
        if (! User::can('edit_shop_orders')) {
            redirect(array( 'dashboard', 'access-denied' ));
        }
        
        global $Options;
        $segments        =    $this->uri->segment_array();
        $command_id        =    end($segments) ;

        // Delete all product from this command
        /**
         * Bug, en cas de réduction, le total de la commande affiche un montant inexacte
        **/
        
        $client            =    riake('REF_CLIENT', $post);
        $payment        =    riake('PAYMENT_TYPE', $post);
        $post[ 'SOMME_PERCU' ]    =    intval(riake('SOMME_PERCU', $post));
        $somme_percu    =    intval($post[ 'SOMME_PERCU' ]);
        $remise            =    intval(riake('REMISE', $post));
        $produits        =    riake('order_products', $post);
        $othercharge    =    intval(riake('other_charge', $post));
        $ttWithCharge    =    intval(riake('total_value_with_charge', $post)) ;
        $total            =    intval(riake('order_total', $post)) ;
        $vat            =    riake('order_vat', $post);
        
        // Old Command
        $query                =    $this->db->where('ID', $command_id)->get('nexo_commandes');
        $result_commandes    =    $query->result_array();
        
        /**
         * Définir le type 
        **/

        // @since 2.6 , added VAT
		// $this->load->config( 'nexo' );

        if ($somme_percu >= $ttWithCharge + $vat) {
            $post[ 'TYPE' ]    =    'nexo_order_comptant';// Comptant
        } elseif ($somme_percu == 0) {
            $post[ 'TYPE' ] =    	'nexo_order_devis'; // Devis
        } elseif ($somme_percu < ($ttWithCharge + $vat) && $somme_percu > 0) {
            $post[ 'TYPE' ]    =    'nexo_order_advance'; // Avance
        }
        
        // Other: Ristourne

        $post[ 'RISTOURNE' ] =    $othercharge;
                
        // Calcul Total		

        $post[ 'TOTAL' ]    =    $total; // - ( intval( @$post[ 'REMISE' ] ) );

        // Author

        $post[ 'AUTHOR' ]    =    User::id();
        
        // Saving discount type

        $post[ 'DISCOUNT_TYPE' ]    = @$Options[ 'discount_type' ];
        
        // VAT

        $post[ 'TVA' ]                =    $vat;
        
        // Payment Type
        /**
         * First Index is set as payment type
        **/
        
        $post[ 'PAYMENT_TYPE' ]    =    $post[ 'PAYMENT_TYPE' ] == '' ?
            // Default paiement type
            is_numeric(@$Options[ 'default_payment_means' ]) ? $Options[ 'default_payment_means' ] : 1
            // end default paiement type
        : $post[ 'PAYMENT_TYPE' ];
        
        // Date

        $post[ 'DATE_MOD' ]    =    date_now();
        
        // Client
        /**
         * Increate Client Product
        **/
        
        $post[ 'REF_CLIENT' ]    =    $post[ 'REF_CLIENT' ] == '' ?
            // Start Loop for Default Compte client
            is_numeric(@$Options[ 'default_compte_client' ]) ? $Options[ 'default_compte_client' ] : 1
            // End loop for default compte client
        : $post[ 'REF_CLIENT' ];
        
        // Si le client a changé
        if (intval($result_commandes[0][ 'REF_CLIENT' ]) != $post[ 'REF_CLIENT' ]) {
        
            // Augmenter la quantité de produit du client
            $query                    =    $this->db->where('ID', $post[ 'REF_CLIENT' ])->get('nexo_clients');
            $result                    =    $query->result_array();
            
            $this->db
            ->set('NBR_COMMANDES', intval($result[0][ 'NBR_COMMANDES' ]) + 1)
            ->set('OVERALL_COMMANDES', intval($result[0][ 'OVERALL_COMMANDES' ]) + 1);
            
            $total_commands            =    intval($result[0][ 'NBR_COMMANDES' ]) + 1;
            $overal_commands        =    intval($result[0][ 'OVERALL_COMMANDES' ]) + 1;
            
            // Désactivation des reductions pour le client par défaut
            if ($post[ 'REF_CLIENT' ] != @$Options[ 'default_compte_client' ]) {
            
                // Verifie si le nouveau client doit profiter de la réduction
                if (@$Options[ 'discount_type' ] != 'disable') {
                    // On définie si en fonction des réglages, l'on peut accorder une réduction au client
                    if ($total_commands >= intval(@$Options[ 'how_many_before_discount' ]) - 1 && $result[0][ 'DISCOUNT_ACTIVE' ] == 0) {
                        echo 'here';
                        $this->db->set('DISCOUNT_ACTIVE', 1);
                    } elseif ($total_commands >= @$Options[ 'how_many_before_discount' ] && $result[0][ 'DISCOUNT_ACTIVE' ] == 1) {
                        echo 'there';
                        $this->db->set('DISCOUNT_ACTIVE', 0); // bénéficiant d'une reduction sur cette commande, la réduction est désactivée
                        $this->db->set('NBR_COMMANDES', 0); // le nombre de commande est également désactivé
                    }
                }
            } // Fin désactivation réduction automatique pour le client par défaut

            // Fin des modifications du client en cours.
            $this->db->where('ID', $post[ 'REF_CLIENT' ])
            ->update('nexo_clients');
            
            // Reduire pour le précédent client

            $query                    =    $this->db->where('ID',  $result_commandes[0][ 'REF_CLIENT' ])->get('nexo_clients');
            $result                    =    $query->result_array();
            
            // Le nombre de commande ne peut pas être inférieur à 0;

            $this->db
            ->set('NBR_COMMANDES',  intval($result_commandes[0][ 'REF_CLIENT' ]) == 0 ? 0 : intval($result[0][ 'NBR_COMMANDES' ]) - 1)
            ->set('OVERALL_COMMANDES',  intval($result_commandes[0][ 'REF_CLIENT' ]) == 0 ? 0 : intval($result[0][ 'OVERALL_COMMANDES' ]) - 1)
            ->where('ID', $result_commandes[0][ 'REF_CLIENT' ])
            ->update('nexo_clients');
        }
        
        /**
         * Reducing Qte
        **/
        
        // Restauration des produits à la boutique
        $query        =    $this->db->where('REF_COMMAND_CODE', $post[ 'command_code' ])->get('nexo_commandes_produits');
        $old_products    =    $query->result_array();
        
        
        // incremente les produits restaurés
        foreach ($old_products as $product) {
            $this->db
                ->set('QUANTITE_RESTANTE', '`QUANTITE_RESTANTE` + ' . intval($product[ 'QUANTITE' ]), false)
                ->set('QUANTITE_VENDU', '`QUANTITE_VENDU` - ' . intval($product[ 'QUANTITE' ]), false)
                ->where('CODEBAR', $product[ 'REF_PRODUCT_CODEBAR' ])
                ->update('nexo_articles');
        }
        
        // Suppression des produits de la commande
        $this->db->where('REF_COMMAND_CODE', $post[ 'command_code' ])->delete('nexo_commandes_produits');
        
        // Adding articles
        foreach (force_array(riake('order_products', $post)) as $prod) {
            $json    =    json_decode($prod);
            $this->db->where('CODEBAR', $json->codebar)->update('nexo_articles', array(
                'QUANTITE_RESTANTE'    =>    (intval($json->quantite_restante) - intval($json->qte)),
                'QUANTITE_VENDU'    =>    intval($json->quantite_vendu) + intval($json->qte)
            ));
            
            // Adding to order product
            $this->db->insert('nexo_commandes_produits', array(
                'REF_PRODUCT_CODEBAR'    =>    $json->codebar,
                'REF_COMMAND_CODE'        =>    $post[ 'command_code' ],
                'QUANTITE'                =>    $json->qte,
                'PRIX'                    =>    $json->price,
                'PRIX_TOTAL'            =>    intval($json->qte) * intval($json->price)
            ));
        };
        
        // New Action
        $this->events->do_action('nexo_edit_order', $post);
            
        return $post;
    }
    
    /**
     * Command delete
     *
     * @param Array
     * @return Array
    **/
    
    public function commandes_delete($post)
    {
        if (class_exists('User')) {
            // Protecting
            // Protecting
            if (! User::can('delete_shop_orders')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }
        }
        
        
        // Remove product from this cart
        $query    =    $this->db
                    ->where('ID', $post)
                    ->get('nexo_commandes');
                    
        $command=    $query->result_array();
        
        // Récupère les produits vendu
        $query    =    $this->db
                    ->where('REF_COMMAND_CODE', $command[0][ 'CODE' ])
                    ->get('nexo_commandes_produits');
                    
        $produits        =    $query->result_array();
        
        $products_data    =    array();
        // parcours les produits disponibles pour les regrouper
        foreach ($produits as $product) {
            $products_data[ $product[ 'REF_PRODUCT_CODEBAR' ] ] =    intval($product[ 'QUANTITE' ]);
        }
        
        // retirer le décompte des commandes passées par le client
        $query        =    $this->db->where('ID', $command[0][ 'REF_CLIENT' ])->get('nexo_clients');
        $client        =    $query->result_array();
        
        $this->db->where('ID', $command[0][ 'REF_CLIENT' ])->update('nexo_clients', array(
            'NBR_COMMANDES'        =>    (intval($client[0][ 'NBR_COMMANDES' ]) - 1) < 0 ? 0 : intval($client[0][ 'NBR_COMMANDES' ]) - 1,
            'OVERALL_COMMANDES'    =>    (intval($client[0][ 'OVERALL_COMMANDES' ]) - 1) < 0 ? 0 : intval($client[0][ 'OVERALL_COMMANDES' ]) - 1,
        ));
        
        // Parcours des produits pour restaurer les quantités vendues
        foreach ($products_data as $codebar => $quantity) {
            // Quantité actuelle
            $query    =    $this->db->where('CODEBAR', $codebar)->get('nexo_articles');
            $article    =    $query->result_array();
            
            // Cumul et restauration des quantités
            $this->db->where('CODEBAR', $codebar)->update('nexo_articles', array(
                'QUANTITE_VENDU'        =>        intval($article[0][ 'QUANTITE_VENDU' ]) - $quantity,
                'QUANTITE_RESTANTE'        =>        intval($article[0][ 'QUANTITE_RESTANTE' ]) + $quantity,
            ));
        }
        // retire les produits vendu du panier de cette commande et les renvoies au stock
        $this->db->where('REF_COMMAND_CODE', $command[0][ 'CODE' ])->delete('nexo_commandes_produits');
        
        // New Action
        $this->events->do_action('nexo_delete_order', $post);
    }
    
    /**
     * Create Permission
     *
     * @return Void
    **/
    
    public function create_permissions()
    {
        $this->aauth        =    $this->users->auth;
        // Create Cashier
        Group::create(
            'shop_clashier',
            __('Caissier', 'nexo'),
            true,
            __('Permet de gérer la vente des articles, la gestion des clients', 'nexo')
        );
        
        // Create Shop Manager
        Group::create(
            'shop_manager',
            __('Gérant de la boutique', 'nexo'),
            true,
            __('Permet de gérer la vente des articles, la gestion des clients, la modification des réglages et accède aux rapports.', 'nexo')
        );
        
        // Create Shop Tester
        Group::create(
            'shop_tester',
            __('Privilège pour testeur', 'nexo'),
            true,
            __('Effectue toutes tâches d\'ajout et de modification. Ne peux pas supprimer du contenu.', 'nexo')
        );
        
        // Shop Orders
        $this->aauth->create_perm('create_shop_orders',    __('Gestion des commandes', 'nexo'),            __('Peut créer des commandes', 'nexo'));
        $this->aauth->create_perm('edit_shop_orders',    __('Modification des commandes', 'nexo'),            __('Peut modifier des commandes', 'nexo'));
        $this->aauth->create_perm('delete_shop_orders',    __('Suppression des commandes', 'nexo'),            __('Peut supprimer des commandes', 'nexo'));
        
        // Shop Items
        $this->aauth->create_perm('create_shop_items',        __('Créer des articles', 'nexo'),            __('Peut créer des produits', 'nexo'));
        $this->aauth->create_perm('edit_shop_items',        __('Modifier des articles', 'nexo'),            __('Peut modifier des produits', 'nexo'));
        $this->aauth->create_perm('delete_shop_items',    __('Supprimer des articles', 'nexo'),        __('Peut supprimer des produits', 'nexo'));
        
        // Shop Categories
        $this->aauth->create_perm('create_shop_categories',  __('Créer des catégories', 'nexo'),        __('Crée les catégories', 'nexo'));
        $this->aauth->create_perm('edit_shop_categories',  __('Modifier des catégories', 'nexo'),        __('Modifie les catégories', 'nexo'));
        $this->aauth->create_perm('delete_shop_categories',  __('Supprimer des catégories', 'nexo'),        __('Supprime les catégories', 'nexo'));
        
        // Shop radius
        $this->aauth->create_perm('create_shop_radius',    __('Créer des rayons', 'nexo'),                __('Crée les rayons', 'nexo'));
        $this->aauth->create_perm('edit_shop_radius',    __('Modifier des rayons', 'nexo'),                __('Modifie les rayons', 'nexo'));
        $this->aauth->create_perm('delete_shop_radius',    __('Supprimer des rayons', 'nexo'),                __('Supprime les rayons', 'nexo'));
        
        // Shop Shipping
        $this->aauth->create_perm('create_shop_shippings',    __('Créer des collections', 'nexo'),        __('Crée les collections', 'nexo'));
        $this->aauth->create_perm('edit_shop_shippings',    __('Modifier des collections', 'nexo'),        __('Modifie les collections', 'nexo'));
        $this->aauth->create_perm('delete_shop_shippings',    __('Supprimer des collections', 'nexo'),        __('Supprime les collections', 'nexo'));
        
        // Shop Provider
        $this->aauth->create_perm('create_shop_providers',    __('Créer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        $this->aauth->create_perm('edit_shop_providers',    __('Modifier des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        $this->aauth->create_perm('delete_shop_providers',    __('Supprimer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        
        // Shop Customers
        $this->aauth->create_perm('create_shop_customers',    __('Créer des clients', 'nexo'),        __('Création des clients', 'nexo'));
        $this->aauth->create_perm('edit_shop_customers',    __('Modifier des clients', 'nexo'),        __('Modification des clients', 'nexo'));
        $this->aauth->create_perm('delete_shop_customers',    __('Supprimer des clients', 'nexo'),        __('Suppression des clients', 'nexo'));
        
        // Shop Customers Group
        $this->aauth->create_perm('create_shop_customers_groups',    __('Créer des groupes de clients', 'nexo'),        __('Création des groupes de clients', 'nexo'));
        $this->aauth->create_perm('edit_shop_customers_groups',    __('Modifier des groupes de clients', 'nexo'),        __('Modification des groupes de clients', 'nexo'));
        $this->aauth->create_perm('delete_shop_customers_groups',    __('Supprimer des groupes de clients', 'nexo'),        __('Suppression des groupes de clients', 'nexo'));
        
        // Shop Payments Means
        $this->aauth->create_perm('create_shop_payments_means',    __('Créer des moyens de paiement', 'nexo'),        __('Création des moyens de paiement', 'nexo'));
        $this->aauth->create_perm('edit_shop_payments_means',    __('Modifier des moyens de paiement', 'nexo'),        __('Modification des moyens de paiement', 'nexo'));
        $this->aauth->create_perm('delete_shop_payments_means',    __('Supprimer des moyens de paiement', 'nexo'),        __('Suppression des moyens de paiement', 'nexo'));
        // Shop Order Types
        $this->aauth->create_perm('create_shop_order_types',    __('Créer des types de commandes', 'nexo'),        __('Création des types de commandes', 'nexo'));
        $this->aauth->create_perm('edit_shop_order_types',    __('Modifier des types de commandes', 'nexo'),        __('Modification des types de commandes', 'nexo'));
        $this->aauth->create_perm('delete_shop_order_types',    __('Supprimer des types de commandes', 'nexo'),        __('Suppression des types de commandes', 'nexo'));
        
        // Shop Purchase Invoices
        $this->aauth->create_perm('create_shop_purchases_invoices',    __('Créer des factures d\'achats', 'nexo'),        __('Création des factures d\'achats', 'nexo'));
        $this->aauth->create_perm('edit_shop_purchases_invoices',    __('Modifier des factures d\'achats', 'nexo'),        __('Modification des factures d\'achats', 'nexo'));
        $this->aauth->create_perm('delete_shop_purchases_invoices',    __('Supprimer des factures d\'achats', 'nexo'),        __('Suppression des factures d\'achats', 'nexo'));
        // Shop Order Types
        $this->aauth->create_perm('create_shop_backup',    __('Créer des sauvegardes', 'nexo'),        __('Création des sauvegardes', 'nexo'));
        $this->aauth->create_perm('edit_shop_backup',    __('Modifier des sauvegardes', 'nexo'),        __('Modification des sauvegardes', 'nexo'));
        $this->aauth->create_perm('delete_shop_backup',    __('Supprimer des sauvegardes', 'nexo'),        __('Suppression des sauvegardes', 'nexo'));
        
        // Shop Track User
        $this->aauth->create_perm('read_shop_user_tracker',    __('Lit le flux d\'activité des utilisateurs', 'nexo'),        __('Lit le flux d\'activité des utilisateurs', 'nexo'));
        $this->aauth->create_perm('delete_shop_user_tracker',    __('Efface le flux d\'actvite des utilisateurs', 'nexo'),        __('Efface le flux d\'actvite des utilisateurs', 'nexo'));
        

        // Shop Read Reports
        $this->aauth->create_perm('read_shop_reports', __('Lecture des rapports & statistiques', 'nexo'),            __('Autorise la lecture des rapports', 'nexo'));
        /**
         * Permission for Cashier
        **/
        
        // Orders
        $this->aauth->allow_group('shop_clashier', 'create_shop_orders');
        $this->aauth->allow_group('shop_clashier', 'edit_shop_orders');
        $this->aauth->allow_group('shop_clashier', 'delete_shop_orders');
        
        // Customers
        $this->aauth->allow_group('shop_clashier', 'create_shop_customers');
        $this->aauth->allow_group('shop_clashier', 'delete_shop_customers');
        $this->aauth->allow_group('shop_clashier', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->allow_group('shop_clashier', 'create_shop_customers_groups');
        $this->aauth->allow_group('shop_clashier', 'delete_shop_customers_groups');
        $this->aauth->allow_group('shop_clashier', 'edit_shop_customers_groups');
        
        // Profile
        $this->aauth->allow_group('shop_clashier', 'edit_profile');
        
        /**
         * Permission for Shop Manager
        **/
        
        // Orders
        $this->aauth->allow_group('shop_manager', 'create_shop_orders');
        $this->aauth->allow_group('shop_manager', 'edit_shop_orders');
        $this->aauth->allow_group('shop_manager', 'delete_shop_orders');
        
        // Customers
        $this->aauth->allow_group('shop_manager', 'create_shop_customers');
        $this->aauth->allow_group('shop_manager', 'delete_shop_customers');
        $this->aauth->allow_group('shop_manager', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->allow_group('shop_manager', 'create_shop_customers_groups');
        $this->aauth->allow_group('shop_manager', 'delete_shop_customers_groups');
        $this->aauth->allow_group('shop_manager', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->allow_group('shop_manager', 'create_shop_items');
        $this->aauth->allow_group('shop_manager', 'edit_shop_items');
        $this->aauth->allow_group('shop_manager', 'delete_shop_items');
        
        // Shop categories
        $this->aauth->allow_group('shop_manager', 'create_shop_categories');
        $this->aauth->allow_group('shop_manager', 'edit_shop_categories');
        $this->aauth->allow_group('shop_manager', 'delete_shop_categories');
        
        // Shop Radius
        $this->aauth->allow_group('shop_manager', 'create_shop_radius');
        $this->aauth->allow_group('shop_manager', 'edit_shop_radius');
        $this->aauth->allow_group('shop_manager', 'delete_shop_radius');
        
        // Shop Shipping
        $this->aauth->allow_group('shop_manager', 'create_shop_shippings');
        $this->aauth->allow_group('shop_manager', 'edit_shop_shippings');
        $this->aauth->allow_group('shop_manager', 'delete_shop_shippings');
        
        // Shop Provider
        $this->aauth->allow_group('shop_manager', 'create_shop_providers');
        $this->aauth->allow_group('shop_manager', 'edit_shop_providers');
        $this->aauth->allow_group('shop_manager', 'delete_shop_providers');
        
        // Shop Payment Means
        $this->aauth->allow_group('shop_manager', 'create_shop_payments_means');
        $this->aauth->allow_group('shop_manager', 'edit_shop_payments_means');
        $this->aauth->allow_group('shop_manager', 'delete_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->allow_group('shop_manager', 'create_shop_order_types');
        $this->aauth->allow_group('shop_manager', 'edit_shop_order_types');
        $this->aauth->allow_group('shop_manager', 'delete_shop_order_types');
        
        // Shop Options
        $this->aauth->allow_group('shop_manager', 'create_options');
        $this->aauth->allow_group('shop_manager', 'edit_options');
        $this->aauth->allow_group('shop_manager', 'delete_options');
        
        // Shop Purchase Invoices
        $this->aauth->allow_group('shop_manager', 'create_shop_purchases_invoices');
        $this->aauth->allow_group('shop_manager', 'edit_shop_purchases_invoices');
        $this->aauth->allow_group('shop_manager', 'delete_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->allow_group('shop_manager', 'create_shop_backup');
        $this->aauth->allow_group('shop_manager', 'edit_shop_backup');
        $this->aauth->allow_group('shop_manager', 'delete_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->allow_group('shop_manager', 'read_shop_user_tracker');
        $this->aauth->allow_group('shop_manager', 'delete_shop_user_tracker');
        
        // Read Reports
        $this->aauth->allow_group('shop_manager', 'read_shop_reports');
        // Profile
        $this->aauth->allow_group('shop_manager', 'edit_profile');
        
        /**
         * Permission for Master
        **/
        
        // Orders
        $this->aauth->allow_group('master', 'create_shop_orders');
        $this->aauth->allow_group('master', 'edit_shop_orders');
        $this->aauth->allow_group('master', 'delete_shop_orders');
        
        // Customers
        $this->aauth->allow_group('master', 'create_shop_customers');
        $this->aauth->allow_group('master', 'delete_shop_customers');
        $this->aauth->allow_group('master', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->allow_group('master', 'create_shop_customers_groups');
        $this->aauth->allow_group('master', 'delete_shop_customers_groups');
        $this->aauth->allow_group('master', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->allow_group('master', 'create_shop_items');
        $this->aauth->allow_group('master', 'edit_shop_items');
        $this->aauth->allow_group('master', 'delete_shop_items');
        
        // Shop categories
        $this->aauth->allow_group('master', 'create_shop_categories');
        $this->aauth->allow_group('master', 'edit_shop_categories');
        $this->aauth->allow_group('master', 'delete_shop_categories');
        
        // Shop Radius
        $this->aauth->allow_group('master', 'create_shop_radius');
        $this->aauth->allow_group('master', 'edit_shop_radius');
        $this->aauth->allow_group('master', 'delete_shop_radius');
        
        // Shop Shipping
        $this->aauth->allow_group('master', 'create_shop_shippings');
        $this->aauth->allow_group('master', 'edit_shop_shippings');
        $this->aauth->allow_group('master', 'delete_shop_shippings');
        
        // Shop Provider
        $this->aauth->allow_group('master', 'create_shop_providers');
        $this->aauth->allow_group('master', 'edit_shop_providers');
        $this->aauth->allow_group('master', 'delete_shop_providers');
        
        // Shop Payment Means
        $this->aauth->allow_group('master', 'create_shop_payments_means');
        $this->aauth->allow_group('master', 'edit_shop_payments_means');
        $this->aauth->allow_group('master', 'delete_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->allow_group('master', 'create_shop_order_types');
        $this->aauth->allow_group('master', 'edit_shop_order_types');
        $this->aauth->allow_group('master', 'delete_shop_order_types');
        
        // Shop Purchase Invoices
        $this->aauth->allow_group('master', 'create_shop_purchases_invoices');
        $this->aauth->allow_group('master', 'edit_shop_purchases_invoices');
        $this->aauth->allow_group('master', 'delete_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->allow_group('master', 'create_shop_backup');
        $this->aauth->allow_group('master', 'edit_shop_backup');
        $this->aauth->allow_group('master', 'delete_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->allow_group('master', 'read_shop_user_tracker');
        $this->aauth->allow_group('master', 'delete_shop_user_tracker');
        
        // Read Reports
        $this->aauth->allow_group('master', 'read_shop_reports');
        
        /**
         * Permission for Shop Test
        **/
        
        // Orders
        $this->aauth->allow_group('shop_tester', 'create_shop_orders');
        $this->aauth->allow_group('shop_tester', 'edit_shop_orders');
        
        // Customers
        $this->aauth->allow_group('shop_tester', 'create_shop_customers');
        $this->aauth->allow_group('shop_tester', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->allow_group('shop_tester', 'create_shop_customers_groups');
        $this->aauth->allow_group('shop_tester', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->allow_group('shop_tester', 'create_shop_items');
        $this->aauth->allow_group('shop_tester', 'edit_shop_items');
        
        // Shop categories
        $this->aauth->allow_group('shop_tester', 'create_shop_categories');
        $this->aauth->allow_group('shop_tester', 'edit_shop_categories');
        
        // Shop Radius
        $this->aauth->allow_group('shop_tester', 'create_shop_radius');
        $this->aauth->allow_group('shop_tester', 'edit_shop_radius');
        
        // Shop Shipping
        $this->aauth->allow_group('shop_tester', 'create_shop_shippings');
        $this->aauth->allow_group('shop_tester', 'edit_shop_shippings');
        
        // Shop Provider
        $this->aauth->allow_group('shop_tester', 'create_shop_providers');
        $this->aauth->allow_group('shop_tester', 'edit_shop_providers');
        
        // Shop Payment Means
        $this->aauth->allow_group('shop_tester', 'create_shop_payments_means');
        $this->aauth->allow_group('shop_tester', 'edit_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->allow_group('shop_tester', 'create_shop_order_types');
        $this->aauth->allow_group('shop_tester', 'edit_shop_order_types');
        
        // Shop Purchase Invoices
        $this->aauth->allow_group('shop_tester', 'create_shop_purchases_invoices');
        $this->aauth->allow_group('shop_tester', 'edit_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->allow_group('shop_tester', 'create_shop_backup');
        $this->aauth->allow_group('shop_tester', 'edit_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->allow_group('shop_tester', 'read_shop_user_tracker');
        
        // Read Reports
        $this->aauth->allow_group('shop_tester', 'read_shop_reports');
        
        // Profile
        // $this->aauth->allow_group('shop_tester', 'edit_profile');
    }
    
    /** 
     * Delete Permission
     *
     * @return Void
    **/
    
    public function delete_permissions()
    {
        $this->aauth        =    $this->users->auth;
        
        /**
         * Denied Permissions
        **/
        
        // Shop Manager
        // Orders
        $this->aauth->deny_group('shop_manager', 'create_shop_orders');
        $this->aauth->deny_group('shop_manager', 'edit_shop_orders');
        $this->aauth->deny_group('shop_manager', 'delete_shop_orders');
        
        // Customers
        $this->aauth->deny_group('shop_manager', 'create_shop_customers');
        $this->aauth->deny_group('shop_manager', 'delete_shop_customers');
        $this->aauth->deny_group('shop_manager', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->deny_group('shop_manager', 'create_shop_customers_groups');
        $this->aauth->deny_group('shop_manager', 'delete_shop_customers_groups');
        $this->aauth->deny_group('shop_manager', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->deny_group('shop_manager', 'create_shop_items');
        $this->aauth->deny_group('shop_manager', 'edit_shop_items');
        $this->aauth->deny_group('shop_manager', 'delete_shop_items');
        
        // Shop categories
        $this->aauth->deny_group('shop_manager', 'create_shop_categories');
        $this->aauth->deny_group('shop_manager', 'edit_shop_categories');
        $this->aauth->deny_group('shop_manager', 'delete_shop_categories');
        
        // Shop Radius
        $this->aauth->deny_group('shop_manager', 'create_shop_radius');
        $this->aauth->deny_group('shop_manager', 'edit_shop_radius');
        $this->aauth->deny_group('shop_manager', 'delete_shop_radius');
        
        // Shop Shipping
        $this->aauth->deny_group('shop_manager', 'create_shop_shipping');
        $this->aauth->deny_group('shop_manager', 'edit_shop_shipping');
        $this->aauth->deny_group('shop_manager', 'delete_shop_shipping');
        
        // Shop Provider
        $this->aauth->deny_group('shop_manager', 'create_shop_providers');
        $this->aauth->deny_group('shop_manager', 'edit_shop_providers');
        $this->aauth->deny_group('shop_manager', 'delete_shop_providers');
        
        // Shop Payment Means
        $this->aauth->deny_group('shop_manager', 'create_shop_payments_means');
        $this->aauth->deny_group('shop_manager', 'edit_shop_payments_means');
        $this->aauth->deny_group('shop_manager', 'delete_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->deny_group('shop_manager', 'create_shop_order_types');
        $this->aauth->deny_group('shop_manager', 'edit_shop_order_types');
        $this->aauth->deny_group('shop_manager', 'delete_shop_order_types');
        
        // Shop purchase invoice
        $this->aauth->deny_group('shop_manager', 'create_shop_purchases_invoices');
        $this->aauth->deny_group('shop_manager', 'edit_shop_purchases_invoices');
        $this->aauth->deny_group('shop_manager', 'delete_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->deny_group('shop_manager', 'create_shop_backup');
        $this->aauth->deny_group('shop_manager', 'edit_shop_backup');
        $this->aauth->deny_group('shop_manager', 'delete_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->deny_group('shop_manager', 'read_shop_user_tracker');
        $this->aauth->deny_group('shop_manager', 'delete_shop_user_tracker');
        
        // Update Profile
        $this->aauth->deny_group('shop_manager', 'edit_profile');

        // Read Reports
        $this->aauth->deny_group('shop_manager', 'read_shop_reports');
        
        // Master
        // Orders
        $this->aauth->deny_group('master', 'create_shop_orders');
        $this->aauth->deny_group('master', 'edit_shop_orders');
        $this->aauth->deny_group('master', 'delete_shop_orders');
        
        // Customers
        $this->aauth->deny_group('master', 'create_shop_customers');
        $this->aauth->deny_group('master', 'delete_shop_customers');
        $this->aauth->deny_group('master', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->deny_group('master', 'create_shop_customers_groups');
        $this->aauth->deny_group('master', 'delete_shop_customers_groups');
        $this->aauth->deny_group('master', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->deny_group('master', 'create_shop_items');
        $this->aauth->deny_group('master', 'edit_shop_items');
        $this->aauth->deny_group('master', 'delete_shop_items');
        
        // Shop categories
        $this->aauth->deny_group('master', 'create_shop_categories');
        $this->aauth->deny_group('master', 'edit_shop_categories');
        $this->aauth->deny_group('master', 'delete_shop_categories');
        
        // Shop Radius
        $this->aauth->deny_group('master', 'create_shop_radius');
        $this->aauth->deny_group('master', 'edit_shop_radius');
        $this->aauth->deny_group('master', 'delete_shop_radius');
        
        // Shop Shipping
        $this->aauth->deny_group('master', 'create_shop_shipping');
        $this->aauth->deny_group('master', 'edit_shop_shipping');
        $this->aauth->deny_group('master', 'delete_shop_shipping');
        
        // Shop Provider
        $this->aauth->deny_group('master', 'create_shop_providers');
        $this->aauth->deny_group('master', 'edit_shop_providers');
        $this->aauth->deny_group('master', 'delete_shop_providers');
        
        // Shop Payment Means
        $this->aauth->deny_group('master', 'create_shop_payments_means');
        $this->aauth->deny_group('master', 'edit_shop_payments_means');
        $this->aauth->deny_group('master', 'delete_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->deny_group('master', 'create_shop_order_types');
        $this->aauth->deny_group('master', 'edit_shop_order_types');
        $this->aauth->deny_group('master', 'delete_shop_order_types');
        
        // Shop purchase invoice
        $this->aauth->deny_group('master', 'create_shop_purchases_invoices');
        $this->aauth->deny_group('master', 'edit_shop_purchases_invoices');
        $this->aauth->deny_group('master', 'delete_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->deny_group('master', 'create_shop_backup');
        $this->aauth->deny_group('master', 'edit_shop_backup');
        $this->aauth->deny_group('master', 'delete_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->deny_group('master', 'read_shop_user_tracker');
        $this->aauth->deny_group('master', 'delete_shop_user_tracker');
        
        // Read Reports
        $this->aauth->deny_group('master', 'read_shop_reports');
        
        // Denied Permissions for Shop Test		
        // Orders
        $this->aauth->deny_group('shop_tester', 'create_shop_orders');
        $this->aauth->deny_group('shop_tester', 'edit_shop_orders');
        
        // Customers
        $this->aauth->deny_group('shop_tester', 'create_shop_customers');
        $this->aauth->deny_group('shop_tester', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->deny_group('shop_tester', 'create_shop_customers_groups');
        $this->aauth->deny_group('shop_tester', 'edit_shop_customers_groups');
        
        // Shop items
        $this->aauth->deny_group('shop_tester', 'create_shop_items');
        $this->aauth->deny_group('shop_tester', 'edit_shop_items');
        
        // Shop categories
        $this->aauth->deny_group('shop_tester', 'create_shop_categories');
        $this->aauth->deny_group('shop_tester', 'edit_shop_categories');
        
        // Shop Radius
        $this->aauth->deny_group('shop_tester', 'create_shop_radius');
        $this->aauth->deny_group('shop_tester', 'edit_shop_radius');
        
        // Shop Shipping
        $this->aauth->deny_group('shop_tester', 'create_shop_shipping');
        $this->aauth->deny_group('shop_tester', 'edit_shop_shipping');
        
        // Shop Provider
        $this->aauth->deny_group('shop_tester', 'create_shop_providers');
        $this->aauth->deny_group('shop_tester', 'edit_shop_providers');
        
        // Shop Payment Means
        $this->aauth->deny_group('shop_tester', 'create_shop_payments_means');
        $this->aauth->deny_group('shop_tester', 'edit_shop_payments_means');
        
        // Shop Orders type
        $this->aauth->deny_group('shop_tester', 'create_shop_order_types');
        $this->aauth->deny_group('shop_tester', 'edit_shop_order_types');
        
        // Shop purchase invoice
        $this->aauth->deny_group('shop_tester', 'create_shop_purchases_invoices');
        $this->aauth->deny_group('shop_tester', 'edit_shop_purchases_invoices');
        $this->aauth->deny_group('shop_tester', 'delete_shop_purchases_invoices');
        
        // Shop Backup
        $this->aauth->deny_group('shop_tester', 'create_shop_backup');
        $this->aauth->deny_group('shop_tester', 'edit_shop_backup');
        
        // Shop Track User Activity
        $this->aauth->deny_group('shop_tester', 'read_shop_user_tracker');
        
        // Read Reports
        $this->aauth->deny_group('shop_tester', 'read_shop_reports');
        
        // Update Profile
        // $this->aauth->deny_group('shop_tester', 'edit_profile');

        // For Cashier
        // Orders
        $this->aauth->deny_group('shop_clashier', 'create_shop_orders');
        $this->aauth->deny_group('shop_clashier', 'edit_shop_orders');
        $this->aauth->deny_group('shop_clashier', 'delete_shop_orders');
        
        // Customers
        $this->aauth->deny_group('shop_clashier', 'create_shop_customers');
        $this->aauth->deny_group('shop_clashier', 'delete_shop_customers');
        $this->aauth->deny_group('shop_clashier', 'edit_shop_customers');
        
        // Customers Groups
        $this->aauth->deny_group('shop_clashier', 'create_shop_customers_groups');
        $this->aauth->deny_group('shop_clashier', 'delete_shop_customers_groups');
        $this->aauth->deny_group('shop_clashier', 'edit_shop_customers_groups');
        
        // Update Profile
        $this->aauth->deny_group('shop_cashier', 'edit_profile');
        
        // Delete Custom Groups
        $this->aauth->delete_group('shop_clashier');
        $this->aauth->delete_group('shop_manager');
        $this->aauth->delete_group('shop_tester');
    }
    
    /**
     * Get Order
     * 
     * @return array
    **/
    
    public function get_order($order_id = null)
    {
        if ($order_id != null && ! is_array($order_id)) {
            $this->db->where('ID', $order_id);
        } elseif (is_array($order_id)) {
            foreach ($order_id as $mark => $value) {
                $this->db->where($mark, $value);
            }
        }
        $query    =    $this->db->get('nexo_commandes');
        if ($query->result_array()) {
            return $query->result_array();
        }
        return false;
    }
    
    /**
     * Get order products
     *
     * @param Int order id
     * @param Bool return all
    **/
    
    public function get_order_products($order_id, $return_all = false)
    {
        $query    =    $this->db
            ->where('ID', $order_id)
            ->get('nexo_commandes');
        if ($query->result_array()) {
            $data    =    $query->result_array();
            $sub_query    =    $this->db
                ->select('*,
				nexo_articles.DESIGN as DESIGN')
                ->from('nexo_commandes')
                ->join('nexo_commandes_produits', 'nexo_commandes.CODE = nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
                ->join('nexo_articles', 'nexo_articles.CODEBAR = nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner')
                ->where('REF_COMMAND_CODE', $data[0][ 'CODE' ])
                ->get();
            $sub_data    = $sub_query->result_array();
            if ($sub_data) {
                if ($return_all) {
                    return array(
                        'order'        =>    $data,
                        'products'    =>    $sub_data
                    );
                }
                return $sub_query->result_array();
            }
            return false;
        }
        return false;
    }
    
    /**
     * Get order type
     *
     * @param Int
     * @return String order type
    **/
    
    public function get_order_type($order_type)
    {
        $query    =    $this->db->where('ID', $order_type)->get('nexo_types_de_commandes');
        $data    =    $query->result_array();
        return $data[0][ 'DESIGN' ];
    }
}
