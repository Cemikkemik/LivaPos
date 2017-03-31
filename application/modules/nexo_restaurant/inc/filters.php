<?php
class Nexo_Restaurant_Filters extends CI_Model
{
	public function __construct()
	{
		// Creaet menus
		$this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ), 15 );	
		
		// Load Assets
		$this->events->add_filter('default_js_libraries', function ($libraries) {
			$bower_path		=    '../modules/nexo_restaurant/bower_components/';
			
			$libraries[]	=	$bower_path . 'chance/chance';
				
			return $libraries;
		});		
		
		// Changin route for Cashier
		$this->events->add_filter( 'login_redirection', function( $route ){
			if( User::in_group( 'shop_cashier' ) ){
				return array( 'dashboard', 'nexo', 'registers', 'for_cashiers' );
			}
		});
		
		// Allow print for order
		$this->events->add_filter( 'allowed_order_for_print', function( $orders ){
			
			foreach( array( 'dinein', 'delivery', 'takeaway' ) as $order_type ) {
				$orders[]		=	$order_type . '_pending';
				$orders[]		=	$order_type . '_ready';
				$orders[]		=	$order_type . '_ongoing';
				$orders[]		=	$order_type;
			}
			
			// Saved
			$orders[]			=	'saved';
			// Void
			$orders[]			=	'void';
			
			return $orders;
			
		});
		
		$this->events->add_filter( 'pos_search_input_after', function( $code ){
			ob_start();
			$this->load->config( 'nexo' );
			?>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php _e( 'Type : ', 'nexo_restaurant' );?><span order_type><?php _e( 'Order Type', 'nexo_restaurant' );?></span><span class="caret"></span></button>
            <ul class="dropdown-menu">
            <?php foreach( $this->config->item( 'nexo_order_types' ) as $key => $order_type ):?>
                <?php if( in_array( $key, array( 'dinein_pending', 'takeaway_pending', 'delivery_pending' ) ) ):?>
                    <li><a href="#" data-order-type="<?php echo $key;?>"><?php echo $order_type;?></a></li>
                <?php endif;?>
            <?php endforeach;?>
            </ul>
            <?php
			return $code . ob_get_clean();
		});
		
		// Lock Order Types
		$this->events->add_filter( 'order_type_locked', function( $array ) {
			foreach( array( 'dinein', 'delivery', 'takeaway' ) as $order_type ) {
				// $array[]		=	$order_type . '_pending';
				$array[]		=	$order_type . '_ready';
				$array[]		=	$order_type . '_ongoing';
				$array[]		=	$order_type;
			}
			
			return $array;
		});
		
		// List Class
		$this->events->add_filter( 'order_list_class', function( $class, $row ) {
			
			$nexo_order_types    =    array_flip($this->config->item('nexo_order_types'));
			// Pending Order
			$pending_orders		=	array();
			foreach( array( 'dinein', 'delivery', 'takeaway' ) as $order_type ) {
				$pending_orders[]		=	$order_type . '_pending';
			}
			// Pending Order for Saved Order
			$pending_orders[]		=	'saved';
			// Pending order class for void order
			$pending_orders[]		=	'void';
			
			// Complete Order
			$ready_orders		=	array();
			foreach( array( 'dinein', 'delivery', 'takeaway' ) as $order_type ) {
				$ready_orders[]		=	$order_type . '_ready';
			}
			
			// Ongoing Order
			$ongoing_orders		=	array();
			foreach( array( 'dinein', 'delivery', 'takeaway' ) as $order_type ) {
				$ongoing_orders[]		=	$order_type . '_ongoing';
			}
			
			if( in_array( $nexo_order_types[ $row->TYPE ], array( 'dinein', 'delivery', 'takeaway' ) ) ) {
				return 'new-order';
			} else if( in_array( $nexo_order_types[ $row->TYPE ], $ready_orders ) ) {
				return 'ready-order';
			} else if( in_array( $nexo_order_types[ $row->TYPE ], $pending_orders ) ) {
				return 'pending-order';
			} else if( in_array( $nexo_order_types[ $row->TYPE ], $ongoing_orders ) ) {
				return 'ongoing-order';
			}
		}, 10, 2 );
		
		// Order editable
		$this->events->add_filter( 'order_editable', function( $array ) {
			
			$array				=	array();
			
			foreach( array( 'dinein_pending', 'delivery_pending', 'takeaway_pending', 'saved' ) as $order_type ) {
				$array[]		=	$order_type;
			}
			
			return $array;
		});
		
		$this->events->add_filter( 'before_cart_pay_button', function( $output ){
			ob_start();
			?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default btn-lg save_order"><i class="fa fa-hdd-o"></i> <?php _e( 'Save', 'nexo_restaurant' );?></button>
            </div>
            <?php
			return $output . ob_get_clean();
		});
		
		// Registers for Multistore
		$this->events->add_filter( 'stores_controller_callback', array( $this, 'multistore' ) );
	}
	
	/**
	 * Creating menus
	**/
	
	public function admin_menus( $menus )
	{
		// Hide menu for cashier
		if( User::in_group( 'shop_cashier' ) ) {
			unset( $menus[ 'users' ] );
		}
		
		if (
            User::can('create_restaurant_tables')  &&
            User::can('edit_restaurant_tables') &&
            User::can('delete_restaurant_tables')
        ) {
            
			if( array_key_exists( 'arrivages', $menus ) ) {
				$menus	=	array_insert_before( 'arrivages', $menus, 'tables', array(
					array(
						'title'		=>	__( 'Restaurant Tables', 'nexo_restaurant' ),
						'href'		=>	'#',
						'icon'		=>	'fa fa-cutlery',
						'disable'	=>	true
					),
					array(
						'title'		=>	__( 'Table list', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant', 'lists' ) ),
					),
					array(
						'title'		=>	__( 'Create a new table', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant', 'lists', 'add' ) ),
					),
					array(
						'title'		=>	__( 'Areas list', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant_areas', 'lists' ) ),
					),
					array(
						'title'		=>	__( 'Create a new area', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant_areas', 'lists', 'add' ) ),
					)
				) );
			}			
		}
		
		if (
            User::can('create_restaurant_kitchens')  &&
            User::can('edit_restaurant_kitchens') &&
            User::can('delete_restaurant_kitchens')
        ) {
			// Kitchen Menu
			if( array_key_exists( 'tables', $menus ) ) {
				$menus		=	array_insert_after( 'tables', $menus, 'nexo_kitchen', array(
					array(
						'title'		=>	__( 'Restaurant Kitchens', 'nexo_restaurant' ),
						'href'		=>	'#',
						'icon'		=>	'fa fa-fire',
						'disable'	=>	true
					),
					array(
						'title'		=>	__( 'Kitchens list', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant_kitchens', 'lists' ) )
					),
					array(
						'title'		=>	__( 'Create new Kitchen', 'nexo_restaurant' ),
						'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo_restaurant_kitchens', 'lists', 'add' ) )
					)
				) );
			}
		}
		
		return $menus;
	}
	
	/**
	 * Register for Multistore
	**/
	
	public function multistore( $array )
	{
		$this->Areas_Controller		=	new Nexo_Restaurant_Areas_Controllers;
		$this->Kitchens_Controller	=	new Nexo_Restaurant_Kitchens_Controllers;
		$this->Tables_Controller	=	new Nexo_Restaurant_Tables_Controllers;
		
		// to match this uri
		// dashboard/stores/nexo_premium/*
		$array[ 'nexo_restaurant' ]				=	array( $this, 'nexo_restaurant_controller' );
		$array[ 'nexo_restaurant_areas' ]		=	array( $this, 'nexo_restaurant_areas_controller' );
		$array[ 'nexo_restaurant_kitchens' ]	=	array( $this, 'nexo_restaurant_kitchens_controller' );
		
		return $array;
	}
	
	/**
     * Index Page
     * @params string
     * @return void
    **/
    
    public function nexo_restaurant_controller($page)
    {
		$page	=	'index';
        if (method_exists( $this->Tables_Controller, $page)) {
            call_user_func_array( array( $this->Tables_Controller, $page ), array_slice(func_get_args(), 1));
        } else {
            show_error(__('Cette page est introuvable', 'nexo_restaurant'));
        }
    }
	
	public function nexo_restaurant_areas_controller($page)
    {
		$page	=	'index';
        if (method_exists( $this->Areas_Controller, $page)) {
            call_user_func_array( array( $this->Areas_Controller, $page ), array_slice(func_get_args(), 1));
        } else {
            show_error(__('Cette page est introuvable', 'nexo_restaurant'));
        }
    }
	
	public function nexo_restaurant_kitchens_controller($page)
    {
		$page	=	'index';
        if (method_exists( $this->Kitchens_Controller, $page)) {
            call_user_func_array( array( $this->Kitchens_Controller, $page ), array_slice(func_get_args(), 1));
        } else {
            show_error(__('Cette page est introuvable', 'nexo_restaurant'));
        }
    }
}