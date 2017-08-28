<?php
class Nexo_Stock_Manager_Controller extends Tendoo_Module
{

    /**
     * Crud Header
     * @return void
    **/

    public function history_crud()
    {
        // if (
        //     ! User::can('edit_transfer_history')    &&
        //     ! User::can('delete_transfer_history')    &&
        //     ! User::can('update_transfer_history') && 
        //     ! User::can('add_transfer_history')
        // ) {
        //     redirect(array( 'dashboard', 'access-denied' ));
        // }
		
		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

        $this->load->module_model( 'stock-manager', 'transfert_model' );
		
        $crud = new grocery_CRUD();
        $crud->set_subject(__('Stock Transfert History', 'nexo'));
        $crud->set_theme('bootstrap');
        // $crud->set_theme( 'bootstrap' );
        $crud->set_table( $this->db->dbprefix( 'nexo_stock_transfert' ) );
		
		// If Multi store is enabled
		// @since 2.8		
		$columns					=	array( 'TITLE', 'APPROUVED', 'APPROUVED_BY', 'FROM_STORE', 'DESTINATION_STORE', 'TYPE', 'AUTHOR', 'DATE_CREATION' );
		
		$crud->columns( $columns );
        // $crud->fields( $fields );
        $crud->where( 'FROM_STORE', get_store_id() );
        $crud->or_where( 'DESTINATION_STORE', get_store_id() );
        
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_edit();
        
        $crud->display_as('TITLE', __('Name', 'stock-manager'));
        $crud->display_as('APPROUVED', __('Status', 'stock-manager'));
        $crud->display_as('APPROUVED_BY', __('Edited by', 'stock-manager'));
        $crud->display_as('FROM_STORE', __('From', 'stock-manager'));
        $crud->display_as('DESTINATION_STORE', __('Send To', 'stock-manager'));
        $crud->display_as( 'TYPE', __('Type', 'stock-manager'));
        $crud->display_as( 'DATE_CREATION', __('Created on', 'stock-manager'));
        $crud->display_as( 'AUTHOR', __('Author', 'stock-manager'));

        $crud->set_relation('AUTHOR', 'aauth_users', 'name');
        $crud->set_relation('APPROUVED_BY', 'aauth_users', 'name');
        $crud->set_relation('FROM_STORE', 'nexo_stores', 'NAME');
        $crud->set_relation('DESTINATION_STORE', 'nexo_stores', 'NAME');

        $crud->add_action(__('Transfert Invoice', 'nexo'), '', site_url(array( 'dashboard', store_slug(), 'stock-transfert', 'history', 'report' )) . '/', 'btn btn-info fa fa-file');
    
        $crud->callback_column( 'APPROUVED', function( $primary, $row ) {
            if( $row->APPROUVED == '0' ) {
                return __( 'Pending', 'stock-manager' );
            } else if( $row->APPROUVED == '1') {
                return __( 'Approuved', 'stock-manager' );
            } else if( $row->APPROUVED == '3' ) {
                return __( 'Refused', 'stock-manager' );
            } else if( $row->APPROUVED == '4' ) {
                return __( 'Canceled', 'stock-manager' );
            }
        });
        
        // XSS Cleaner
        $this->events->add_filter( 'grocery_filter_row', function( $row ) {
            if( $row->sa2ab2bc9 == null ) {
                $row->sa2ab2bc9      =   __( 'N/A', 'stock-manager' );
            }

            if( $row->sa3130f62 == null ) {
                $row->sa3130f62 =   __( 'Main Warehouse', 'stock-manager' );
            }

            if( $row->s837f6f5d == null ) {
                $row->s837f6f5d =   __( 'Main Warehouse', 'stock-manager' );
            }

            return $row;
        });
        
        $this->events->add_filter( 'grocery_filter_actions', function( $data ) {
            $urls           =   $data[0];
            $actions        =   $data[1]; 
            $row            =   $data[2];
            $query          =   $this->transfert_model->get( $row->ID );

            if( intval( $query[0][ 'APPROUVED' ] ) == 0 && intval( $row->DESTINATION_STORE ) == get_store_id() ) { // means pending

                $urls[ 'receive' ]   =   site_url([ 'dashboard', store_slug(), 'stock-transfert', 'receive', $row->ID ]);
                $actions[ 'receive' ]                   =   new stdClass;
                $actions[ 'receive' ]->css_class        =   'btn btn-success fa fa-check';
                $actions[ 'receive' ]->label            =   __( 'Allow the Transfert', 'stock-manager' );
                $actions[ 'receive' ]->text             =   __( 'Approuve', 'stock-manager' );

                $urls[ 'refuse' ]                   =   site_url([ 'dashboard', store_slug(), 'stock-transfert', 'reject', $row->ID ]);
                $actions[ 'refuse' ]                =   new stdClass;
                $actions[ 'refuse' ]->css_class     =   'btn btn-danger fa fa-remove';
                $actions[ 'refuse' ]->label         =   __( 'Refuse', 'stock-manager' );
                $actions[ 'refuse' ]->text             =   __( 'Reject', 'stock-manager' );
            } else if( intval( $query[0][ 'APPROUVED' ] ) == 0 && intval( $row->FROM_STORE ) == get_store_id() ) {
                $urls[ 'void' ]                   =   site_url([ 'dashboard', store_slug(), 'stock-transfert', 'cancel', $row->ID ]);
                $actions[ 'void' ]                =   new stdClass;
                $actions[ 'void' ]->css_class     =   'btn btn-danger fa fa-remove';
                $actions[ 'void' ]->label         =   __( 'Cancel', 'stock-manager' );
                $actions[ 'void' ]->text            =   __( 'Cancel', 'stock-manager' );
            }

            return [ $urls, $actions, $row ];
        }, 10 );
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        // $crud->columns('customerName','phone','addressLine1','creditLimit');

        $crud->unset_jquery();
        $output = $crud->render();
        
        foreach ($output->js_files as $files) {
            $this->enqueue->js(substr($files, 0, -3), '');
        }
        foreach ($output->css_files as $files) {
            $this->enqueue->css(substr($files, 0, -4), '');
        }
        
        return $output;
    }

    /**
     * History Stock Manager
     * @param string 
     * @return void
    **/

    public function history( $page = 'history', $id = 'null' )
    {
        if( $page == 'history' ) {
            $crud       =   $this->history_crud();
            $this->Gui->set_title( store_title( 'Stock Transfer History', 'stock-manager') );
            $this->load->module_view( 'stock-manager', 'transfert.history-gui', compact( 'crud' ) );
        } else if( $page == 'new' ) {
            $this->load->model( 'Nexo_Stores' );
            $this->Gui->set_title( store_title( 'New Stock Transfer', 'stock-manager' ) );
            
            $this->events->add_action( 'dashboard_footer', function(){
                get_instance()->load->module_view( 'stock-manager', 'transfert.script' );
            });

            return $this->load->module_view( 'stock-manager', 'transfert.gui' );
        } else if( $page == 'new-by-csv' ) {
            
        } else if( $page == 'report' ) {
            $this->load->library( 'parser' );
            $this->load->module_model( 'stock-manager', 'transfert_model' );
            $transfert      =   $this->transfert_model->get( $id );
            if( $transfert ) {
                $items          =   $this->transfert_model->get_with_items( $id );
            } else {
                return redirect([ 'dashbaord', 'error', '404']);
            }

            // denied access to unauthorized
            if( ! in_array( get_store_id(), [ $transfert[0][ 'FROM_STORE' ], $transfert[0][ 'DESTINATION_STORE' ] ] ) ) {
                return redirect([ 'dashboard', 'error', 'access-denied' ]);
            }

            $this->Gui->set_title( store_title( __( 'Transfert Invoice' ) ) );
            $this->load->module_view( 'stock-manager', 'transfert.invoice-gui', compact( 'transfert', 'items' ) );
        } else {
            $crud       =   $this->history_crud();
        }
    }

    /**
     * Settings Page for Transfert
     * @return void
    **/

    public function settings()
    {
        $this->Gui->set_title( store_title( __( 'Transfert Settings', 'stock-manager' ) ) );
        $this->load->module_view( 'stock-manager', 'settings.gui' );
    }

    /**
     * Receive Transfert
     * @param int current store
     * @return void
    **/

    public function receive( $transfert_id ) 
    {
        $this->load->module_model( 'stock-manager', 'transfert_model' );
        $transfert   =   $this->transfert_model->get( $transfert_id );

        if( $transfert[0][ 'APPROUVED' ] != '0' ) {
            return show_error( __( 'You cannot approuve this transfert. It may have been canceled or yet approuved.', 'nexo' ) );
        }

        // restrict access to allowed store
        if( intval( $transfert[0][ 'DESTINATION_STORE' ] ) != get_store_id() ) {
            return redirect([ 'dashboard', 'error', 'access-denied' ] );
        }

        $items      =   $this->transfert_model->get_with_items( $transfert_id );
        $allItems   =   $this->db->get( store_prefix() . 'nexo_articles' )
        ->result_array();

        $storeItems     =   [];
        foreach( $allItems as $item ) {
            $storeItems[ $item[ 'CODEBAR' ] ]   =   $item;
        }

        // create shipping on the store receiver
        $this->db->insert( store_prefix() . 'nexo_arrivages', [
            'TITRE'             =>  $transfert[0][ 'TITLE' ],
            'DESCRIPTION'       =>  $transfert[0][ 'DESCRIPTION' ],
            'VALUE'             =>  0,
            'ITEMS'             =>  0,
            'REF_PROVIDERS'     =>  '',
            'DATE_CREATION'     =>  date_now(),
            'AUTHOR'            =>  User::id() 
        ]);
        $delivery_id        =   $this->db->insert_id();

        $failures       =   [];
        foreach( $items as $item ) {
            // check if item exists on the current store otherwise create it
            // Create Slug
            $slug                   =   intval( $transfert[0][ 'FROM_STORE' ] ) == 0 ? '' : 'store_' . intval( $transfert[0][ 'FROM_STORE' ] ) . '_';
            $itemExists             =   false;

            if( @$storeItems[ $item[ 'BARCODE' ] ] == null ) {

                // get item details from orignal store
                $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
                ->get( $slug . 'nexo_articles' )
                ->result_array();

                // if item exist on the original store
                if( $item_details ) {
                    $itemExists                 =   true;
                    $this->db->insert( store_prefix() . 'nexo_articles', [
                        'CODEBAR'               =>  $item[ 'BARCODE' ],
                        'QUANTITE_RESTANTE'     =>  $item[ 'QUANTITY' ],
                        'DESIGN'                =>  $item[ 'DESIGN' ],
                        'REF_RAYON'             =>  $item_details[0][ 'REF_RAYON' ],
                        'REF_CATEGORIE'         =>  $item_details[0][ 'REF_CATEGORIE' ],
                        'SKU'                   =>  $item_details[0][ 'SKU' ],
                        'PRIX_DACHAT'           =>  $item_details[0][ 'PRIX_DACHAT' ],
                        'PRIX_DE_VENTE'         =>  $item_details[0][ 'PRIX_DE_VENTE' ],
                        'PRIX_DE_VENTE_TTC'     =>  $item_details[0][ 'PRIX_DE_VENTE_TTC' ],
                        'SHADOW_PRICE'          =>  $item_details[0][ 'SHADOW_PRICE' ],
                        'TAILLE'                =>  $item_details[0][ 'TAILLE' ],
                        'POIDS'                 =>  $item_details[0][ 'POIDS' ],
                        'DATE_CREATION'         =>  date_now()
                    ]);
                } else {
                    // if item can't be transfered, save it as failures
                    $failures[]     =   $item;
                    break;
                }            
            }

            $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

            // Make a supply entry
            // store which receive item
            $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                'REF_ARTICLE_BARCODE'           =>  $item[ 'BARCODE' ],
                'QUANTITE'                      =>  $item[ 'QUANTITY' ],
                'DATE_CREATION'                 =>  date_now(),
                'AUTHOR'                        =>  User::id(),
                'REF_SHIPPING'                  =>  $delivery_id,
                'TYPE'                          =>  'transfert_in',
                'UNIT_PRICE'                    =>  $item[ 'UNIT_PRICE' ],
                'TOTAL_PRICE'                   =>  $item[ 'TOTAL_PRICE' ],
            ]);

            if( ! $itemExists ) {
                $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )->update( store_prefix() . 'nexo_articles', [
                    'QUANTITE_RESTANTE'             => floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $item[ 'QUANTITY' ] )
                ]);
            }      

            // if the from store allow deduction only when stock is approuved.
            if( intval( $transfert[0][ 'FROM_STORE'] ) == 0 ) {
                if( get_option( 'deduct_from_store', 'yes' ) == 'no' ) {
                    $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
                    ->get( 'nexo_articles' )
                    ->result_array();
                    // reduce from quantity
                    $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )->update( 'nexo_articles', [
                        'QUANTITE_RESTANTE'     =>      floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $item[ 'QUANTITY' ] )
                    ]);

                    // Input in stock flow as transfer
                    $this->db->insert( 'nexo_articles_stock_flow', [
                        'QUANTITE'              =>      floatval( $item[ 'QUANTITY' ] ),
                        'TYPE'                  =>      'transfert_out',
                        'UNIT_PRICE'            =>      $item[ 'UNIT_PRICE' ],
                        'TOTAL_PRICE'           =>      floatval( $item[ 'QUANTITY' ] ) * floatval( $item[ 'UNIT_PRICE' ] ),
                        'REF_ARTICLE_BARCODE'   =>      $item[ 'BARCODE' ],
                        'DATE_CREATION'         =>      date_now(),
                        'AUTHOR'                =>      User::id()
                    ]);
                }
            } else {
                if( get_option( 'store_' . $transfert[0][ 'FROM_STORE' ] . '_deduct_from_store', 'yes' ) == 'no' ) {
                    $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
                    ->get( 'store_' . $transfert[0][ 'FROM_STORE' ] . '_nexo_articles' )
                    ->result_array();
                    // reduce from quantity
                    $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )->update( 'store_' . $transfert[0][ 'FROM_STORE' ] . '_nexo_articles', [
                        'QUANTITE_RESTANTE'     =>      floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) - floatval( $item[ 'QUANTITY' ] )
                    ]);

                    // Input in stock flow as transfer
                    $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                        'QUANTITE'              =>      floatval( $item[ 'QUANTITY' ] ),
                        'TYPE'                  =>      'transfert_out',
                        'UNIT_PRICE'            =>      $item[ 'UNIT_PRICE' ],
                        'TOTAL_PRICE'           =>      floatval( $item[ 'QUANTITY' ] ) * floatval( $item[ 'UNIT_PRICE' ] ),
                        'REF_ARTICLE_BARCODE'   =>      $item[ 'BARCODE' ],
                        'DATE_CREATION'         =>      date_now(),
                        'AUTHOR'                =>      User::id()
                    ]);
                }
            } 
        }

        // update transfert status
        $this->transfert_model->status( $transfert_id, 1 );

        // redirect with errors
        return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=done&errors=' . count( $failures )]);
    }

    /**
     * Decline Stock Transfert
     * @param
    **/

    public function cancel( $transfert_id ) 
    {
        $this->load->module_model( 'stock-manager', 'transfert_model' );
        $transfert   =   $this->transfert_model->get( $transfert_id );

        if( $transfert[0][ 'APPROUVED' ] != '0' ) {
            return show_error( __( 'You cannot cancel this transfert, it may have yet been approuved.', 'nexo' ) );
        }

        // restrict access to FROM store
        if( intval( $transfert[0][ 'FROM_STORE' ] ) != get_store_id() ) {
            return redirect([ 'dashboard', 'error', 'access-denied' ] );
        }

        // if the stock hasn't been send, then we don't need to return anything, just change the transfert status.
        if( store_option( 'deduct_from_store', 'yes' ) == 'no' ) {
            // update transfert status
            $this->transfert_model->status( $transfert_id, 4 );

            // redirect with errors
            return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=cancel_done&errors=' . count( $failures )]);
        }

        $items      =   $this->transfert_model->get_with_items( $transfert_id );
        $allItems   =   $this->db->get( store_prefix() . 'nexo_articles' )
        ->result_array();

        $storeItems     =   [];
        foreach( $allItems as $item ) {
            $storeItems[ $item[ 'CODEBAR' ] ]   =   $item;
        }

        $this->db->insert( store_prefix() . 'nexo_arrivages', [
            'TITRE'             =>  sprintf( __( 'Canceling Transfert : %s', 'stock-transfer' ), $transfert[0][ 'TITLE' ] ),
            'DESCRIPTION'       =>  $transfert[0][ 'DESCRIPTION' ],
            'VALUE'             =>  0,
            'ITEMS'             =>  0,
            'REF_PROVIDERS'     =>  '',
            'DATE_CREATION'     =>  date_now(),
            'AUTHOR'            =>  User::id() 
        ]);

        $delivery_id        =   $this->db->insert_id();
        $supply_value       =   0;
        $supply_quantity    =   0;

        $failures       =   [];
        foreach( $items as $item ) {
            // check if item exists on the current store otherwise create it
            // Create Slug
            $slug                   =   intval( $transfert[0][ 'FROM_STORE' ] ) == 0 ? '' : 'store_' . get_store_id() . '_';
            $itemExists             =   false;

            if( @$storeItems[ $item[ 'BARCODE' ] ] == null ) {

                // get item details from orignal store
                $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
                ->get( $slug . 'nexo_articles' )
                ->result_array();

                // if item exist on the original store
                if( $item_details ) {
                    $itemExists                 =   true;
                    $this->db->insert( store_prefix() . 'nexo_articles', [
                        'CODEBAR'               =>  $item[ 'BARCODE' ],
                        'QUANTITE_RESTANTE'     =>  $item[ 'QUANTITY' ],
                        'DESIGN'                =>  $item[ 'DESIGN' ],
                        'REF_RAYON'             =>  $item_details[0][ 'REF_RAYON' ],
                        'REF_CATEGORIE'         =>  $item_details[0][ 'REF_CATEGORIE' ],
                        'SKU'                   =>  $item_details[0][ 'SKU' ],
                        'PRIX_DACHAT'           =>  $item_details[0][ 'PRIX_DACHAT' ],
                        'PRIX_DE_VENTE'         =>  $item_details[0][ 'PRIX_DE_VENTE' ],
                        'PRIX_DE_VENTE_TTC'     =>  $item_details[0][ 'PRIX_DE_VENTE_TTC' ],
                        'SHADOW_PRICE'          =>  $item_details[0][ 'SHADOW_PRICE' ],
                        'TAILLE'                =>  $item_details[0][ 'TAILLE' ],
                        'POIDS'                 =>  $item_details[0][ 'POIDS' ],
                        'DATE_CREATION'         =>  date_now()
                    ]);
                } else {
                    // if item can't be transfered, save it as failures
                    $failures[]     =   $item;
                    break;
                }            
            }            

            $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
            ->get( store_prefix() . 'nexo_articles' )
            ->result_array();

            // Make a supply entry
            // store which receive item
            $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                'REF_ARTICLE_BARCODE'           =>  $item[ 'BARCODE' ],
                'QUANTITE'                      =>  $item[ 'QUANTITY' ],
                'DATE_CREATION'                 =>  date_now(),
                'AUTHOR'                        =>  User::id(),
                'REF_SHIPPING'                  =>  $delivery_id,
                'TYPE'                          =>  'transfert_canceled',
                'UNIT_PRICE'                    =>  $item[ 'UNIT_PRICE' ],
                'TOTAL_PRICE'                   =>  $item[ 'TOTAL_PRICE' ],
            ]);

            $supply_value                   +=  floatval( $item[ 'TOTAL_PRICE' ] );
            $supply_quantity                +=  floatval( $item[ 'QUANTITY' ] );

            if( ! $itemExists ) {
                $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )->update( store_prefix() . 'nexo_articles', [
                    'QUANTITE_RESTANTE'             => floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $item[ 'QUANTITY' ] )
                ]);
            }                
        }

        // update delivery item quantity
        $this->db->where( 'ID', $delivery_id )->update( store_prefix() . 'nexo_arrivages', [
            'VALUE'         =>  $supply_value,
            'ITEMS'         =>  $supply_quantity
        ]);

        // update transfert status
        $this->transfert_model->status( $transfert_id, 4 );

        // redirect with errors
        return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=cancel_done&errors=' . count( $failures )]);
    }

    /** 
     * Reject Transfert
     *
    **/

    public function reject( $transfert_id ) 
    {
        $this->load->module_model( 'stock-manager', 'transfert_model' );
        $transfert   =   $this->transfert_model->get( $transfert_id );

        $from_prefix         =   $transfert[0][ 'FROM_STORE' ] == '0' ? '' : 'store_' . $transfert[0][ 'FROM_STORE' ] . '_';

        if( $transfert[0][ 'APPROUVED' ] != '0' ) {
            return show_error( __( 'You cannot reject this transfert, it may have yet been approuved or canceled.', 'nexo' ) );
        }

        // restrict access to FROM store
        if( intval( $transfert[0][ 'DESTINATION_STORE' ] ) != get_store_id() ) {
            return redirect([ 'dashboard', 'error', 'access-denied' ] );
        }

        // if the stock hasn't been send, then we don't need to return anything, just change the transfert status.
        if( $transfert[0][ 'FROM_STORE'] == '0' ) {
            if( get_option( 'deduct_from_store', 'yes' ) == 'no' ) {
                // update transfert status
                $this->transfert_model->status( $transfert[0][ 'ID' ], 3 );

                // redirect with errors
                return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=cancel_done&errors=' . count( $failures )]);
            }
        } else {
            if( get_option( 'store_' . $transfert[0][ 'FROM_STORE' ] . '_deduct_from_store', 'yes' ) == 'no' ) {
                // update transfert status
                $this->transfert_model->status( $transfert[0][ 'ID' ], 3 );

                // redirect with errors
                return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=cancel_done&errors=' . count( $failures )]);
            }
        }

        $items      =   $this->transfert_model->get_with_items( $transfert_id );
        $allItems   =   $this->db->get( $from_prefix . 'nexo_articles' )
        ->result_array();

        $storeItems     =   [];
        foreach( $allItems as $item ) {
            $storeItems[ $item[ 'CODEBAR' ] ]   =   $item;
        }

        $this->db->insert( $from_prefix . 'nexo_arrivages', [
            'TITRE'             =>  sprintf( __( 'Rejecting Transfert : %s', 'stock-transfer' ), $transfert[0][ 'TITLE' ] ),
            'DESCRIPTION'       =>  $transfert[0][ 'DESCRIPTION' ],
            'VALUE'             =>  0,
            'ITEMS'             =>  0,
            'REF_PROVIDERS'     =>  '',
            'DATE_CREATION'     =>  date_now(),
            'AUTHOR'            =>  User::id() 
        ]);

        $delivery_id        =   $this->db->insert_id();
        $supply_value       =   0;
        $supply_quantity    =   0;

        $failures       =   [];
        foreach( $items as $item ) {
            // check if item exists on the current store otherwise create it
            // Create Slug
            $slug                   =   intval( $transfert[0][ 'FROM_STORE' ] ) == 0 ? '' : 'store_' . get_store_id() . '_';
            $itemExists             =   false;

            if( @$storeItems[ $item[ 'BARCODE' ] ] == null ) {

                // get item details from orignal store
                $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
                ->get( $slug . 'nexo_articles' )
                ->result_array();

                // if item exist on the original store
                if( $item_details ) {
                    $itemExists                 =   true;
                    $this->db->insert( $from_prefix . 'nexo_articles', [
                        'CODEBAR'               =>  $item[ 'BARCODE' ],
                        'QUANTITE_RESTANTE'     =>  $item[ 'QUANTITY' ],
                        'DESIGN'                =>  $item[ 'DESIGN' ],
                        'REF_RAYON'             =>  $item_details[0][ 'REF_RAYON' ],
                        'REF_CATEGORIE'         =>  $item_details[0][ 'REF_CATEGORIE' ],
                        'SKU'                   =>  $item_details[0][ 'SKU' ],
                        'PRIX_DACHAT'           =>  $item_details[0][ 'PRIX_DACHAT' ],
                        'PRIX_DE_VENTE'         =>  $item_details[0][ 'PRIX_DE_VENTE' ],
                        'PRIX_DE_VENTE_TTC'     =>  $item_details[0][ 'PRIX_DE_VENTE_TTC' ],
                        'SHADOW_PRICE'          =>  $item_details[0][ 'SHADOW_PRICE' ],
                        'TAILLE'                =>  $item_details[0][ 'TAILLE' ],
                        'POIDS'                 =>  $item_details[0][ 'POIDS' ],
                        'DATE_CREATION'         =>  date_now()
                    ]);
                } else {
                    // if item can't be transfered, save it as failures
                    $failures[]     =   $item;
                    break;
                }            
            }            

            $item_details           =   $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )
            ->get( $from_prefix . 'nexo_articles' )
            ->result_array();

            // Make a supply entry
            // store which receive item
            $this->db->insert( $from_prefix . 'nexo_articles_stock_flow', [
                'REF_ARTICLE_BARCODE'           =>  $item[ 'BARCODE' ],
                'QUANTITE'                      =>  $item[ 'QUANTITY' ],
                'DATE_CREATION'                 =>  date_now(),
                'AUTHOR'                        =>  User::id(),
                'REF_SHIPPING'                  =>  $delivery_id,
                'TYPE'                          =>  'transfert_rejected',
                'UNIT_PRICE'                    =>  $item[ 'UNIT_PRICE' ],
                'TOTAL_PRICE'                   =>  $item[ 'TOTAL_PRICE' ],
            ]);

            $supply_value                   +=  floatval( $item[ 'TOTAL_PRICE' ] );
            $supply_quantity                +=  floatval( $item[ 'QUANTITY' ] );

            if( ! $itemExists ) {
                $this->db->where( 'CODEBAR', $item[ 'BARCODE' ] )->update( $from_prefix . 'nexo_articles', [
                    'QUANTITE_RESTANTE'             => floatval( $item_details[0][ 'QUANTITE_RESTANTE' ] ) + floatval( $item[ 'QUANTITY' ] )
                ]);
            }                
        }

        // update delivery item quantity
        $this->db->where( 'ID', $delivery_id )->update( $from_prefix . 'nexo_arrivages', [
            'VALUE'         =>  $supply_value,
            'ITEMS'         =>  $supply_quantity
        ]);

        // update transfert status
        $this->transfert_model->status( $transfert_id, 3 ); // mean the transferst has been rejected

        // redirect with errors
        return redirect([ 'dashboard', store_slug(), 'stock-transfert', 'history?notice=cancel_done&errors=' . count( $failures )]);
    }
}