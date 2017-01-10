<?php
class Nexo_Commandes extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->defaults();
            }
        }
        return $this->defaults();
    }

    public function crud_header()
    {
        if (
            ! User::can('edit_shop_orders')    &&
            ! User::can('create_shop_orders')    &&
            ! User::can('delete_shop_orders')
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

        global $Options;

        $this->load->model('Nexo_Checkout');
        $this->load->model('Nexo_Misc');
        $this->load->config('nexo');
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('Vente', 'nexo'));

        $crud->set_table($this->db->dbprefix( store_prefix() . 'nexo_commandes'));

		/**
		 * Hide register Cols when register option is disabled
		 * @since 2.7.7
		**/

		$cols       	=    array( 'CODE', 'REF_REGISTER', 'REF_CLIENT', 'TOTAL', 'PAYMENT_TYPE', 'TYPE', 'DATE_CREATION', 'AUTHOR' );
		$edit_link		=	site_url(array( 'rest', 'nexo', 'registers' )) . '/';
		$edit_class		=	'select_register';

		if( in_array( @$Options[ store_prefix() .'nexo_enable_registers' ], array( null, 'non' ) ) ){
	        unset( $cols[ 1 ] ); // remove "REF_REGISTER"
			$edit_link		=	site_url( array( 'dashboard',  store_slug(), 'nexo', 'registers', '__use', 'default' ) ) . '/';
			$edit_class		=	'';
		}

        // add filter for table columns
        $cols           =   $this->events->apply_filters( 'nexo_commandes_columns', $cols );

        if (@$Options[ store_prefix() .'nexo_enable_vat' ] == 'oui') {
            array_splice($cols, 5, 0, 'TVA');
        }

        $crud->order_by( 'ID', 'desc' );

		$crud->unset_edit();
        $crud->columns($cols);

        // Add custom Actions
        $crud->add_action(
			__('Imprimer le ticket de caisse', 'nexo'),
			'',
			site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'order_receipt' )) .
			'/',
			'btn btn-info fa fa-file'
		);

		$crud->add_action(
			__('Modifier la commande', 'nexo'),
			'',
			$edit_link,
			'btn btn-default fa fa-edit ' . $edit_class
		);

        $crud->display_as('CODE', __('Code', 'nexo'));
        $crud->display_as('REF_CLIENT', __('Client', 'nexo'));
        $crud->display_as('REMISE', __('Remise Expresse', 'nexo'));
        $crud->display_as('SOMME_PERCU', __('Somme perçu', 'nexo'));
        $crud->display_as('AUTHOR', __('Par', 'nexo'));
        $crud->display_as('PAYMENT_TYPE', __('Paiement', 'nexo'));
        $crud->display_as('TYPE', __('Statut', 'nexo'));
        $crud->display_as('TVA', __('TVA', 'nexo'));
        $crud->display_as('DATE_CREATION', __('Date', 'nexo'));
        $crud->display_as('DATE_MOD', __('Date de modification', 'nexo'));
        $crud->display_as('TOTAL', __('Total', 'nexo'));
		$crud->display_as( 'REF_REGISTER', __( 'Caisse', 'nexo' ) );

		// $crud->order_by('DATE_CREATION', 'desc');

        $crud->field_type('TYPE', 'dropdown', $this->config->item('nexo_order_types'));
        $crud->field_type('PAYMENT_TYPE', 'dropdown', $this->config->item('nexo_all_payment_types'));

		$crud->set_relation('REF_CLIENT', store_prefix() . 'nexo_clients', 'NOM');
		$crud->set_relation('REF_REGISTER', store_prefix() . 'nexo_registers', 'NAME');
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');

        $crud->change_field_type('RABAIS', 'invisible');
        $crud->change_field_type('RISTOURNE', 'invisible');
        $crud->change_field_type('CODE', 'invisible');
        $crud->change_field_type('TOTAL', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DISCOUNT_TYPE', 'invisible');
        $crud->change_field_type('TVA', 'invisible');

		$crud->unset_add();


        // XSS Cleaner
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        // Filter Class
        $this->events->add_filter('grocery_crud_list_item_class', array( $this, 'filter_grocery_list_item_class' ), 10, 2);
        $this->events->add_filter('grocery_filter_edit_button', array( $this, 'filter_edit_button' ), 10, 4);
        $this->events->add_filter('grocery_filter_actions', array( $this, 'filter_grocery_actions' ), 10, 3);
		$this->events->add_filter('gui_wrapper_attrs', function( $content ){
			return $content	.	'ng-controller="nexo_order_list"';
		}, 10 );

        // Run special commande on current crud object
        $crud   =   $this->events->apply_filters( 'nexo_commandes_loaded', $crud );

		$this->events->add_filter( 'grocery_row_actions_output', function( $filter, $row ) {
			return $filter . '<span class="btn btn-primary btn-sm" ng-click="openDetails( ' . $row->ID . ', \'' . $row->CODE . '\'  )">' . __( 'Options', 'nexo' ) . '</span>';
		}, 10, 2 );


        // $crud->required_fields( 'PAYMENT_TYPE', 'SOMME_PERCU' );
        // $crud->callback_before_insert(array( $this->Nexo_Checkout, 'commandes_save' ));
        // $crud->callback_before_update(array( $this->Nexo_Checkout, 'commandes_update' ));
        $crud->callback_before_delete(array( $this->Nexo_Checkout, 'commandes_delete' ));

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

    public function lists($page = 'home', $id = null)
    {
        global $NexoEditScreen, $NexoAddScreen, $Options, $PageNow;

        $NexoEditScreen    	= 	( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/edit#', uri_string());
        $NexoAddScreen    	= 	( bool ) preg_match('#dashboard\/nexo/commandes\/lists\/add#', uri_string());
		$PageNow			=	'nexo/commandes/list';

        $this->events->add_action('dashboard_header', function () use ($NexoAddScreen, $NexoEditScreen) {
            /**
             * We Want to make sure that nothing appear before checkout load
            **/
            if ($NexoAddScreen || $NexoEditScreen) {
                ?>
            <style type="text/css">
			#meta-produits, .content-wrapper .content, .content-header {
				display:none;
			}
			</style>
			<?php

            }
        });

		$this->events->add_action( 'dashboard_footer', array( $this, 'footer' ) );

        if ($page == 'delete') {

            nexo_permission_check('delete_shop_orders');

            $data[ 'crud_content' ]    	=    $this->crud_header();
            $_var1                    	=    'commandes';

            $this->Gui->set_title( store_title( __('Modifier une commande', 'nexo') ) );
            $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
        } else {

            // Change add url
            $this->events->add_filter('grocery_add_url', function ($url) {
                return site_url(array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout' ));
            });

            $data[ 'crud_content' ]    	=    $this->crud_header();
            $_var1    					=    'commandes';

            $this->Gui->set_title( store_title( __('Liste des commandes', 'nexo') ) );
            $this->load->view('../modules/nexo/views/' . $_var1 . '-list.php', $data);
        }
    }

    public function defaults()
    {
        $this->lists();
    }

    /**
     * Proceed order
     * @param int order int
     * @return void
    **/

    public function proceed($order_id)
    {
        $this->load->model('Nexo_Checkout');

        if ($this->Nexo_Checkout->proceed_order($order_id)) {
            redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=order_proceeded' ));
        }

        redirect(array( 'dashboard', 'nexo', 'commandes', 'lists?notice=advanced_required' ));
    }

    /**
     * filter_grocery_list_item_class
     *
     * @param string
     * @param object Row Item
     * @return string
    **/

    public function filter_grocery_list_item_class($class, $row)
    {
        $Advance        =    'nexo_order_advance';
        $Cash            =   'nexo_order_comptant';
        $Estimate        =   'nexo_order_devis';

        $nexo_order_types    =    array_flip($this->config->item('nexo_order_types'));

        if (@$nexo_order_types[ $row->TYPE ]    == $Advance) {
            return 'info';
        } elseif (@$nexo_order_types[ $row->TYPE ] == $Cash) {
            return 'success';
        } elseif (@$nexo_order_types[ $row->TYPE ] == $Estimate) {
            return 'warning';
        } else {
			//@since 2.7.1
			// Let custom class for unknow order type
            return $this->events->apply_filters_ref_array( 'order_list_class', array( $class, $row ) );
        }
    }

    /**
     * Filter Edit button
     * Hide edit button for cash orders
    **/

    public function filter_edit_button($string, $row, $edit_text, $subject)
    {
        $Advance        =    'nexo_order_advance';
        $Cash            =   'nexo_order_comptant';
        $Estimate        =   'nexo_order_devis';

        $nexo_order_types    =    array_flip($this->config->item('nexo_order_types'));

        if (in_array( @$nexo_order_types[ $row->TYPE ], $this->events->apply_filters( 'order_type_locked', array( $Cash ) ) ) ) {
            return;
        } elseif (in_array(@$nexo_order_types[ $row->TYPE ], $this->events->apply_filters( 'order_editable', array( $Estimate ) ) ) ) {
            ob_start();
            ?>
            <a href='<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists', 'v2_checkout', $row->ID ));
            ?>' title='<?php echo $edit_text?> <?php echo $subject?>'>
                <span class='edit-icon fa fa-edit btn-default btn'></span>
            </a>
            <?php
            return ob_get_clean();
        } elseif ( in_array( @$nexo_order_types[ $row->TYPE ], $this->events->apply_filters( 'order_only_payable', array( $Advance ) ) ) ) {
            ob_start();
            ?>
            <a href='<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'proceed', $row->ID ));
            ?>' title='<?php _e('Payer une commande', 'nexo');
            ?>'>
                <span class='edit-icon fa fa-money btn-success btn'></span>
            </a>
            <?php
            return ob_get_clean();
        }

        return $string;
    }

    /**
     * Filter Grocery Actions
     * Allow printing only on Complete orders
     * @param Array grocery actions
     * @return Array
    **/

    public function filter_grocery_actions($grocery_actions_obj, $actions, $row)
    {
        // return $grocery_actions_obj;
        foreach ($actions as $key => $action) {
            $order_type        =    array_flip($this->config->item('nexo_order_types'));

            if ($order_type[ $row->TYPE ] != 'nexo_order_comptant' && $action->css_class == 'btn btn-info fa fa-file') {
                unset($grocery_actions_obj[ $key ]);
            }
			// Hide edit for complete order
			if ($order_type[ $row->TYPE ] == 'nexo_order_comptant' && trim( $action->css_class ) == 'btn btn-default fa fa-edit' ) {
                unset($grocery_actions_obj[ $key ]);
            }
        }

        return $grocery_actions_obj;
    }

	/**
	 * Footer
	 * @since 2.7.1
	**/

	public function footer()
	{
		$this->load->config('rest');
		global $Options;
		if( ( $this->uri->segment( 4 ) == 'lists' && $this->uri->segment( 4 ) != '__use' ) || ( $this->uri->segment( 6 ) == 'lists' && $this->uri->segment( 6 ) != '__use' ) ) {
?>
<script type="text/javascript">
"use strict";
var BindAction		=	function(){
	 $( '.select_register' ).each(function(index, element) {
		if( typeof $(this).attr( 'bound' ) == 'undefined' ) {
			$( this ).bind( 'click', function(){
				$this	=	$( this );
				$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'registers?store_id=' . get_store_id() ) );?>', {
					success	:	function( data ){

						var register_lists	=	'';

						console.log( data );

						_.each( data, function( value, key ) {
							if( value.STATUS == 'opened' ) {
								register_lists	+=	'<tr>' +
									'<td>' + value.NAME + '</td>' +
									'<td><a class="btn btn-primary btn-sm" href="<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', '__use' ) );?>/' + value.ID + '/' + $this.data( 'item-id' ) + '"><?php echo _s( 'Utiliser cette caisse', 'nexo' );?></a></td>' +
								'</tr>';
							}
						});

						var dom		=	'<h4><?php echo _s( 'Selectionner une caisse', 'nexo' );?></h4>' +
						'<br>' +
						'<table class="table table-bordered table-striped">' +
							'<thead>' +
								'<tr>' +
									'<td><?php echo _s( 'Caisse', 'nexo' );?></td>' +
									'<td width="200"><?php echo _s( 'Action', 'nexo' );?></td>' +
								'</tr>' +
							'</thead>' +
							'<tbody>' +
								register_lists +
							'</tbody>' +
						'</table>' +
						'<br>' +
						'<?php echo tendoo_info( _s( 'Les caisses affichées sont celles actuellement ouvertes. Assurez-vous de choisir une caisse ayant une de vos sessions', 'nexo' ) );?>';

						NexoAPI.Bootbox().alert( dom, function( action ) {

						});
					},
					dataType:"json",
					error: function(){
						bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement des caisses.', 'nexo' );?>' );
					}
				});
				return false;
			})
			$(this).attr( 'bound', 'true' );
		}
	});
}
$(document).ready(function(e) {
   BindAction();
});
$( document ).ajaxComplete(function(){
	BindAction();
});
</script>
<?php if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):?>
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
	'use strict';
	// Close Checkout on page navigation:
	$(window).on('popstate', function() {
		// alert( 'POP' );
		//get your angular element
		  var elem = angular.element(document.querySelector('[ng-controller="nexo_order_list"]'));

		  //get the injector.
		  var injector = elem.injector();

		  //get the service.
		  // var __stripeCheckout = injector.get( '__stripeCheckout' );

		  //update the service.
		  // __stripeCheckout.handler.close();

		  // elem.scope().$apply();
	});
</script>
<?php endif;?>

<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/include.php' );?>
<?php
		}
	}

}
new Nexo_Commandes($this->args);
