<?php
class Nexo_Settings_Controller extends CI_Model
{
    public function __construct($args)
    {
         parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->index();
            }
        }
        return $this->index();
    }
	
	public function index()
	{
		$this->lists();
	}
    
    public function crud_header()
    {
        if (
            ! User::can('create_shop_registers')  &&
            ! User::can('edit_shop_registers') &&
            ! User::can('delete_shop_registers') && 
			! User::can( 'view_shop_registers' )
        ) {
            redirect(array( 'dashboard', 'access-denied' ));
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
			site_url(array( 'dashboard', store_slug(), 'nexo', 'registers', 'open' )) . '/', 
			'btn open_register btn-success fa fa-unlock'
		);
		
		$crud->add_action(
			__('Fermer la caisse', 'nexo'),
			'', 
			site_url(array( 'dashboard', store_slug(), 'nexo', 'registers', 'close' )) . '/', 
			'btn close_register btn-warning fa fa-lock'
		);
		
		$crud->add_action(
			__('Utiliser la caisse', 'nexo'), 
			'', 
			site_url(array( 'dashboard', store_slug(), 'nexo', 'registers', '__use' )) . '/', 
			'btn btn-info fa fa-sign-in'
		);
		
		$crud->add_action(
			__('Historique de la caisse', 'nexo'), 
			'', 
			site_url(array( 'dashboard', store_slug(),  'nexo', 'registers', 'history' )) . '/', 
			'register_history btn btn-default fa fa-history'
		);
                
        $this->events->add_filter( 'grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter( 'grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
		$this->events->add_filter( 'grocery_filter_actions', array( $this, '__register_grocery_filter_action' ), 10, 3 );
		$this->events->add_filter( 'grocery_filter_edit_button', array( $this, '__filter_admin_button' ), 10, 4);
		$this->events->add_filter( 'grocery_filter_delete_button', array( $this, '__filter_admin_button' ), 10, 4);

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
		nexo_permission_check( 'create_shop_registers' );
		
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_CREATION' ]	=	date_now();
		return $post;
	}
	
	/**
	 * __edit register
	**/
	
	public function __edit_register( $post ) 
	{
		nexo_permission_check( 'edit_shop_registers' );
		
		$post[ 'AUTHOR' ]			=	User::id();
		$post[ 'DATE_MOD' ]			=	date_now();
		return $post;
	}
	
	/**
	 * __delete register
	**/
	
	public function __delete_register( $post ) 
	{
		nexo_permission_check( 'delete_shop_registers' );
		
		$this->db->where( 'REF_REGISTER', $post )->delete( store_prefix() . 'nexo_registers_activites' );

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
			?>
            <script type="text/javascript">
			"use strict";

			$( document ).ready(function(e) {
                $( '.open_register' ).bind( 'click', function(){
					var $this	=	$( this );
					$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
						success		:	function( data ){
							// Somebody is logged in
							if( data[0].STATUS == 'opened' ) {
								if( data[0].USED_BY != '<?php echo User::id();?>' ) {
									// Display confirm box to logout current user and login
									bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse en cours d\'utilisation. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
								} else {
									bootbox.alert( '<?php echo _s( 'Vous allez être redirigé vers la caisse...', 'nexo' );?>' );	
									// Document Location
								}
							} else if( data[0].STATUS == 'locked' ) {
								bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

							} else if( data[0].STATUS == 'closed' ) {
								var dom		=	'<h3 class="modal-title"><?php echo _s( 'Ouverture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';
					
									dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à ouvrir une caisse. Veuillez spécifier le montant initiale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' + 
												'<div class="input-group">' +
													'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde d\'ouverture de la caisse', 'nexo' );?></span>' +
													'<input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">' +
												'</div>';
								
								bootbox.confirm( dom, function( action ) {
									if( action ) {
										$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'open_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
											dataType	:	'json',
											type		:	'POST',
											data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
											success: function( data ){
												bootbox.alert( '<?php echo _s( 'La caisse a été ouverte. Veuillez patientez...', 'nexo' );?>' );
												document.location	=	'<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', '__use' ) );?>/' + $this.data( 'item-id');
											}
										});
									}
								});
								
								// Set custom width
								$( '.modal-title' ).closest( '.modal-dialog' ).css({
									'width'		:	'80%'
								})
							}
							
						},
						dataType	:	"json",
						error		:	function(){
							bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
						}
					})
					
					return false;
				});
				
				$( '.close_register' ).bind( 'click', function(){
					var $this	=	$( this );
					$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
						success		:	function( data ){
							// Somebody is logged in
							if( data[0].STATUS == 'opened' ) {
								
								if( data[0].USED_BY != '<?php echo User::id();?>'  ) {
									bootbox.alert( '<?php echo _s( 'Vous ne pouvez pas fermer cette caisse. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
									return;
								}
								
								var dom		=	'<h3 class="modal-title"><?php echo _s( 'Fermeture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';
					
									dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à fermer une caisse. Veuillez spécifier le montant finale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' + 
												'<div class="input-group">' +
													'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde de fermeture de la caisse', 'nexo' );?></span>' +
													'<input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">' +
												'</div>';
								
								bootbox.confirm( dom, function( action ) {
									if( action == true ) {
										$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'close_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
											dataType	:	'json',
											type		:	'POST',
											data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
											success: function( data ){
												bootbox.alert( '<?php echo _s( 'La caisse a été fermée. Veuillez patientez...', 'nexo' );?>' );
												document.location	=	'<?php echo current_url();?>';
											}
										});
									}
								});
								
								// Set custom width
								$( '.modal-title' ).closest( '.modal-dialog' ).css({
									'width'		:	'80%'		
								})
								
							} else if( data[0].STATUS == 'locked' ) {
								
								bootbox.alert( '<?php echo _s( 'Impossible de fermer une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

							} else if( data[0].STATUS == 'closed' ) {
								
								bootbox.alert( '<?php echo _s( 'Cette caisse est déjà fermée.', 'nexo' );?>' );
								
							}
							
						},
						dataType	:	"json",
						error		:	function(){
							bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
						}
					})
					
					return false;
				});
				
				$( '.register_history' ).bind( 'click', function(){
					
					$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_activities' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
						success	:	function( data ){
							var dom			=	'<h4><?php echo _s( 'Historique de la caisse', 'nexo' );?></h4>';
							var lignes		=	'';
							
							if( ! _.isEmpty( data ) ) {
								_.each( data, function( val, key ) {
									lignes 	+=	
									'<tr>' +
										'<td>' + val.name + '</td>' + 
										'<td>' + ( val.TYPE == 'opening' ? '<?php echo _s( 'Ouvrir', 'nexo' );?>' : '<?php echo _s( 'Fermer', 'nexo' );?>' ) + '</td>' +
										'<td>' + NexoAPI.DisplayMoney( val.BALANCE ) + '</td>' + 
										'<td>' + val.DATE_CREATION + '</td>' +
									'</tr>';
								});
							} else {
								lignes	+=	'<tr><td colspan="4"><?php echo _s( 'Aucune historique pour cette caisse', 'nexo' );?></td></tr>';	
							}
							
								dom			+=
							'<table class="table table-bordered table-striped">' +
								'<thead>' +
									'<tr>' +
										'<td><?php echo _s( 'Auteur', 'nexo' );?></td>' +
										'<td><?php echo _s( 'Action', 'nexo' );?></td>' +
										'<td><?php echo _s( 'Montant', 'nexo' );?></td>' +
										'<td><?php echo _e( 'Date', 'nexo' );?></td>' +
									'</tr>' + 
								'</thead>' +
								'<tbody>' +
									lignes
								'</tbody>' +
							'</table>';
							
							bootbox.alert( dom, function( action ){
								
							});
							
							// Set custom width
							$( '.modal-title' ).closest( '.modal-dialog' ).css({
								'width'		:	'80%'
							})
						},
						dataType	:	'json',
						
					});
					
					return false;
				});
            });
			</script>
            <?php
		});
		
		if( $page == 'add' ) {
			// Only for those who can create
			if( ! User::can('create_shop_registers') ) {
				redirect( array( 'dashboard', 'access-denied' ) );
			}
			
			$PageNow		=	'nexo/registers/add';
			
			$this->Gui->set_title( store_title( __('Ajouter une caisse', 'nexo')) );
		} elseif( $page == 'edit' ) {
			// Only for those who can create
			if( ! User::can('edit_shop_registers') ) {
				redirect( array( 'dashboard', 'access-denied' ) );
			}
			
			$PageNow		=	'nexo/registers/edit';
			
			$this->Gui->set_title( store_title( __('Modifier une caisse', 'nexo')) );
		} elseif( $page == 'delete' ) {
			nexo_permission_check('delete_shop_registers');
			
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
	
	public function __use( $reg_id, $order_id = null )
	{
		global $Options, $store_id, $PageNow, $register_id;	
		
		$register_id		=	$reg_id;
		$options_prefix		=	$store_id != null ? 'store_' . $store_id . '_' : '';
		$PageNow			=	'nexo/registers/__use';
		
		/// If current user can open registers
		if( ! User::can( 'view_shop_registers' ) && ! User::can('create_shop_orders') ){
			redirect( array( 'dashboard', 'access-denied' ) );
		}

		$this->events->add_action( 'dashboard_header', function(){
			?>
            <script type="text/javascript" src="<?php echo module_url( 'nexo' ) . '/js/jmarquee.js';?>"></script>
			<script src="<?php echo module_url('nexo') . '/bower_components/slick-carousel/slick/slick.js';?>"></script>
			<link rel="stylesheet" href="<?php echo module_url('nexo') . '/bower_components/slick-carousel/slick/slick.css';?>" media="screen" />
			<link rel="stylesheet" href="<?php echo module_url('nexo') . '/bower_components/slick-carousel/slick/slick-theme.css';?>" media="screen" />
			<?php
		});
		
		$this->events->add_action( 'dashboard_footer', function(){
			?>
            <?php include_once( MODULESPATH . '/nexo/inc/angular/register/include.php' );?>
            <div class="nexo-overlay" style="width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 5000; position: absolute; top: 0px; left: 0px;"><i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color: rgb(0, 0, 0); font-size: 50px; position: absolute; top: 50%; left: 50%; margin-top: -25px; margin-left: -25px; width: 44px; height: 50px;"></i></div>
			<?php
		});
		/**
		 * If Register Option is disabled, then we hide "close register" menu
		 * Order proceeded when register option is disabled will be bound to default register which is "0"
		 * @since 2.7.7
		**/
		
		if( ! in_array( @$Options[ $options_prefix . 'nexo_enable_registers' ], array( null, 'non' ) ) ){
		
			$this->events->add_action( 'display_admin_header_menu', function(){
			$item_id	=	store_prefix() == '' ? $this->uri->segment( 5 ) : $this->uri->segment( 7 );
			?>
            <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle close_register" data-item-id="<?php echo $item_id;?>" data-toggle="dropdown" aria-expanded="true">
            	<i class="fa fa-sign-out"></i>
              <?php _e( 'Fermer la caisse', 'nexo' );?>
              <!-- <span class="label label-warning">30</span> -->
            </a>
          </li>
          <script type="text/javascript">
		  "use strict";
		  $( document ).ready(function(e) {
            $( '.close_register' ).bind( 'click', function(){
					var $this	=	$( this );
					$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
						success		:	function( data ){
							// Somebody is logged in
							if( data[0].STATUS == 'opened' ) {
								
								if( data[0].USED_BY != '<?php echo User::id();?>'  ) {
									bootbox.alert( '<?php echo _s( 'Vous ne pouvez pas fermer cette caisse. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
									return;
								}
								
								var dom		=	'<h3 class="modal-title"><?php echo _s( 'Fermeture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';
					
									dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à fermer une caisse. Veuillez spécifier le montant finale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' + 
												'<div class="input-group">' +
													'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde de fermeture de la caisse', 'nexo' );?></span>' +
													'<input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">' +
												'</div>';
								
								bootbox.confirm( dom, function( action ) {
									if( action == true ) {
										$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'close_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
											dataType	:	'json',
											type		:	'POST',
											data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
											success: function( data ){
												bootbox.alert( '<?php echo _s( 'La caisse a été fermée. Veuillez patientez...', 'nexo' );?>' );
												<?php if( User::in_group( 'shop_cashier' ) ):?>
													document.location	=	'<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'for_cashiers?notice=register_has_been_closed' ) );?>';
												<?php else:?>
													document.location	=	'<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers?notice=register_has_been_closed' ) );?>';
												<?php endif;?>
											}
										});
									}
								});
								
								// Set custom width
								$( '.modal-title' ).closest( '.modal-dialog' ).css({
									'width'		:	'80%'		
								})
								
							} else if( data[0].STATUS == 'locked' ) {
								
								bootbox.alert( '<?php echo _s( 'Impossible de fermer une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

							} else if( data[0].STATUS == 'closed' ) {
								
								bootbox.alert( '<?php echo _s( 'Cette caisse est déjà fermée.', 'nexo' );?>' );
								
							}
							
						},
						dataType	:	"json",
						error		:	function(){
							bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
						}
					})
					
					return false;
				});
        }); 
		  </script>
            <?php
		});		
		
			// Register Status
			$this->load->model( 'Nexo_Checkout' );
			
			// Does register exists ?
			$status		=	$this->Nexo_Checkout->get_register( $register_id );
			
			$register_slug	=	User::in_group( 'shop_cashier' ) ? 'registers/for_cashiers' : 'registers' ;
			
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
		
		$data        =    array();
		// Prefetch order
		if ( $order_id != null) {
			$this->load->model('Nexo_Checkout');
			
			$order        =    $this->Nexo_Checkout->get_order_products($order_id, true);
			
			if ($order) {
				
				if (! User::can('edit_shop_orders')) {
					redirect(array( 'dashboard', 'access-denied' ));
				}                    
				
				if (in_array($order[ 'order' ][0][ 'TYPE' ], $this->events->apply_filters( 'order_type_locked', array( 'nexo_order_comptant', 'nexo_order_advance' )))) {
					redirect(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists?notice=order_edit_not_allowed' ));
				}
			
				$data[ 'order' ]    =    $order;
				
			} else {
				redirect(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists?notice=order_not_found' ));
			}
		}
		
		if (@$Options[ $options_prefix . 'default_compte_client' ] == null && User::can('edit_options')) {
			
			redirect(array( 'dashboard', store_slug(), 'nexo', 'settings', 'customers?notice=default-customer-required' ));
			
		} elseif (@$Options[ $options_prefix . 'default_compte_client' ] == null) {

			if( $store_id != null ) {
				redirect(array( 'dashboard', 'stores', $store_id . '?notice=default-customer-required' ));
			} else {
				redirect(array( 'dashboard?notice=default-customer-required' ));
			}
			
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
		
		$this->load->view('../modules/nexo/views/checkout/v2-1/body.php', $data);		
	}
	
	/**
	 * Filter Grocery actions for registers
	**/
	
	public function __register_grocery_filter_action($grocery_actions_obj, $actions, $row)
    {
		$register_status		=	$this->config->item( 'nexo_registers_status' );
        // return $grocery_actions_obj;
        foreach ($actions as $key => $action) {
			$url				=	substr( $action->link_url, 0, -1 );
			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'open' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'opened' ], $register_status[ 'locked' ] ) ) ){
					unset($grocery_actions_obj[ $key ]);
				}
			}
			
			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'history' ) ) ) {
				// Only Master & Shop manager can see register history
				if ( in_array( $row->STATUS, array( $register_status[ 'opened' ] ) ) || ( ! User::in_group( 'master' ) && ! User::in_group( 'shop_manager' ) ) ){
					unset($grocery_actions_obj[ $key ]);
				}
			}
			
			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', '__use' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'closed' ], $register_status[ 'locked' ] ) ) ) {
					unset($grocery_actions_obj[ $key ]);
				}
			}
			
			if( $url == site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'close' ) ) ) {
				if ( in_array( $row->STATUS, array( $register_status[ 'closed' ], $register_status[ 'locked' ] ) ) ) {
					unset($grocery_actions_obj[ $key ]);
				}
			}
        }
		
        return $grocery_actions_obj;
    }
	
	/**
	 * Filter Register Edit Button
	**/
	
	public function __filter_admin_button($string, $row, $edit_text, $subject)
    {
		if ( ! User::can( 'edit_shop_registers' ) ) {
			return '';
		}
        return $string;
    }
	
	/**
	 * For Cashier
	**/
	
	public function for_cashiers()
	{
		$this->Gui->set_title( store_title( __( 'Caisses enregistreuses', 'nexo' ) ) );
		$this->load->module_view( 'nexo', 'registers/for_cashiers' );
	}
}
new Nexo_Settings_Controller($this->args);
