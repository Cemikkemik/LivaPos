<?php
class NexoRegistersController extends CI_Model
{
    public function crud_header()
    {
        if( User::cannot( 'nexo.view.registers' ) ) {
			return nexo_access_denied();
		}

		/**
		 * This feature is not more accessible on main site when
		 * multistore is enabled
		**/

		if( multistore_enabled() && ! is_multistore() ) {
			redirect( array( 'dashboard', 'feature-disabled' ) );
		}

		$crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__( 'Caisses', 'nexo'));

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_registers'));

		// If Multi store is enabled
		// @since 2.8
		$fields					=	array( 'NAME', 'STATUS', 'DESCRIPTION', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
		$crud->columns('NAME', 'USED_BY', 'STATUS', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD');
        $crud->fields( $fields );

		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		$crud->set_relation('USED_BY', 'aauth_users', 'name');

        $crud->order_by('DATE_CREATION', 'desc');

        $crud->display_as('NAME', __('Caisse', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
		$crud->display_as('IMAGE_URL', __('Aperçu', 'nexo'));
		$crud->display_as('STATUS', __('Etat', 'nexo'));
		$crud->display_as('AUTHOR', __('Auteur', 'nexo'));
		$crud->display_as('DATE_CREATION', __('Crée', 'nexo'));
        $crud->display_as('DATE_MOD', __('Modifié', 'nexo'));
		$crud->display_as('USED_BY', __('Utilisé par', 'nexo'));


        // Liste des produits
        $crud->add_action(
			__('Ouvrir la caisse', 'nexo'),
			'',
			site_url(array( 'dashboard', store_slug(), 'nexo', 'open', 'register' )) . '/',
			'open_register fa fa-unlock'
		);

		$crud->add_action(
			__('Fermer la caisse', 'nexo'),
			'',
			site_url(array( 'dashboard', store_slug(), 'nexo', 'close', 'register' )) . '/',
			'close_register fa fa-lock'
		);

		$crud->add_action(
			__('Utiliser la caisse', 'nexo'),
			'',
			site_url(array( 'dashboard', store_slug(), 'nexo', 'use', 'register' )) . '/',
			'fa fa-sign-in'
		);

		$crud->add_action(
			__('Historique de la caisse', 'nexo'),
			'',
			site_url(array( 'dashboard', store_slug(),  'nexo', 'register-history' )) . '/',
			'register_history fa fa-history'
		);

        $this->events->add_filter( 'grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter( 'grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
		$this->events->add_filter( 'grocery_filter_actions', array( $this, '__register_grocery_filter_action' ), 10 );
		$this->events->add_filter( 'grocery_filter_edit_button', array( $this, '__filter_admin_button' ), 10, 4);
		$this->events->add_filter( 'grocery_filter_delete_button', array( $this, '__filter_delete_register' ), 10, 4);

        $crud->callback_before_insert(array( $this, '__create_register' ));
        $crud->callback_before_update(array( $this, '__edit_register' ));
        $crud->callback_before_delete(array( $this, '__delete_register' ));

		if( in_array( $this->uri->segment( 5 ), array( 'add', 'edit' ) ) ) {
			$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_registers_status_for_creating'));
		} else {
			$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_registers_status'));
		}

        $crud->required_fields('NAME', 'STATUS');
		$crud->change_field_type('DATE_CREATION', 'invisible');
		$crud->change_field_type('DATE_MOD', 'invisible');
		$crud->change_field_type('AUTHOR', 'invisible');

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
	 * __create register
	**/

	public function __create_register( $post )
	{
		nexo_permission_check( 'nexo.create.registers' );

		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_CREATION' ]	=	date_now();
		return $post;
	}

	/**
	 * __edit register
	**/

	public function __edit_register( $post )
	{
		nexo_permission_check( 'nexo.edit.registers' );

		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_MOD' ]			=	date_now();
		return $post;
	}

	/**
	 * __delete register
	**/

	public function __delete_register( $post )
	{
		nexo_permission_check( 'nexo.delete.registers' );

		// fixed @since 3.0.20
		$this->db->where( 'REF_REGISTER', $post )->delete( store_prefix() . 'nexo_registers_activities' );

		return $post;
	}

	// Multi Store not yet supported

	public function lists($page = 'home', $id = null)
	{
		/**
		 * Set Page Now namespace
		**/

		global $PageNow;

		// Footer
		$this->events->add_action( 'dashboard_footer', function(){
			get_instance()->load->module_view( 'nexo', 'registers.dashboard_footer' );
		});

		if( $page == 'add' ) {
			if( User::cannot( 'nexo.create.registers' ) ) {
				return nexo_access_denied();
			}

			$PageNow		=	'nexo/registers/add';

			$this->Gui->set_title( store_title( __('Ajouter une caisse', 'nexo')) );
		} elseif( $page == 'edit' ) {
			// Only for those who can create
			if( ! User::can( 'nexo.edit.registers' ) ) {
				return nexo_access_denied();
			}

			$PageNow		=	'nexo/registers/edit';

			$this->Gui->set_title( store_title( __('Modifier une caisse', 'nexo')) );
		} elseif( $page == 'delete' ) {
			nexo_permission_check( 'nexo.delete.registers' );

			$PageNow		=	'nexo/registers/delete';

            // Checks whether an item is in use before delete
            nexo_availability_check($id, array(
                array( 'col'    =>    'REF_REGISTER', 'table'    =>    store_prefix() . 'nexo_commandes' )
            ));
		} else {

			$PageNow		=	'nexo/registers/list';

			$this->Gui->set_title( store_title( __('Liste des caisses', 'nexo')) );
		}

		$data[ 'crud_content' ]    =    $this->crud_header();

		$this->load->view('../modules/nexo/views/registers', $data);
	}

	/**
	 * Use Register
	**/

	public function __use( $reg_id = 'default', $order_id = null )
	{
		global $Options, $store_id, $PageNow, $register_id;

		$register_id		=	$reg_id;
		$options_prefix		=	$store_id != null ? 'store_' . $store_id . '_' : '';
		$PageNow			=	'nexo/registers/__use';

		/// If current user can open registers
		if( ! User::can( 'nexo.create.orders' ) ){
			return show_error( __( 'Vous n\'êtes pas autorisé à effectuer une vente.', 'nexo' ) );
		}
		
        $this->events->add_filter( 'gui_wrapper_attrs', function( $attrs ) {
            $attrs          =   'ng-controller="posTabs"';
            return $attrs;
        });

		$this->events->add_action( 'dashboard_footer', function(){

			include_once( MODULESPATH . '/nexo/inc/angular/register/include.php' );

			/**
			* @since 3.0.1
			* Coupon Feature
			**/

			get_instance()->load->module_view( 'nexo', 'coupon-script' );
			// get_instance()->load->module_view( 'nexo', 'registers/shortcuts' );

			/**
			 * @since 3.0.19
			 * Header Angular Controller
			**/

			get_instance()->load->module_view( 'nexo', 'checkout.v2-1.header-script' );

			/**
			 * @since 3.1
			 * Load Customer script
			**/

			if( get_option( store_prefix() . 'disable_customer_creation' ) != 'yes' ) {
				get_instance()->load->module_view( 'nexo', 'customers.script', [
					'clients'       =>  [],
					'client_id'     =>  0,
					'groups' 		=>	get_instance()->Nexo_Misc->customers_groups()
				]);
			}			
		});

        $this->events->add_action( 'before_body_content', function(){
			get_instance()->load->module_view( 'nexo', 'registers.loading-spinner' );
		});

        $this->events->add_action( 'angular_paybox_footer', function(){
            get_instance()->load->module_view( 'nexo', 'coupon-footer' );
        });

        $this->events->add_action( 'load_register_content', function(){
            include_once( MODULESPATH . '/nexo/inc/angular/register/directives/coupon-payment.php' );
        });

        /**
         * @since 3.0.13
         * Add coupon filter
        **/

        $this->events->add_action( 'nexo_payments_types', function( $payment_types ) {
            global $Options;

            if( @$Options[ store_prefix() . 'disable_coupon' ] == 'yes' ) {
                foreach( $payment_types as $index => $payment ) {
                    if( $index == 'coupon' ) {
                        unset( $payment_types[ $index ] );
                    }
                }
            }
            
            return $payment_types;
        });

		/**
		 * If Register Option is disabled, then we hide "close register" menu
		 * Order proceeded when register option is disabled will be bound to default register which is "0"
		 * @since 2.7.7
		**/

		if( ! in_array( @$Options[ $options_prefix . 'nexo_enable_registers' ], array( null, 'non' ) ) ){

			$this->events->add_filter( 'checkout_header_menus_2', function( $menus ) {
				$item_id	=	store_prefix() == '' ? get_instance()->uri->segment( 5 ) : get_instance()->uri->segment( 7 );
				$menus[] 	=	[
					'class' =>  'default close_register',
					'text'  =>  __( 'Fermer la caisse', 'nexo' ),
					'icon'  =>  'sign-out',
					'attrs' =>  [
						'data-item-id'  =>  $item_id
					]
				];

				return $menus;
			});

			// register script
			$this->events->add_action( 'dashboard_footer', function() {
				$this->load->module_view( 'nexo', 'registers.script' );
			});

			// Register Status
			$this->load->model( 'Nexo_Checkout' );

			// Does register exists ?
			$status		=	$this->Nexo_Checkout->get_register( $register_id );

			$register_slug	=	'registers';

			switch( @$status[0][ 'STATUS' ] ) {
				case 'not_found' : redirect( array( 'dashboard', 'nexo', $register_slug . '?notice=register_not_found' ) ); break;
				case 'closed' : redirect( array( 'dashboard', 'nexo', $register_slug . '?notice=register_is_closed' ) ); break;
				case 'locked' : redirect( array( 'dashboard', 'nexo', $register_slug . '?notice=register_is_locked' ) ); break;
				case 'opened' : break;
				default : redirect( array( 'dashboard', 'nexo', 'registers?notice=unknow_register_status' ) ); break;
			}

			// Register in use by another user
			if( $status[0][ 'USED_BY' ] != User::id() && $status[0][ 'USED_BY' ] != '0' ) {
				redirect( array( 'dashboard', 'nexo', $register_slug . '?notice=register_busy' ) );
			}

			// Log current user
			$this->Nexo_Checkout->connect_user( $register_id, User::id() );
		}

		/**
		 * @since 3.0.19
		 * Introduce header buttons
		**/

		$this->events->add_filter( 'gui_before_rows', function() {
			return get_instance()->load->module_view( 'nexo', 'checkout.v2-1.header', null, true );
		});

		$data        =    array();
		// Prefetch order
		if ( $order_id != null) {
			$this->load->model('Nexo_Checkout');

			$order        =    $this->events->apply_filters( 
				'loaded_order', 
				$this->Nexo_Checkout->get_order_products($order_id, true) 
			);

			if ($order) {
				if (! User::can('nexo.create.orders' )) {
					return nexo_access_denied();
				}

				if (in_array($order[ 'order' ][0][ 'TYPE' ], $this->events->apply_filters( 'order_type_locked', array( 'nexo_order_comptant', 'nexo_order_advance' )))) {
					redirect( dashboard_url([ 'orders?notice=order_edit_not_allowed' ]) );
				}

				$data[ 'order' ]    =    $order;

			} else {
				redirect( dashboard_url([ 'orders?notice=order_not_found' ]) );
			}
		}

		if (@$Options[ $options_prefix . 'default_compte_client' ] == null && User::can('edit_options')) {
			return redirect( dashboard_url([ 'settings', 'customers?notice=default-customer-required' ]) );
		} elseif (@$Options[ $options_prefix . 'default_compte_client' ] == null) {
			return show_error( __( 'Il est nécessaire d\'avoir un client par défaut. Veuillez reporter à l\'administrateur de fournir un client par défaut.', 'nexo' ) );
		}

		// $data[ 'initial_balance_set' ]		=	$this->Nexo_Misc->get_balance_for_date( date_now() );
		$data[ 'register_id' ]			=		$register_id;

		// Before Cols
		$this->events->add_filter('gui_before_rows', function ($content) {
			return $content . get_instance()->load->module_view('nexo', 'checkout/v2/options', array(), true);
		});

		$this->load->model('Nexo_Checkout');

		$this->enqueue->js('../modules/nexo/bower_components/moment/min/moment.min');
		$this->enqueue->js('../plugins/bootstrap-select/dist/js/bootstrap-select.min');

		$this->enqueue->css('../modules/nexo/css/animate');
 		$this->enqueue->css('../plugins/bootstrap-select/dist/css/bootstrap-select.min');

		if ($order_id == null) {
			$this->Gui->set_title( store_title( __('Effectuer un vente', 'nexo')) );
		} else {
			$this->Gui->set_title( store_title( __('Modifier une commande', 'nexo')) );
		}

		/**
		 * @since 3.11.7
		 * load taxes on POS
		 */
		$data[ 'taxes' ] 		=	get_instance()->Nexo_Misc->get_taxes();

		$this->load->view('../modules/nexo/views/checkout/v2-1/body.php', $data);
	}

	/**
	 * Filter Grocery actions for registers
	**/

	public function __register_grocery_filter_action( $data )
    {
		$grocery_actions_obj 		=	$data[0];
		$actions 					=	$data[1];
		$row 						=	$data[2];
		$register_status			=	$this->config->item( 'nexo_registers_status' );
        // return $grocery_actions_obj;
        foreach ($actions as $key => $action) {
			$url				=	substr( $action->link_url, 0, -1 );
			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'open', 'register' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'opened' ], $register_status[ 'locked' ] ) ) ){
					unset($grocery_actions_obj[ $key ]);
				}
			}

			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'register-history' ) ) ) {
				// Only Master & Shop manager can see register history
				if ( in_array( $row->STATUS, array( $register_status[ 'opened' ] ) ) || ( User::cannot( 'nexo.view.registers-history' ) ) ){
					unset($grocery_actions_obj[ $key ]);
				}
			}

			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'use', 'register' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'closed' ], $register_status[ 'locked' ] ) ) ) {
					unset($grocery_actions_obj[ $key ]);
				}
			}

			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'close', 'register' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'closed' ], $register_status[ 'locked' ] ) ) ) {
					unset($grocery_actions_obj[ $key ]);
				}
			}
        }

        return [ $grocery_actions_obj, $actions, $row ];
    }

	/**
	 * Filter Register Edit Button
	**/

	public function __filter_admin_button($string, $row, $edit_text, $subject)
    {
		if ( ! User::can( 'nexo.edit.registers' ) ) {
			return '';
		}
        return $string;
	}
	
	/**
	 * Filter Register Edit Button
	**/

	public function __filter_delete_register($string, $row, $edit_text, $subject)
    {
		if ( ! User::can( 'nexo.delete.registers' ) ) {
			return '';
		}
        return $string;
    }
}