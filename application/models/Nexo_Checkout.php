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
     * Create random Code
     *
     * @param Int length
     * @return String
    **/

    public function random_code($length = 6)
    {
        $allCode    =    $this->options->get( store_prefix() . 'order_code');
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
        $this->options->set( store_prefix() . 'order_code', $allCode);

        return $randomString;
    }

    /**
     * Shuffle_code : alias of random_code
     *
    **/

    public function shuffle_code($length = 6)
    {
        $Options        =    $this->Options->get();
        $orders_code    =   force_array(@$Options[ 'order_code' ]);
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
        } while (in_array($randomString, force_array($orders_code)));

        $orders_code[]    =    $randomString;
        $this->Options->set('order_code', $orders_code);

        return $randomString;
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
            if (! User::can('delete_shop_orders')) {
                redirect(array( 'dashboard', 'access-denied' ));
            }
        }

        // Remove product from this cart
        $query    =    $this->db
        ->where('ID', $post)
        ->get( store_prefix() . 'nexo_commandes');

        $command    =    $query->result_array();

        if( ! $command ) {
            return false;
        }

        // Récupère les produits vendu
        $query    =    $this->db
        ->where('REF_COMMAND_CODE', $command[0][ 'CODE' ])
        ->get( store_prefix() . 'nexo_commandes_produits');

        $produits        =    $query->result_array();

        $products_data    =    array();
        // parcours les produits disponibles pour les regrouper
        foreach ($produits as $product) {
            $products_data[ $product[ 'REF_PRODUCT_CODEBAR' ] ] =    [
                'QUANTITY'              =>  floatval($product[ 'QUANTITE' ]),
                'COMMAND_PRODUCT_ID'    =>  $product[ 'ID' ],
                'INLINE'                =>  $product[ 'INLINE' ]
            ];
        }

        // retirer le décompte des commandes passées par le client
        $query        =    $this->db->where('ID', $command[0][ 'REF_CLIENT' ])->get( store_prefix() . 'nexo_clients');
        $client        =    $query->result_array();

        $this->db->where('ID', $command[0][ 'REF_CLIENT' ])->update('nexo_clients', array(
            'NBR_COMMANDES'        =>    (floatval($client[0][ 'NBR_COMMANDES' ]) - 1) < 0 ? 0 : floatval($client[0][ 'NBR_COMMANDES' ]) - 1,
            'OVERALL_COMMANDES'    =>    (floatval($client[0][ 'OVERALL_COMMANDES' ]) - 1) < 0 ? 0 : floatval($client[0][ 'OVERALL_COMMANDES' ]) - 1,
        ));

        // Parcours des produits pour restaurer les quantités vendues
        foreach ( $products_data as $codebar => $data ) {
            // Quantité actuelle
            $query    =    $this->db->where('CODEBAR', $codebar)->get( store_prefix() . 'nexo_articles');
            $article    =    $query->result_array();

            // Restoring quantities don't work for inline item
            if( $data[ 'INLINE' ] != '1' ) {
                // Cumul et restauration des quantités
                $this->db->where('CODEBAR', $codebar)->update( store_prefix() . 'nexo_articles', array(
                    'QUANTITE_VENDU'        =>        floatval($article[0][ 'QUANTITE_VENDU' ]) - $data[ 'QUANTITY' ],
                    'QUANTITE_RESTANTE'     =>        floatval($article[0][ 'QUANTITE_RESTANTE' ]) + $data[ 'QUANTITY' ],
                ));
            }

            // Suppresison des meta des produits
            $this->db->where( 'ID', $data[ 'COMMAND_PRODUCT_ID' ] )->delete( store_prefix() . 'nexo_commandes_produits_meta' );
        }

        // retire les produits vendu du panier de cette commande et les renvoies au stock
        $this->db->where('REF_COMMAND_CODE', $command[0][ 'CODE' ])->delete( store_prefix() . 'nexo_commandes_produits');

		// @since 2.9 supprime les paiements
		$this->db->where('REF_COMMAND_CODE', $command[0][ 'CODE' ])->delete( store_prefix() . 'nexo_commandes_paiements');

		// Delete order meta
		$this->db->where( 'REF_ORDER_ID', $command[0][ 'ID' ] )->delete( store_prefix() . 'nexo_commandes_meta' );

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
        // $this->aauth        =    $this->users->auth;
        // // Create Cashier
        // Group::create(
        //     'shop_cashier',
        //     get_instance()->lang->line( 'nexo_cashier' ),
        //     true,
        //     get_instance()->lang->line( 'nexo_cashier_details' )
        // );

        // // Create Shop Manager
        // Group::create(
        //     'shop_manager',
        //     get_instance()->lang->line( 'nexo_shop_manager' ),
        //     true,
        //     get_instance()->lang->line( 'nexo_shop_manager_details' )
        // );

        // // Create Shop Tester
        // Group::create(
        //     'shop_tester',
        //     get_instance()->lang->line( 'nexo_tester' ),
        //     true,
        //     get_instance()->lang->line( 'nexo_tester_details' )
        // );

        // // Shop Orders
        // $this->aauth->create_perm('create_shop_orders',    __('Gestion des commandes', 'nexo'),            __('Peut créer des commandes', 'nexo'));
        // $this->aauth->create_perm('edit_shop_orders',    __('Modification des commandes', 'nexo'),            __('Peut modifier des commandes', 'nexo'));
        // $this->aauth->create_perm('delete_shop_orders',    __('Suppression des commandes', 'nexo'),            __('Peut supprimer des commandes', 'nexo'));

        // // Shop Items
        // $this->aauth->create_perm('create_shop_items',        __('Créer des articles', 'nexo'),            __('Peut créer des produits', 'nexo'));
        // $this->aauth->create_perm('edit_shop_items',        __('Modifier des articles', 'nexo'),            __('Peut modifier des produits', 'nexo'));
        // $this->aauth->create_perm('delete_shop_items',    __('Supprimer des articles', 'nexo'),        __('Peut supprimer des produits', 'nexo'));

        // // Shop Categories
        // $this->aauth->create_perm('create_shop_categories',  __('Créer des catégories', 'nexo'),        __('Crée les catégories', 'nexo'));
        // $this->aauth->create_perm('edit_shop_categories',  __('Modifier des catégories', 'nexo'),        __('Modifie les catégories', 'nexo'));
        // $this->aauth->create_perm('delete_shop_categories',  __('Supprimer des catégories', 'nexo'),        __('Supprime les catégories', 'nexo'));

        // // Shop radius
        // $this->aauth->create_perm('create_shop_radius',    __('Créer des rayons', 'nexo'),                __('Crée les rayons', 'nexo'));
        // $this->aauth->create_perm('edit_shop_radius',    __('Modifier des rayons', 'nexo'),                __('Modifie les rayons', 'nexo'));
        // $this->aauth->create_perm('delete_shop_radius',    __('Supprimer des rayons', 'nexo'),                __('Supprime les rayons', 'nexo'));

        // // Shop Shipping
        // $this->aauth->create_perm('create_shop_shippings',    __('Créer des collections', 'nexo'),        __('Crée les collections', 'nexo'));
        // $this->aauth->create_perm('edit_shop_shippings',    __('Modifier des collections', 'nexo'),        __('Modifie les collections', 'nexo'));
        // $this->aauth->create_perm('delete_shop_shippings',    __('Supprimer des collections', 'nexo'),        __('Supprime les collections', 'nexo'));

        // // Shop Provider
        // $this->aauth->create_perm('create_shop_providers',    __('Créer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        // $this->aauth->create_perm('edit_shop_providers',    __('Modifier des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        // $this->aauth->create_perm('delete_shop_providers',    __('Supprimer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));

        // // Shop Customers
        // $this->aauth->create_perm('create_shop_customers',    __('Créer des clients', 'nexo'),        __('Création des clients', 'nexo'));
        // $this->aauth->create_perm('edit_shop_customers',    __('Modifier des clients', 'nexo'),        __('Modification des clients', 'nexo'));
        // $this->aauth->create_perm('delete_shop_customers',    __('Supprimer des clients', 'nexo'),        __('Suppression des clients', 'nexo'));

        // // Shop Customers Group
        // $this->aauth->create_perm('create_shop_customers_groups',    __('Créer des groupes de clients', 'nexo'),        __('Création des groupes de clients', 'nexo'));
        // $this->aauth->create_perm('edit_shop_customers_groups',    __('Modifier des groupes de clients', 'nexo'),        __('Modification des groupes de clients', 'nexo'));
        // $this->aauth->create_perm('delete_shop_customers_groups',    __('Supprimer des groupes de clients', 'nexo'),        __('Suppression des groupes de clients', 'nexo'));

        // // Shop Purchase Invoices
        // $this->aauth->create_perm('create_shop_purchases_invoices',    __('Créer des factures d\'achats', 'nexo'),        __('Création des factures d\'achats', 'nexo'));
        // $this->aauth->create_perm('edit_shop_purchases_invoices',    __('Modifier des factures d\'achats', 'nexo'),        __('Modification des factures d\'achats', 'nexo'));
        // $this->aauth->create_perm('delete_shop_purchases_invoices',    __('Supprimer des factures d\'achats', 'nexo'),        __('Suppression des factures d\'achats', 'nexo'));
        // // Shop Order Types
        // $this->aauth->create_perm('create_shop_backup',    __('Créer des sauvegardes', 'nexo'),        __('Création des sauvegardes', 'nexo'));
        // $this->aauth->create_perm('edit_shop_backup',    __('Modifier des sauvegardes', 'nexo'),        __('Modification des sauvegardes', 'nexo'));
        // $this->aauth->create_perm('delete_shop_backup',    __('Supprimer des sauvegardes', 'nexo'),        __('Suppression des sauvegardes', 'nexo'));

        // // Shop Track User
        // $this->aauth->create_perm('read_shop_user_tracker',    __('Lit le flux d\'activité des utilisateurs', 'nexo'),        __('Lit le flux d\'activité des utilisateurs', 'nexo'));
        // $this->aauth->create_perm('delete_shop_user_tracker',    __('Efface le flux d\'actvite des utilisateurs', 'nexo'),        __('Efface le flux d\'actvite des utilisateurs', 'nexo'));

        // // Shop Read Reports
        // $this->aauth->create_perm('read_shop_reports', __('Lecture des rapports & statistiques', 'nexo'),            __('Autorise la lecture des rapports', 'nexo'));

		// // Shop Registers
        // $this->aauth->create_perm('create_shop_registers',    $this->lang->line( 'create_registers' ),        $this->lang->line( 'create_registers_details' ));
        // $this->aauth->create_perm('edit_shop_registers',    $this->lang->line( 'edit_registers' ),        $this->lang->line( 'edit_registers_details' ));
        // $this->aauth->create_perm('delete_shop_registers',    $this->lang->line( 'delete_registers' ),       $this->lang->line( 'delete_registers_details' ));
		// $this->aauth->create_perm('view_shop_registers',    $this->lang->line( 'view_registers' ),       $this->lang->line( 'view_registers_details' ));

		// // @since 2.8 Stores
		// $this->aauth->create_perm('create_shop',    $this->lang->line( 'create_shop' ),        $this->lang->line( 'create_shop_details' ));
        // $this->aauth->create_perm('edit_shop',    $this->lang->line( 'edit_shop' ),        $this->lang->line( 'edit_shop_details' ));
        // $this->aauth->create_perm('delete_shop',    $this->lang->line( 'delete_shop' ),       $this->lang->line( 'delete_shop_details' ));
		// $this->aauth->create_perm('enter_shop',    $this->lang->line( 'view_shop' ),       $this->lang->line( 'view_shop_details' ));

        // // Coupons
        // $this->aauth->create_perm('create_coupons',    $this->lang->line( 'create_coupons' ),        $this->lang->line( 'create_coupons_details' ));
        // $this->aauth->create_perm('edit_coupons',    $this->lang->line( 'edit_coupons' ),        $this->lang->line( 'edit_coupons_details' ));
        // $this->aauth->create_perm('delete_coupons',    $this->lang->line( 'delete_coupons' ),        $this->lang->line( 'delete_coupons_details' ));

        // // Item Stock
        // $this->aauth->create_perm('create_item_stock',    $this->lang->line( 'create_item_stock' ),        $this->lang->line( 'create_item_stock_details' ));
        // $this->aauth->create_perm('edit_item_stock',    $this->lang->line( 'edit_item_stock' ),        $this->lang->line( 'edit_item_stock_details' ));
        // $this->aauth->create_perm('delete_item_stock',    $this->lang->line( 'delete_item_stock' ),        $this->lang->line( 'delete_item_stock_details' ));

        // /**
        //  * Permission for Cashier
        // **/

        // // Orders
        // $this->aauth->allow_group('shop_cashier', 'create_shop_orders');
        // $this->aauth->allow_group('shop_cashier', 'edit_shop_orders');
        // $this->aauth->allow_group('shop_cashier', 'delete_shop_orders');

        // // Customers
        // $this->aauth->allow_group('shop_cashier', 'create_shop_customers');
        // $this->aauth->allow_group('shop_cashier', 'delete_shop_customers');
        // $this->aauth->allow_group('shop_cashier', 'edit_shop_customers');

        // // Customers Groups
        // $this->aauth->allow_group('shop_cashier', 'create_shop_customers_groups');
        // $this->aauth->allow_group('shop_cashier', 'delete_shop_customers_groups');
        // $this->aauth->allow_group('shop_cashier', 'edit_shop_customers_groups');

        // // Profile
        // $this->aauth->allow_group('shop_cashier', 'edit_profile');

		// // Registers
		// $this->aauth->allow_group('shop_cashier', 'view_shop_registers');

		// // Shop
		// $this->aauth->allow_group('shop_cashier', 'enter_shop');

        // /**
        //  * Permission for Shop Manager
        // **/

        // // Orders
        // $this->aauth->allow_group('shop_manager', 'create_shop_orders');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_orders');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_orders');

        // // Customers
        // $this->aauth->allow_group('shop_manager', 'create_shop_customers');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_customers');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_customers');

        // // Customers Groups
        // $this->aauth->allow_group('shop_manager', 'create_shop_customers_groups');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_customers_groups');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_customers_groups');

        // // Shop items
        // $this->aauth->allow_group('shop_manager', 'create_shop_items');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_items');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_items');

        // // Shop categories
        // $this->aauth->allow_group('shop_manager', 'create_shop_categories');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_categories');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_categories');

        // // Shop Radius
        // $this->aauth->allow_group('shop_manager', 'create_shop_radius');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_radius');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_radius');

        // // Shop Shipping
        // $this->aauth->allow_group('shop_manager', 'create_shop_shippings');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_shippings');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_shippings');

        // // Shop Provider
        // $this->aauth->allow_group('shop_manager', 'create_shop_providers');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_providers');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_providers');

        // // Shop Options
        // $this->aauth->allow_group('shop_manager', 'create_options');
        // $this->aauth->allow_group('shop_manager', 'edit_options');
        // $this->aauth->allow_group('shop_manager', 'delete_options');

        // // Shop Purchase Invoices
        // $this->aauth->allow_group('shop_manager', 'create_shop_purchases_invoices');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_purchases_invoices');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_purchases_invoices');

        // // Shop Backup
        // $this->aauth->allow_group('shop_manager', 'create_shop_backup');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_backup');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_backup');

        // // Shop Track User Activity
        // $this->aauth->allow_group('shop_manager', 'read_shop_user_tracker');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_user_tracker');

        // // Stock Entry
        // $this->aauth->allow_group('shop_manager', 'create_item_stock');
        // $this->aauth->allow_group('shop_manager', 'edit_item_stock');
        // $this->aauth->allow_group('shop_manager', 'delete_item_stock');

        // // Read Reports
        // $this->aauth->allow_group('shop_manager', 'read_shop_reports');
        // // Profile
        // $this->aauth->allow_group('shop_manager', 'edit_profile');

		// // @since 2.7.5
		// // Creating registers
        // $this->aauth->allow_group('shop_manager', 'create_shop_registers');
        // $this->aauth->allow_group('shop_manager', 'edit_shop_registers');
        // $this->aauth->allow_group('shop_manager', 'delete_shop_registers');
		// $this->aauth->allow_group('shop_manager', 'view_shop_registers');

		// // @since 2.8
		// $this->aauth->allow_group('shop_manager', 'enter_shop');
        // $this->aauth->allow_group('shop_manager', 'create_shop');
        // $this->aauth->allow_group('shop_manager', 'delete_shop');
		// $this->aauth->allow_group('shop_manager', 'edit_shop');


        // /**
        //  * Permission for Master
        // **/

        // // Orders
        // $this->aauth->allow_group('master', 'create_shop_orders');
        // $this->aauth->allow_group('master', 'edit_shop_orders');
        // $this->aauth->allow_group('master', 'delete_shop_orders');

        // // Customers
        // $this->aauth->allow_group('master', 'create_shop_customers');
        // $this->aauth->allow_group('master', 'delete_shop_customers');
        // $this->aauth->allow_group('master', 'edit_shop_customers');

        // // Customers Groups
        // $this->aauth->allow_group('master', 'create_shop_customers_groups');
        // $this->aauth->allow_group('master', 'delete_shop_customers_groups');
        // $this->aauth->allow_group('master', 'edit_shop_customers_groups');

        // // Shop items
        // $this->aauth->allow_group('master', 'create_shop_items');
        // $this->aauth->allow_group('master', 'edit_shop_items');
        // $this->aauth->allow_group('master', 'delete_shop_items');

        // // Shop categories
        // $this->aauth->allow_group('master', 'create_shop_categories');
        // $this->aauth->allow_group('master', 'edit_shop_categories');
        // $this->aauth->allow_group('master', 'delete_shop_categories');

        // // Shop Radius
        // $this->aauth->allow_group('master', 'create_shop_radius');
        // $this->aauth->allow_group('master', 'edit_shop_radius');
        // $this->aauth->allow_group('master', 'delete_shop_radius');

        // // Shop Shipping
        // $this->aauth->allow_group('master', 'create_shop_shippings');
        // $this->aauth->allow_group('master', 'edit_shop_shippings');
        // $this->aauth->allow_group('master', 'delete_shop_shippings');

        // // Shop Provider
        // $this->aauth->allow_group('master', 'create_shop_providers');
        // $this->aauth->allow_group('master', 'edit_shop_providers');
        // $this->aauth->allow_group('master', 'delete_shop_providers');

        // // Shop Purchase Invoices
        // $this->aauth->allow_group('master', 'create_shop_purchases_invoices');
        // $this->aauth->allow_group('master', 'edit_shop_purchases_invoices');
        // $this->aauth->allow_group('master', 'delete_shop_purchases_invoices');

        // // Shop Backup
        // $this->aauth->allow_group('master', 'create_shop_backup');
        // $this->aauth->allow_group('master', 'edit_shop_backup');
        // $this->aauth->allow_group('master', 'delete_shop_backup');

        // // Shop Track User Activity
        // $this->aauth->allow_group('master', 'read_shop_user_tracker');
        // $this->aauth->allow_group('master', 'delete_shop_user_tracker');

        // // Read Reports
        // $this->aauth->allow_group('master', 'read_shop_reports');

		// // @since 2.7.5
		// // Creating registers
        // $this->aauth->allow_group('master', 'create_shop_registers');
        // $this->aauth->allow_group('master', 'edit_shop_registers');
        // $this->aauth->allow_group('master', 'delete_shop_registers');
		// $this->aauth->allow_group('master', 'view_shop_registers');

		// // @since 2.8
		// $this->aauth->allow_group('master', 'enter_shop');
        // $this->aauth->allow_group('master', 'create_shop');
        // $this->aauth->allow_group('master', 'delete_shop');
		// $this->aauth->allow_group('master', 'edit_shop');

        // //@since 3.0.20
        // // Stock Entry
        // $this->aauth->allow_group('master', 'create_item_stock');
        // $this->aauth->allow_group('master', 'edit_item_stock');
        // $this->aauth->allow_group('master', 'delete_item_stock');

        // /**
        //  * Permission for Shop Test
        // **/

        // // Orders
        // $this->aauth->allow_group('shop_tester', 'create_shop_orders');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_orders');

        // // Customers
        // $this->aauth->allow_group('shop_tester', 'create_shop_customers');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_customers');

        // // Customers Groups
        // $this->aauth->allow_group('shop_tester', 'create_shop_customers_groups');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_customers_groups');

        // // Shop items
        // $this->aauth->allow_group('shop_tester', 'create_shop_items');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_items');

        // // Shop categories
        // $this->aauth->allow_group('shop_tester', 'create_shop_categories');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_categories');

        // // Shop Radius
        // $this->aauth->allow_group('shop_tester', 'create_shop_radius');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_radius');

        // // Shop Shipping
        // $this->aauth->allow_group('shop_tester', 'create_shop_shippings');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_shippings');

        // // Shop Provider
        // $this->aauth->allow_group('shop_tester', 'create_shop_providers');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_providers');

        // // Shop Purchase Invoices
        // $this->aauth->allow_group('shop_tester', 'create_shop_purchases_invoices');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_purchases_invoices');

        // // Shop Backup
        // $this->aauth->allow_group('shop_tester', 'create_shop_backup');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_backup');

        // // Shop Track User Activity
        // $this->aauth->allow_group('shop_tester', 'read_shop_user_tracker');

        // // Read Reports
        // $this->aauth->allow_group('shop_tester', 'read_shop_reports');

		// // @since 2.7.5
		// // Creating registers
        // $this->aauth->allow_group('shop_tester', 'create_shop_registers');
        // $this->aauth->allow_group('shop_tester', 'edit_shop_registers');
		// $this->aauth->allow_group('shop_tester', 'view_shop_registers');

        // //@since 3.0.20
        // // Stock Entry
        // $this->aauth->allow_group('shop_tester', 'create_item_stock');
        // $this->aauth->allow_group('shop_tester', 'edit_item_stock');

		// // @since 2.8
		// $this->aauth->allow_group('master', 'enter_shop');
        // $this->aauth->allow_group('master', 'create_shop');
		// $this->aauth->allow_group('master', 'edit_shop');

        // // Profile
        // // $this->aauth->allow_group('shop_tester', 'edit_profile');

        // // @since 3.0.1 coupons
        // $this->aauth->allow_group( 'shop_cashier', 'create_coupons');
        // $this->aauth->allow_group( 'shop_cashier', 'edit_coupons');
        // $this->aauth->allow_group( 'shop_cashier', 'delete_coupons');

        // $this->aauth->allow_group( 'shop_manager', 'create_coupons');
        // $this->aauth->allow_group( 'shop_manager', 'edit_coupons');
        // $this->aauth->allow_group( 'shop_manager', 'delete_coupons');

        // $this->aauth->allow_group( 'master', 'create_coupons');
        // $this->aauth->allow_group( 'master', 'edit_coupons');
        // $this->aauth->allow_group( 'master', 'delete_coupons');

        // $this->aauth->allow_group( 'shop_tester', 'create_coupons');
        // $this->aauth->allow_group( 'shop_tester', 'edit_coupons');
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

		// Shop Backup
		// @since 2.7.5
        $this->aauth->deny_group('shop_manager', 'create_shop_registers');
        $this->aauth->deny_group('shop_manager', 'edit_shop_registers');
        $this->aauth->deny_group('shop_manager', 'delete_shop_registers');

		// @since 2.8.0
        $this->aauth->deny_group('shop_manager', 'create_shop');
        $this->aauth->deny_group('shop_manager', 'edit_shop');
        $this->aauth->deny_group('shop_manager', 'delete_shop');
		$this->aauth->deny_group('shop_manager', 'enter_shop');

        //@since 3.0.20
        $this->aauth->deny_group('shop_manager', 'create_item_stock');
        $this->aauth->deny_group('shop_manager', 'edit_item_stock');
        $this->aauth->deny_group('shop_manager', 'delete_item_stock');

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

		// Shop Permissions
		// @since 2.8.0
        $this->aauth->deny_group('shop_manager', 'create_shop');
        $this->aauth->deny_group('shop_manager', 'edit_shop');
        $this->aauth->deny_group('shop_manager', 'delete_shop');
		$this->aauth->deny_group('shop_manager', 'enter_shop');

		// Shop Backup
		// @since 2.7.5
        $this->aauth->deny_group('master', 'create_shop_registers');
        $this->aauth->deny_group('master', 'edit_shop_registers');
        $this->aauth->deny_group('master', 'delete_shop_registers');

        //@since 3.0.20
        $this->aauth->deny_group('master', 'create_item_stock');
        $this->aauth->deny_group('master', 'edit_item_stock');
        $this->aauth->deny_group('master', 'delete_item_stock');

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

        // Shop purchase invoice
        $this->aauth->deny_group('shop_tester', 'create_shop_purchases_invoices');
        $this->aauth->deny_group('shop_tester', 'edit_shop_purchases_invoices');

        // Shop Backup
        $this->aauth->deny_group('shop_tester', 'create_shop_backup');
        $this->aauth->deny_group('shop_tester', 'edit_shop_backup');

        // Shop Track User Activity
        $this->aauth->deny_group('shop_tester', 'read_shop_user_tracker');

        // Read Reports
        $this->aauth->deny_group('shop_tester', 'read_shop_reports');

		// Shop Backup
		// @since 2.7.5
        $this->aauth->deny_group('shop_tester', 'create_shop_registers');
        $this->aauth->deny_group('shop_tester', 'edit_shop_registers');

        // Update Profile
        // $this->aauth->deny_group('shop_tester', 'edit_profile');
		// @since 2.8.0
        $this->aauth->deny_group('shop_tester', 'create_shop');
        $this->aauth->deny_group('shop_tester', 'edit_shop');
		$this->aauth->deny_group('shop_tester', 'enter_shop');
        
        //@since 3.0.20
        $this->aauth->deny_group('shop_tester', 'create_item_stock');
        $this->aauth->deny_group('shop_tester', 'edit_item_stock');
        $this->aauth->deny_group('shop_tester', 'delete_item_stock');

        // For Cashier
        // Orders
        $this->aauth->deny_group('shop_cashier', 'create_shop_orders');
        $this->aauth->deny_group('shop_cashier', 'edit_shop_orders');
        $this->aauth->deny_group('shop_cashier', 'delete_shop_orders');

        // Customers
        $this->aauth->deny_group('shop_cashier', 'create_shop_customers');
        $this->aauth->deny_group('shop_cashier', 'delete_shop_customers');
        $this->aauth->deny_group('shop_cashier', 'edit_shop_customers');

        // Customers Groups
        $this->aauth->deny_group('shop_cashier', 'create_shop_customers_groups');
        $this->aauth->deny_group('shop_cashier', 'delete_shop_customers_groups');
        $this->aauth->deny_group('shop_cashier', 'edit_shop_customers_groups');

        // Update Profile
        $this->aauth->deny_group('shop_cashier', 'edit_profile');

        // Delete Custom Groups
        $this->aauth->delete_group('shop_cashier');
        $this->aauth->delete_group('shop_manager');
        $this->aauth->delete_group('shop_tester');

		// Store
		$this->aauth->deny_group('shop_tester', 'enter_shop');
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

        $query    =    $this->db->get( store_prefix() . 'nexo_commandes');

        if ($query->result_array()) {
            return $query->result_array();
        }
        return false;
    }

    /**
     * Get Order with metas
     * @param int order id
     * @return array
    **/

    public function get_order_with_metas( $order_id = null )
    {
        $orders      =   $this->get_order( $order_id );

        foreach( $orders as &$order ) {
            $metas       =   $this->db->where( 'REF_ORDER_ID', $order_id )->get( store_prefix() . 'nexo_commandes_meta' )
            ->result_array();
            
            if( $metas ) {
                foreach( $metas as $meta ) {
                    if( empty( @$order[ 'METAS' ] ) ) {
                        $order[ 'METAS' ]   =   [];
                    }
                    
                    $order[ 'METAS' ][ $meta[ 'KEY' ] ]     =   $meta[ 'VALUE' ];
                }
            }
        }

        return $orders;
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
        ->where( store_prefix() . 'nexo_commandes.ID', $order_id)
        ->select( '*,
        ' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
        ' . store_prefix() . 'nexo_commandes.DATE_MOD as DATE_MOD,
        ' . store_prefix() . 'nexo_commandes.SHIPPING_AMOUNT as SHIPPING_AMOUNT,
        ' . store_prefix() . 'nexo_clients.NOM as customer_name,
        ' . store_prefix() . 'nexo_clients.TEL as customer_phone,
        ' . store_prefix() . 'nexo_commandes.ID as ORDER_ID,
        ' . store_prefix() . 'nexo_commandes.DESCRIPTION as DESCRIPTION,
        ' . store_prefix() . 'nexo_commandes.AUTHOR as AUTHOR,
        aauth_users.name as author_name' )
        ->from( store_prefix() . 'nexo_commandes' )
        ->join( store_prefix() . 'nexo_clients', store_prefix() . 'nexo_commandes.REF_CLIENT = ' . store_prefix() . 'nexo_clients.ID' )
        ->join( 'aauth_users', 'aauth_users.id = ' . store_prefix() . 'nexo_commandes.AUTHOR' )
        ->get();

        if ($query->result_array()) {
            $data            =    $query->result_array();
            // var_dump( $query->result_array() );die;
            $sub_query        =    $this->db
            ->select('*,
            ' . store_prefix() . 'nexo_commandes_produits.ID as ITEM_ID,
			' . store_prefix() . 'nexo_commandes_produits.QUANTITE as QTE_ADDED,
			' . store_prefix() . 'nexo_articles.DESIGN as DESIGN')
            ->from( store_prefix() . 'nexo_commandes')
            ->join( store_prefix() . 'nexo_commandes_produits', store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner')
            ->join( store_prefix() . 'nexo_articles', store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'left')
            ->where( 'REF_COMMAND_CODE', $data[0][ 'CODE' ])
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
        $order_types    =    $this->config->item('nexo_order_types');
        return $order_types[ $order_type ];
    }

    /**
     * Proceed order
     * complete an order
     * @param int order id
     * @return bool
    **/

    public function proceed_order($order_id)
    {
        $order    =    $this->Nexo_Checkout->get_order($order_id);

        if ($order) {
            if ($order[0][ 'TYPE' ] == 'nexo_order_advance') {
                $this->db->where('ID', $order_id)->update( store_prefix() . 'nexo_commandes', array(
                    'SOMME_PERCU'    =>    $order[0][ 'TOTAL' ],
                    'TYPE'            =>    'nexo_order_comptant'
                ));
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

	/**
	 * Check Registers
	 * @since 2.7.5
	 * @param int register int
	 * @return string (open, closed, disabled, not_found)
	**/

	public function register_status( $id )
	{
		$result	=	$this->db->where( 'ID', $id )->get( store_prefix() . 'nexo_registers' )->result_array();

		if( @$result[0] != null ) {
			return $result[0][ 'STATUS' ];
		}
		return 'not_found';
	}

	/**
	 * Get Register
	 * @param int register id
	 * @return array
	**/

	public function get_register( $id )
	{
		return $this->db->where( 'ID', $id )->get( store_prefix() . 'nexo_registers' )->result_array();
	}

	/**
	 * Connect User to a Register
	 * @return void
	**/

	public function connect_user( $register_id, $user_id )
	{
		$this->db->where( 'ID', $register_id )->update( store_prefix() . 'nexo_registers', array(
			'USED_BY'	=>	$user_id
		) );
	}

	/**
	 * Has user logged in
	 * @return bool
	**/

	public function has_user( $register_id )
	{
		$result		=	$this->db->where( 'ID', $register_id )->get( store_prefix() . 'nexo_registers' )->result_array();

		if( $result ) {
			return $result[0][ 'USED_BY' ] == '0' ? false : true;
		}
		return false;
	}

	/**
	 * Disconnect User
	 *
	 * @param int register id
	 * @return void
	**/

	public function disconnect_user( $register_id )
	{
		$result		=	$this->db->where( 'ID', $register_id )->update( store_prefix() . 'nexo_registers', array(
			'USED_BY'		=>		0
		) );
	}

    /**
     *  Get Order with item stock
     *  @param int order id
     *  @return array
    **/

    public function get_order_with_item_stock( $order_id )
    {
        $data           =   $this->db->where( 'ID', $order_id )->get( store_prefix() . 'nexo_commandes' )->result_array();

        $order_code     =   $data[0][ 'CODE' ];

        $this->db->select(
            store_prefix() . 'nexo_articles.DESIGN, ' .
            store_prefix() . 'nexo_commandes_produits.PRIX, ' .
            store_prefix() . 'nexo_commandes.CODE, ' .
            store_prefix() . 'nexo_articles_stock_flow.QUANTITE as QUANTITE, ' .
            store_prefix() . 'nexo_articles_stock_flow.TYPE as TYPE'
        );

        $this->db->from( store_prefix() . 'nexo_articles_stock_flow' );

        $this->db->join(
            store_prefix() . 'nexo_commandes_produits',
            store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE = ' .
            store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE', 'left'
        );

        $this->db->join(
            store_prefix() . 'nexo_articles',
            store_prefix() . 'nexo_articles.CODEBAR = ' .
            store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'left'
        );

        $this->db->join(
            store_prefix() . 'nexo_commandes',
            store_prefix() . 'nexo_commandes.CODE = ' .
            store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE', 'left'
        );

        $this->db->where( store_prefix() . 'nexo_articles_stock_flow.REF_COMMAND_CODE', $order_code );

        return $this->db->get()->result_array();
    }


}
