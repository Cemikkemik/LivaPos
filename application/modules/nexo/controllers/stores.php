<?php

use Pecee\SimpleRouter\SimpleRouter as Route;
use \Pecee\SimpleRouter\Router;
use \Pecee\SimpleRouter\Route\RouteUrl;
use Pecee\Http\Request;
use Pecee\SimpleRouter\IRouterBootManager;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\SimpleRouter\Route\RouteController;

class NexoStoreController extends CI_Model
{ 
    public function crud_header()
    {
        if( User::cannot( 'nexo.view.stores' ) ) {
			return nexo_access_denied();
		}
		
		$this->load->model( 'Nexo_Stores' );
        
        $crud = new grocery_CRUD();
        $crud->set_theme('bootstrap');
        $crud->set_subject(__('une boutique', 'nexo'));

        $crud->set_table($this->db->dbprefix('nexo_stores'));
		
        $crud->columns( 'NAME', 'STATUS', 'IMAGE', 'AUTHOR', 'DATE_CREATION', 'DATE_MOD' );
        $crud->fields( 'NAME', 'STATUS', 'IMAGE', 'AUTHOR', 'DESCRIPTION', 'DATE_CREATION', 'DATE_MOD' );
		$crud->field_type('STATUS', 'dropdown', $this->config->item('nexo_shop_status'));                
        $crud->order_by('DATE_CREATION', 'desc');
        
        $crud->display_as('NAME', __('Nom de la boutique', 'nexo'));
		$crud->display_as('IMAGE', __('Aperçu', 'nexo'));
        $crud->display_as('DESCRIPTION', __('Description', 'nexo'));
        $crud->display_as('AUTHOR', __('Auteur', 'nexo'));
		$crud->display_as('DATE_CREATION', __('Date création', 'nexo'));
		$crud->display_as('DATE_MOD', __('Date de modification', 'nexo'));
		$crud->display_as('STATUS', __('Etat de la boutique', 'nexo'));
		
		$crud->change_field_type('AUTHOR', 'invisible');
        $crud->change_field_type('DATE_MOD', 'invisible');
        $crud->change_field_type('DATE_CREATION', 'invisible');
		
		$crud->set_relation('AUTHOR', 'aauth_users', 'name');
		
		$crud->set_field_upload('IMAGE', 'public/upload/stores');
        
        // Liste des produits
        $crud->add_action(__('Accéder à la boutique', 'nexo'), '', site_url(array( 'dashboard', 'stores' )) . '/', 'fa fa-sign-in');
		
		$crud->callback_before_insert(array( $this->Nexo_Stores, '__insert_store' ));
		$crud->callback_before_update(array( $this->Nexo_Stores, '__update_store' ));
		$crud->callback_before_delete(array( $this->Nexo_Stores, '__delete_store' ));
		$crud->callback_after_insert( array( $this->Nexo_Stores, '__callback_after_insert' ) );
                
        $this->events->add_filter('grocery_callback_insert', array( $this->grocerycrudcleaner, 'xss_clean' ));
        $this->events->add_filter('grocery_callback_update', array( $this->grocerycrudcleaner, 'xss_clean' ));
        
        $crud->required_fields('NAME', 'STATUS');
        
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
    
    public function lists($page = 'index', $id = null)
    {
		global $PageNow;
		$this->events->add_filter( 'gui_page_title', function( $title ) {
			return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', 'nexo', 'stores', 'all' )) . '">' . __('Mode Simplifié', 'nexo') . '</a></h1></section>';
		});
		
        if ($page == 'index') {
			
			$PageNow		=	'nexo/stores/list';
			
            $this->Gui->set_title( store_title( __('Liste des boutiques', 'nexo')) );
        } elseif ($page == 'delete') { // Check Deletion permission
		
			$PageNow		=	'nexo/stores/delete';

            nexo_permission_check('nexo.delete.store');
            
        } else {
			
			$PageNow		=	'nexo/stores/create';
			
            $this->Gui->set_title( store_title( __('Créer une nouvelle boutique', 'nexo')) );
        }
        
        $data[ 'crud_content' ]    =    $this->crud_header();
		
        $this->load->view('../modules/nexo/views/stores/list.php', $data);
    }
    
    public function add()
    {
        if( User::cannot( 'nexo.create.stores' ) ) {
			return nexo_access_denied();
		}
		
		global $PageNow;
		$PageNow					=	'nexo/stores/add';
        
        $data[ 'crud_content' ]    =    $this->crud_header();

        $this->Gui->set_title( store_title( __('Créer une nouvelle boutique', 'nexo') ) );
        $this->load->view('../modules/nexo/views/stores/list.php', $data);
    }
    
    public function defaults()
    {
        $this->lists();
    }
	
	/**
	 * All Stores
	**/
	
	public function all()
	{
		global $PageNow;
		$PageNow					=	'nexo/stores_all/list';
		
		$this->events->add_filter( 'gui_page_title', function( $title ) {
			return '<section class="content-header"><h1>' . strip_tags($title) . ' <a class="btn btn-primary btn-sm pull-right" href="' . site_url(array( 'dashboard', 'nexo', 'stores', 'lists' )) . '">' . __('Mode Avancé', 'nexo') . '</a></h1></section>';
		});
		
		$this->load->model( 'Nexo_Stores' );
		
		$data[ 'data' ]		=	array(
			'stores'		=>		$this->events->apply_filters( 'stores_list_menu', $this->Nexo_Stores->get() )
		);
		
		$this->Gui->set_title( store_title( __('Toutes les boutiques &mdash; NexoPOS', 'nexo') ));
        $this->load->view('../modules/nexo/views/stores/all-stores.php', $data);
    }
    
    /**
	 * Store
	**/

	public function stores()
	{
		global	$store_id,
				$CurrentStore,
				$Options;

		if( @$Options[ 'nexo_store' ] == 'enabled' ) {

			$urls 				=	func_get_args();
			$store_id 			=	@$urls[0];
            $slug_namespace 	= 	@$urls[1];
			$urls	 			=	array_splice( $urls, 2 );

			if( $CurrentStore ) {

				// if store is closed, then no one can access to that
				if( @$CurrentStore[0][ 'STATUS' ] == 'closed' ) {
					show_error( __( 'Cette boutique est fermée', 'nexo' ) );
				}
				// Reset Previous Routing System
				global $Routes;
				$Routes->router()->reset();

				// Start a new Routing System
				global $StoreRoutes;
				
				$StoreRoutes 	=	new Route();

				// Hard Code route to access to the store.
				$StoreRoutes->partialGroup( substr( request()->getHeader( 'script-name' ), 0, -10 ) . '/dashboard/stores/' . $store_id . '/', function() {
					
					$modules                =   Modules::get();
					
					foreach( $modules as $namespace => $module ) {
						if( Modules::is_active( $namespace ) ) {
							if( is_dir( $dir = MODULESPATH . $namespace . '/controllers/' ) ) {
								foreach( glob( $dir . "*.php") as $filename) {
									include_once( $filename );
								}
							}
				
							if( is_file( MODULESPATH . $namespace . '/store-routes.php' ) ) {
								include_once( MODULESPATH . $namespace . '/store-routes.php' );
							}
						}
					}

				});		

				// Show Errors
				$StoreRoutes->error(function($request, \Exception $exception) {
					return show_error( sprintf( 
						__( 'The request returned the following message : %s<br>Code : %s'  ),
						$exception->getMessage(),
						$exception->getCode()
					), intval( $exception->getCode() ) );
				});
				
				$StoreRoutes->start();

			} else {
				show_error( __( 'Boutique introuvable.', 'nexo' ) );
			}
		} else {
			show_error( __( 'Fonctionnalité indisponible ou désactivée.', 'nexo' ) );
		}
	}
}