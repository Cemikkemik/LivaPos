<?php
include_once( dirname( __FILE__ ) . '/controllers/tables.php' );
include_once( dirname( __FILE__ ) . '/controllers/kitchens.php' );
include_once( dirname( __FILE__ ) . '/controllers/areas.php' );

use	Dompdf\Dompdf;
use Carbon\Carbon;

class Nexo_Restaurant_Actions extends CI_Model
{
	public function __construct()
	{
		$this->Areas_Controller		=	new Nexo_Restaurant_Areas_Controllers;
		$this->Kitchens_Controller	=	new Nexo_Restaurant_Kitchens_Controllers;
		$this->Tables_Controller	=	new Nexo_Restaurant_Tables_Controllers;
		
		// Load Dashboard
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
		
		// Admin Menu
		$this->events->add_action( 'display_admin_header_menu', array( $this, 'dash_menu' ) );
		
		// While creating Store
		$this->events->add_action( 'nexo_after_install_tables', array( $this, 'install_store_tables' ) );
		
		// After delete Store
		$this->events->add_action( 'nexo_after_delete_tables', array( $this, 'delete_store_tables' ) );

		// Load Dashboard Header
		$this->events->add_action( 'dashboard_header', function(){
			?>
            <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
            <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-animate.js"></script>
            <script src="<?php echo module_url( 'nexo_restaurant' ) . '/bower_components/gridstack/dist/gridstack.min.js';?>"></script>
            <link rel="stylesheet" href="<?php echo module_url( 'nexo_restaurant' ) . '/bower_components/gridstack/dist/gridstack.min.css';?>"/>
            <?php if( User::in_group( 'shop_cashier' ) ):?>
            <style type="text/css">
			.sidebar-menu >li {
				position: relative;
				margin: 0;
				padding: 0;
				line-height: 45px;
				font-size: 20px;
			}
			.sidebar-menu .treeview-menu>li>a {
				padding: 5px 5px 5px 15px;
				display: block;
				font-size: 20px;
			}
			.sidebar-menu > li > a > .fa, .sidebar-menu > li > a > .glyphicon, .sidebar-menu > li > a > .ion {
				width: 20px;
				margin-right:5px;
			}
			.sidebar-menu > li > a {
				padding: 12px 5px 12px 12px;
				display: block;
			}
			</style>
            <?php
			endif;
		});
		
		//Dashboard Home
		$this->events->add_action( 'load_dashboard_home', array( $this, 'dashboard_home' ), 15 );
		
		// Dashboard Footer
		$this->events->add_action( 'dashboard_footer', array( $this, 'footer' ) );
	}
	
	/**
	 * Install Store Table
	**/
	
	public function install_store_tables( $table_prefix )
	{
		$Install	=	new Nexo_Restaurant_Install;
		$Install->sql_install_queries( $table_prefix );
	}
	
	/** 
	 * Delete Store Tables
	**/
	
	public function delete_store_tables( $table_prefix ) 
	{
		$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix . $table_prefix .'nexo_restaurant_orders_meta`;');
		$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix . $table_prefix .'nexo_restaurant_tables`;');
		$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix . $table_prefix .'nexo_restaurant_tables_groups`;');
		$this->db->query('DROP TABLE IF EXISTS `'.$this->db->dbprefix . $table_prefix .'nexo_restaurant_kitchens`;');
	}
	
	/** 
	 * Dashoard Footer
	**/
	
	public function footer()
	{
		if( ( $this->uri->segment(4) == '__use' && ! multistore_enabled() ) || ( $this->uri->segment(6) == '__use' && multistore_enabled() ) ) {
		?>
<script type="text/javascript">
"use strict";

var SendOrderToKitchen	=	true;
var CustomOrderType		=	null;

var RestaurantTable	=	function(){
	this.reset		=	function(){
		this.hasSelectedTable	=	false;
	};
	
	this.run		=	function(){
		this.reset;
	}
};

var SelectedTable	=	null;
var Restaurant		=	angular.module( 'table', [ 'ngAnimate' ] );
	Restaurant.controller( 'tableCtrl', function( $scope ) {
		$scope.selectedTable	=	null;
		// <button class="btn btn-default" type="submit">Button</button>
		$scope.openTables		=	function(){
			$.ajax( '<?php echo site_url( array( 'rest', 'restaurant', 'tables', store_get_param( '?' ) ) );?>', {
				dataType	:	'json',
				success		:	function( tables_data ){
					var tables	= '';					
						tables	+=	'<h4><?php echo _s( 'Select a table', 'nexo_restaurant' );?></h4><div class="container-fluid"><div class="row">';
					
					if( ! _.isEmpty( tables_data ) ) {
						_.each( tables_data, function( value, key ) {
							if( value.STATUS == '1' ) { // available table	
								var	_class	=	value.ID == $scope.selectedTable ? 'btn-primary' : 'btn-default';							
								tables	+=	'<button style="line-height:40px;margin:0 1% 1% 0;width:24%;" class="btn ' + _class + ' btn-lg" data-table-id="' + value.ID + '" type="submit" value="' + value.ID + '">' + value.NAME + '</button>';
							}
						});
						
						tables	+=	'</div></div>';
						
					} else {
						NexoAPI.Bootbox().alert( '<?php echo _s( 'No table has been found. Please create a table first. "Take Away" order type will be selected.', 'nexo' );?>', function(){
							$( '[data-order-type="takeaway_pending"]' ).trigger( 'click' );
						});
						return;
					}
					
					// Test
					tables		+=	
					'<div class="grid-stack">' +
						'<div class="bg-primary grid-stack-item"' +
							'data-gs-x="0" data-gs-y="0"' +
							'data-gs-width="4" data-gs-height="2">' +
								'<div class="grid-stack-item-content"></div>' +
						'</div>' +
						'<div class="bg-primary grid-stack-item"' +
							'data-gs-x="4" data-gs-y="0"' +
							'data-gs-width="4" data-gs-height="4">' +
								'<div class="grid-stack-item-content"></div>' +
						'</div>' +
						'<div class="bg-primary grid-stack-item"' +
							'data-gs-x="4" data-gs-y="0"' +
							'data-gs-width="4" data-gs-height="4">' +
								'<div class="grid-stack-item-content"></div>' +
						'</div>' +
						'<div class="bg-primary grid-stack-item"' +
							'data-gs-x="4" data-gs-y="0"' +
							'data-gs-width="4" data-gs-height="4">' +
								'<div class="grid-stack-item-content"></div>' +
						'</div>' +
						'<div class="bg-primary grid-stack-item"' +
							'data-gs-x="4" data-gs-y="0"' +
							'data-gs-width="4" data-gs-height="4">' +
								'<div class="grid-stack-item-content"></div>' +
						'</div>' +
					'</div>';
					// Test end
					
					NexoAPI.Bootbox().confirm( tables, function( action ) {
				
						if( action == true ) {
							if( $scope.selectedTable == null ) {
								NexoAPI.Notify().warning( '<?php echo _s( 'Please select a table', 'nexo_restaurant' );?>', '<?php echo _s( 'You need to select a table, before proceed.', 'nexo_restaurant' );?>' );
								return false;
							}
						} else {
							
							/** 
							 * For Dine In Order, a table should be selected otherwise, Takeaway order type is selected
							 **/
							 
							NexoAPI.Bootbox().confirm( '<?php echo _s( 'This order will be changed into "Take Away". Would you like to confirm', 'nexo_restaurant' );?>', function( _action ) {
								if( _action == true ) {
									$( '[data-order-type="takeaway_pending"]' ).trigger( 'click' );
								} else {
									// Opentable is opened another time
									$scope.openTables();
								}
							});
						}
					});
					
					// test
					var options = {
						cell_height: 80,
						vertical_margin: 10
					};
					$('.grid-stack').gridstack(options);
					// test
					
					
					
					// Bind table Selection
					$( '[data-table-id]' ).each(function(index, element) {
						$( this ).bind( 'click', function(){
                        	$scope.selectedTable	=	$( this ).data( 'table-id' );
							$( '[data-table-id]' ).each( function(){
								$( this ).removeClass( 'btn-primary' );
							});
							
							$( this ).addClass( 'btn-primary' );
							$( this ).removeClass( 'btn-default' );
							
							v2Checkout.CartMetas	=	_.extend( v2Checkout.CartMetas, {
								table_id			:	$scope.selectedTable
							})
						});
                    });
			
					$( '.modal-dialog' ).css({
						'width'		:	'80%'
					});
				}
			});
		}
		
		$scope.availableTable	=	0;
		
		if( $scope.availableTable ) {
		}
		
		$scope.restaurantApp	=	new RestaurantTable;
		
		if( $scope.restaurantApp.hasSelectedTable == false ) {
			NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select a table.', 'nexo_restaurant' );?>' );
		}
	});

// Add Print button to paybox
NexoAPI.events.addFilter( 'pay_box_footer', function( data ) {
	return data + ' <input type="checkbox" ' + ( SendOrderToKitchen == true ? 'checked="checked"' : '' )  + ' name="print_tokitchen" print-to-kitchen data-toggle="toggle" data-width="150" data-height="35">';
}, 12 );

NexoAPI.events.addAction( 'pay_box_loaded', function(){
	$('[print-to-kitchen]').bootstrapToggle({
      on: '<?php echo _s('Send to kitchen', 'nexo_sms');?>',
      off: '<?php echo _s('Ignore Kitchen', 'nexo_sms');?>'
    });
	
	$( '[print-to-kitchen]' ).bind( 'change', function(){
		if( typeof $(this).attr( 'checked' ) != 'undefined' ) {
			SendOrderToKitchen	=	true;
		} else {
			SendOrderToKitchen	=	false;
		}
	});
});

/** 
 * Change Order status when submiting
**/

NexoAPI.events.addFilter( 'before_submit_order', function( order ) {
	
	order.TYPE			=	'<?php echo isset( $_GET[ 'order_type' ] ) ? $_GET[ 'order_type' ] : 'takeaway_pending';?>';
	
	if( CustomOrderType != null ) {
		order.TYPE		=	CustomOrderType;
	}
		
	return order;
});

/**
 * Submit Order
**/

NexoAPI.events.addFilter( 'before_submit_order', function( order ) {
	
	/**
	 * Set a table as busy
	**/
	
	if( order.TYPE == 'dinein_pending' ) {
		
		$.ajax( '<?php echo site_url( array( 'rest', 'restaurant', 'tables' ) );?>/' + v2Checkout.CartMetas.table_id + '/<?php echo  store_get_param( '?' );?>', {
			type	:	'PUT',
			data	:	_.object( [ 'STATUS' ], [ 2 ] ), // Busy
		});
		
		console.log( v2Checkout.CartMetas.table_id );
	}
		
	return order;
});

/**
 * Filter Returned Message
**/

NexoAPI.events.addFilter( 'callback_message', function( data ) {
	
	if( _.indexOf( [ 'dinein_pending', 'takeaway_pending', 'delivery_pending' ], data[1].order_type ) ) {
		data[0].title	=	'<?php echo _s( 'Order Submited', 'nexo_restaurant' );?>';
		data[0].msg		=	'<?php echo _s( 'The order has been send to the kitchen', 'nexo_restaurant' );?>';
	}
		
	return data;
});

/**
 * Test Order type 
**/

NexoAPI.events.addFilter( 'test_order_type', function( data ){

	if( _.indexOf( [ 'dinein_pending', 'takeaway_pending', 'delivery_pending' ], data[1].order_type ) ) {
		data[0]	=	true;
	} else {
		data[0]	=	false;
	}
	
	return data;
	
});

/**
 * While submiting order
**/

NexoAPI.events.addFilter( 'before_submit_order', function( order_details ){
	
	<?php global $Options;?>
	
	order_details.site_name		=	'<?php echo @$Options[ 'site_name' ];?>';
	order_details.user_name		=	'<?php echo User::pseudo();?>';
	order_details.date			=	v2Checkout.CartDateTime.format( 'YYYY-MM-DD HH:mm:ss' );
	
	if( SendOrderToKitchen ) {
		$.ajax( '<?php echo site_url( array( 'rest', 'restaurant', 'print_to_kitchen', store_get_param( '?' ) ) );?>', {
			type	:	'POST',
			success	:	function( data ) {
				// console.log( data );
			},
			dataType	:	'json',
			data	:	order_details		
		});
	}
	
	return order_details
});

/**
 * Edit item edit button
**/

NexoAPI.events.addFilter( 'cart_before_item_name', function( name ) {
	return '<a class="btn btn-sm btn-default add_item_note" href="javascript:void(0)" style="vertical-align:inherit;margin-right:10px;"><i class="fa fa-edit"></i></a>';
});

/**
 * Add custom meta to items
**/

NexoAPI.events.addFilter( 'items_metas', function( data ){
	data.push({
		'ITEM_NOTE'	:	''
	})
	
	return data;
});

/**
 * Cart refreshed
**/

NexoAPI.events.addAction( 'cart_refreshed', function(){
	$( '.add_item_note' ).bind( 'click', function(){

		var $this		=	$( this );
		var barcode		=	$this.closest( '[cart-item]' ).attr( 'data-item-barcode' );
		var OldItemNote	=	'';
		
		_.each( v2Checkout.CartItems, function( _item, key ) {
			// console.log( _item );
			// Looking for the right item to use "note" meta
			if( _item.CODEBAR == barcode ) {
				OldItemNote		=	_item.ITEM_NOTE
			} 
		})
		
		var dom		=	'<h4 class="text-center"><?php _e( 'Add note to this item', 'nexo_restaurant' );?></h4>' + 
		'<div class="form-group">' + 
			'<textarea class="form-control" rows="3" item_note></textarea>' +
		'</div>';
		
		NexoAPI.Bootbox().confirm( dom, function( action ) {
			if( action == true ) { // if action confirmed
				// var barcode		=	$this.closest( '[cart-item]' ).attr( 'data-item-barcode' );
				
				_.each( v2Checkout.CartItems, function( _item, key ) {
					// Looking for the right item to add meta
					if( _item.CODEBAR == barcode ) {
						v2Checkout.CartItems[ key ].ITEM_NOTE		=	$( '[item_note]' ).val();
					}
				})
			}
		});
		
		$( '[item_note]' ).val( OldItemNote	);
	});
});

/**
 * Make NexoPOS support payment type "Save"
**/

NexoAPI.events.addFilter( 'check_payment_mean', function( data ) {
	if( data[1] == 'save' ) {
		data[0]	=	true;
	}
	return data;
});

NexoAPI.events.addFilter( 'payment_mean_checked', function( data ){
	if( data[1] == 'save' ) {
		data[0].SOMME_PERCU		=	0;
		data[0].PAYMENT_TYPE	=	'saved';
	}
	return data;
});

$( document ).ready(function(e) {
    $( '[data-order-type]' ).bind( 'click', function(){
		CustomOrderType		=	$( this ).attr( 'data-order-type' );
		$( '[order_type]' ).html( $( this ).html() );
		
		// For Dine In, lets show table select
		if( CustomOrderType == 'dinein_pending' ) {
			$( '[ng-click="openTables()"]' ).trigger( 'click' );
		}
	});
	
	$( '.save_order' ).bind( 'click', function(){
		if( v2Checkout.isCartEmpty() == false ) {
			
			NexoAPI.Bootbox().prompt( '<?php echo _s( 'Set order title', 'nexo_restaurant' );?>', function( order_name ) {
				if( order_name != null ) {
					if( order_name == '' ) {
						NexoAPI.Notify().warning( '<?php echo _s( 'An error occured', 'nexo_restaurant' );?>', '<?php echo _s( 'Please set a title', 'nexo_restaurant' );?>' );
						return;
					}
					
					NexoAPI.events.addFilter( 'cart_enable_print', function( data ) {
						return false;
					});
					
					NexoAPI.events.addFilter( 'before_submit_order', function( order_details ) {
						order_details.TITRE		=	order_name;
						return order_details;
					});
					
					v2Checkout.cartSubmitOrder( 'save' );
					
					NexoAPI.Notify().info( 
						'<?php echo _s( 'Saving Order', 'nexo_restaurant' );?>', 
						'<?php echo _s( 'The order has been saved.', 'nexo_restaurant' );?>' 
					);
				}
			});
			
		} else {
			NexoAPI.Bootbox().alert( '<?php echo _s( 'An empty order can\'t be saved.', 'nexo_restaurant' );?>' );
		}
	});
	
	<?php if( isset( $_GET[ 'order_type' ] ) ):?>
	$( '[data-order-type="<?php echo $_GET[ 'order_type' ];?>"]' ).trigger( 'click' );
	<?php else:?>
	$( '[data-order-type="dinein_pending"]' ).trigger( 'click' );
	<?php endif;?>
});	
</script>
		
        <?php
		}	
		if( User::in_group( 'shop_cashier' ) ) {
?>
<script type="text/javascript">
			<?php if( User::in_group( 'shop_cashier' ) ):?>
$( '.main-sidebar' ).remove();
$( '.content-wrapper' ).each(function () {
	this.style.setProperty( 'margin-left', '0px', 'important' );
});
$( '.main-footer' ).each(function () {
	this.style.setProperty( 'margin-left', '0px', 'important' );
});

$( '.main-header' ).find( '.logo' ).remove();

$( '.navbar.navbar-static-top' ).each(function () {
	this.style.setProperty( 'margin-left', '0px', 'important' );
});
<?php endif;?>
</script>
			<?php
		}
	}
	
	/**
	 * Load Dashbaord
	**/
	
	public function load_dashboard()
	{
		$this->lang->load_lines(dirname(__FILE__) . '/../language/lines.php');
        
        $this->load->config('nexo_restaurant');
		
		$this->Gui->register_page( 'nexo_restaurant', array( $this->Tables_Controller, 'index' ) );
		$this->Gui->register_page( 'nexo_restaurant_areas', array( $this->Areas_Controller, 'index' ) );
		$this->Gui->register_page( 'nexo_restaurant_kitchens', array( $this->Kitchens_Controller, 'index' ) );
		
		// Change Nexo order status
		$this->load->config( 'nexo' );
		
		$old_order_type	=	$this->config->item( 'nexo_order_types' );
		
		$old_order_type[ 'dinein_ready' ]		=	__( 'Dine In Ready', 'nexo_restaurant' );
		$old_order_type[ 'dinein_pending' ]		=	__( 'Dine In Pending', 'nexo_restaurant' );
		$old_order_type[ 'dinein_ongoing' ]		=	__( 'Dine In Ongoing', 'nexo_restaurant' );
		$old_order_type[ 'dinein' ]				=	__( 'Dine In Complete', 'nexo_restaurant' );
		
		$old_order_type[ 'delivery_ready' ]		=	__( 'Delivery Ready', 'nexo_restaurant' );
		$old_order_type[ 'delivery_pending' ]	=	__( 'Delivery Pending', 'nexo_restaurant' );
		$old_order_type[ 'delivery_ongoing' ]	=	__( 'Delivery Ongoing', 'nexo_restaurant' );
		$old_order_type[ 'delivery' ]			=	__( 'Delivery Complete', 'nexo_restaurant' );
		
		$old_order_type[ 'takeaway_ready' ]		=	__( 'Take Away Ready', 'nexo_restaurant' );
		$old_order_type[ 'takeaway_pending' ]	=	__( 'Take Away Pending', 'nexo_restaurant' );
		$old_order_type[ 'takeaway_ongoing' ]	=	__( 'Take Away Ongoing', 'nexo_restaurant' );
		$old_order_type[ 'takeaway' ]			=	__( 'Take Away Complete', 'nexo_restaurant' );
		
		$old_order_type[ 'void' ]				=	__( 'Void', 'nexo_restaurant' );
		$old_order_type[ 'saved' ]				=	__( 'Saved', 'nexo_restaurant' );
		
		$this->config->set_item( 'nexo_order_types', $old_order_type );
	}
	
	/** 
	 * Dash Menu
	**/
	
	public function dash_menu()
	{
		if( ( $this->uri->segment(4) == '__use' && ! multistore_enabled() ) || ( $this->uri->segment(6) == '__use' && multistore_enabled() ) ) {
		?>
          <li class="dropdown messages-menu" ng-app="table" ng-controller="tableCtrl">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" ng-click="openTables()">
              <?php _e( 'Select Table', 'nexo_restaurant' );?>
              <span ng-class="[isHidden]" class="label label-success">{{availableTable}}</span>
            </a>
          </li>
		<?php
		} 
		if( User::in_group( 'shop_cashier' ) ){
		?>
	  <li>
		<a href="<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'for_cashiers' ) );?>">
		  <?php _e( 'Select Register', 'nexo_restaurant' );?>
		</a>
	  </li>
		<?php
		}
		?>
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle today_sales" data-toggle="dropdown" aria-expanded="true">
            	<i class="fa fa-circle-o-notch"></i>
              <?php _e( 'Today Sales', 'nexo_restaurant' );?>
              <!-- <span class="label label-warning">30</span> -->
            </a>
          </li>
		<script type="text/javascript">
		$( document ).ready(function(e) {			
			$( '.today_sales' ).bind( 'click', function(){
				var today_start		=	'<?php echo Carbon::parse( date_now() )->startOfDay();?>';
				var today_end		=	'<?php echo Carbon::parse( date_now() )->endOfDay();?>';
				
				$.ajax( '<?php echo site_url( array( 'rest', 'restaurant', 'order_by_dates', store_get_param( '?' ) ) );?>', {
					type	:	'POST',
					data	:	_.object( [ 'start', 'end' ], [ today_start, today_end ] ),
					success		:	function( data ){
						
						$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'registers', store_get_param( '?' ) ) );?>', {
							success		:	function( registers_data ){						
								var content	=	'';
								if( ! _.isEmpty( data ) ) {
									var	order_type		=	$.parseJSON( '<?php echo json_encode( $this->config->item( 'nexo_order_types' ) );?>' );
									
									_.each( data, function( _order, key ) {
										
										var	registers	=	'<div class="input-group"><span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Use register', 'nexo_restaurant' );?></span><select class="form-control select_register"><option value=""><?php echo _s( 'Choose a register', 'nexo_restaurant' );?></option>';
										
										if( ! _.isEmpty( registers_data ) ) {

											_.each( registers_data, function( _register, key ) {
												registers	+=	'<option value="<?php echo site_url( array( 'dashboard', 'nexo', 'registers', '__use' ) );?>/' + _register.ID + '/' + _order.ID + '">' + _register.NAME + '</option>';
											});
											
										} else {
											registers	=	'<option value=""><?php echo _s( 'No registers available. Please create a registers', 'nexo_restaurant' );?></option>';
										};
										
										registers		+=	'</select></div>';
										
										content	+=	
										'<tr>' +
											'<td>' + _order.CODE + '</td>' + 
											'<td>' + _.propertyOf( order_type )( _order.TYPE ) + '</td>' +
											'<td>' + _order.NOM + '</td>' +
											'<td>' + NexoAPI.DisplayMoney( _order.TOTAL ) + '</td>' +
											'<td>' + registers  + '</td>' +
										'</tr>';
									});
								} else {
									content	+=	
									'<tr>' +
										'<td colspan="5" class="text-center"><?php echo _s( 'No order yet', 'nexo_restaurant' );?></td>' +
									'</tr>';
								}
								
								var dom		=	'<h4><?php echo _s( 'Today Sales', 'nexo_restaurant' );?></h4>' +
									'<br>' +
									'<table class="table table-bordered">' + 
										'<thead>' +
											'<tr>' +
												'<td><?php echo _s( 'Code', 'nexo_restaurant' );?></td>' +
												'<td><?php echo _s( 'Order Type', 'nexo_restaurant' );?></td>' +
												'<td><?php echo _s( 'Customer', 'nexo_restaurant' );?></td>' + 
												'<td><?php echo _s( 'Total', 'nexo_restaurant' );?></td>' +
												'<td width="300"><?php echo _s( 'Edit with register', 'nexo_restaurant' );?></td>' +
											'</tr>' +
										'</thead>' + 
										'<tbody>' +
											content + 
										'</tbody>' +
									'</table>';
				
								bootbox.alert( dom, function( action ){
								});
								
								// Set custom width
								$( '.select_register' ).closest( '.modal-dialog' ).css({
									'width'		:	'80%'		
								})
								
								$( '.select_register' ).bind( 'change', function(){
									if( $( this ).val() != '' ) {
										document.location	=	$( this ).val();
									}
								});
							},
							dataType	:	'json',
							error		:	function(){
								bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement...', 'nexo' );?>' );
							}
						});
						
					},
					dataType:"json",
					error	:	function(){
						bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement...', 'nexo' );?>' );
					}
				});
			});
        });
		</script>
        <?php
	}
	
	/**
	 * Dashboard Home
	**/
	
	public function dashboard_home()
	{
		$this->dashboard_widgets->remove( 'nexo_profile' );
		
		$this->dashboard_widgets->add('nexo_restaurant_order_tools', array(
            'title'                    => __( 'Transactions', 'nexo'),
            'type'                    => 'box-primary',
            // 'hide_body_wrapper'        =>    true,
            // 'background-color'	=>	'',
            'position'                => 2,
            'content'                =>    $this->load->module_view( 'nexo_restaurant', 'dashboard/widgets/transactions', array(), true )
        ));
		
		$this->dashboard_widgets->add('nexo_restaurant_operation', array(
            'title'                    => __( 'Useful Links', 'nexo'),
            'type'                    => 'box-primary',
            // 'hide_body_wrapper'        =>    true,
            // 'background-color'	=>	'',
            'position'                => 3,
            'content'                =>    $this->load->module_view( 'nexo_restaurant', 'dashboard/widgets/links', array(), true )
        ));
	}
}