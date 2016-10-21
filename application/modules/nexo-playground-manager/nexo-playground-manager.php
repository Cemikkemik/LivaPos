<?php 

include_once( dirname( __FILE__ ) . '/inc/controller.php' );

class NexoPlayGroundMain extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->events->add_filter( 'grocery_registered_fields', array( $this, 'add_fields' ) );
		$this->events->add_filter( 'grocery_edit_fields', array( $this, 'add_fields' ) );
		$this->events->add_filter( 'grocery_input_fields', array( $this, 'input_fields' ) );
		$this->events->add_action( 'nexo_after_save_product', array( $this, 'save_item' ), 10, 2 );
		$this->events->add_action( 'nexo_after_update_product', array( $this, 'update_item' ), 10, 2 );
		$this->events->add_filter( 'product_required_fields', array( $this, 'required_fields' ) );
		$this->events->add_filter( 'load_product_crud', array( $this, 'crud_load' ) );
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
		$this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ), 20 );
		// $this->events->add_filter( 'grocery_get_add_fields', array( $this, 'add_fields' ) );
		$this->Controller		=	new NexoPlayGroundController;
		$this->events->add_filter( 'stores_controller_callback', array( $this, 'controller_callback' ) );
	}
	
	/**
	 * Add Fields
	 * Add fields to grocery crud
	 * @return object
	**/
	
	public function add_fields( $fields )
	{
		global $PageNow;
		
		if( $PageNow	==	'nexo/produits' ) {
			
			$field_enabled	=	new stdClass;
			$field_enabled->field_name		=	'NP_ENABLED';
			$field_enabled->display_as		=	__( 'Use as time package', 'nexo-playground-manager' );
			$field_enabled->description		=	tendoo_info( __( 'Let you set whether this item should be used as time package. make sure to provide correct time in minute on the next field.', 'nexo-playground-manager' ) );
			
			$fields[]	=	$field_enabled;
			
			$field_time	=	new stdClass;
			$field_time->field_name		=	'NP_TIME';
			$field_time->display_as		=	__( 'Time in minutes', 'nexo-playground-manager' );
			$field_time->description	=	tendoo_info( __( 'You can set how many time this item will add to the order.', 'nexo-playground-manager' ) );
			
			$fields[]	=	$field_time;
		}
		
		return $fields;
	}
	
	/**
	 * Admin Menus
	**/
	
	public function admin_menus( $menus )
	{
		$array	=	array_insert_before( 'arrivages', $menus, 'np_menus', array(
			array( 
				'title'		=>	__( 'Nexo PlayGround', 'nexo-playground-manager' ),
				'disable'	=>	true,
				'icon'		=>	'fa fa-paw'
			),
			array( 
				'title'		=>	__( 'Manage Package', 'nexo-playground-manager' ),
				'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo-playground-manager', 'manager' ) )
			)
		) );
		
		return $array ? $array : $menus;
	}
	
	/**
	 * Controller Callback
	**/
	
	public function controller_callback( $controllers )
	{
		$controllers[ 'nexo-playground-manager' ]	=	array( $this->Controller, 'manager' );
		
		return $controllers;
	}
	
	/**
	 * Crud Load
	**/
	
	public function crud_load( $crud )
	{
		$crud->add_group( 'np_options', __( 'PlayGround Options', 'nexo-playground-manager' ), array( 'NP_TIME', 'NP_ENABLED' ), 'fa-star' );
		return $crud;
	}
	
	/**
	 * Input Fields
	 * @return object
	**/
	
	public function input_fields( $input_fields )
	{
		global $PageNow;
		
		if( $PageNow	==	'nexo/produits' ) {
			
			if( is_multistore() ) {
				$id			=		$this->uri->segment(8);
			} else {
				$id			=		$this->uri->segment(8);
			}
			
			$NP_ENABLED		=		null;			
			if( $id ) {
				$data		=		$this->db->where( 'REF_ARTICLE', $id )
				->where( 'KEY', 'NP_ENABLED' )
				->get( store_prefix() . 'nexo_articles_meta' )
				->result_array();
	
				$NP_ENABLED	=		@$data[0][ 'VALUE' ];
			}
			
			$input_fields[ 'NP_ENABLED' ]					=	new stdClass;
			$input_fields[ 'NP_ENABLED' ]->name				=	'NP_ENABLED';
			$input_fields[ 'NP_ENABLED' ]->type				=	'varchar';
			$input_fields[ 'NP_ENABLED' ]->max_length		=	200;
			$input_fields[ 'NP_ENABLED' ]->primary_key		=	0;
			$input_fields[ 'NP_ENABLED' ]->default			=	null;
			$input_fields[ 'NP_ENABLED' ]->db_max_length	=	11;
			$input_fields[ 'NP_ENABLED' ]->db_type			=	'varchar';
			$input_fields[ 'NP_ENABLED' ]->db_null			=	false;
			$input_fields[ 'NP_ENABLED' ]->required			=	true;
			$input_fields[ 'NP_ENABLED' ]->display_as		=	__( 'Use as time package', 'nexo-playground-manager' );
			$input_fields[ 'NP_ENABLED' ]->crud_type		=	false;
			$input_fields[ 'NP_ENABLED' ]->extras			=	false;
			$input_fields[ 'NP_ENABLED' ]->input			=	'
  <select id="field-NP_ENABLED"  name="NP_ENABLED" class="form-control"><option value="2" ' . ( $NP_ENABLED == '2' ? 'selected="selected"' : '' ) . '>' . __( 'No', 'nexo' ) . '</option><option value="1" ' . ( $NP_ENABLED == '1' ? 'selected="selected"' : '' ) . '>' . __( 'Yes', 'nexo' ) . '</option></select>';
			
			$NP_TIME		=		null;			
			if( $id ) {
				$data		=		$this->db->where( 'REF_ARTICLE', $id )
				->where( 'KEY', 'NP_TIME' )
				->get( store_prefix() . 'nexo_articles_meta' )
				->result_array();
	
				$NP_TIME	=		@$data[0][ 'VALUE' ];
			} 
			
			$input_fields[ 'NP_TIME' ]					=	new stdClass;
			$input_fields[ 'NP_TIME' ]->name			=	'NP_TIME';
			$input_fields[ 'NP_TIME' ]->type			=	'varchar';
			$input_fields[ 'NP_TIME' ]->max_length		=	200;
			$input_fields[ 'NP_TIME' ]->primary_key		=	0;
			$input_fields[ 'NP_TIME' ]->default			=	null;
			$input_fields[ 'NP_TIME' ]->db_max_length	=	11;
			$input_fields[ 'NP_TIME' ]->db_type			=	'varchar';
			$input_fields[ 'NP_TIME' ]->db_null			=	false;
			$input_fields[ 'NP_TIME' ]->required		=	true;
			$input_fields[ 'NP_TIME' ]->display_as		=	__( 'Time in minutes', 'nexo-playground-manager' );
			$input_fields[ 'NP_TIME' ]->crud_type		=	false;
			$input_fields[ 'NP_TIME' ]->extras			=	false;
			$input_fields[ 'NP_TIME' ]->input			=	'<input id="field-NP_TIME" class="form-control" name="NP_TIME" type="text" value="' . $NP_TIME . '" maxlength="200">';
		}
		
		return $input_fields;
	}
	
	/** 
	 * Load Dashboard
	**/
	
	public function load_dashboard()
	{
		$this->Gui->register_page_objet( 'nexo_playground_manager', $this->Controller );
	}
	
	/**
	 * Required field
	**/
	
	public function required_fields( $fields ) 
	{
		$fields[]	=	'NP_TIME';
		$fields[]	=	'NP_ENABLED';
		return $fields;
	}
	
	/**
	 * While Saving item
	**/
	
	public function save_item( $array, $id )
	{
		$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_TIME' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'NP_TIME',
			'VALUE'				=>	$array[ 'NP_TIME' ],
			'REF_ARTICLE'		=>	$id
		) );
		
		$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_ENABLED' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'NP_ENABLED',
			'VALUE'				=>	$array[ 'NP_ENABLED' ],
			'REF_ARTICLE'		=>	$id
		) );
	}
	
	/**
	 * While Updating item
	 * @param array item details
	 * @param int order id
	 * @return void
	**/
	
	public function update_item( $array, $id )
	{
		$query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_TIME' )
		->get( store_prefix() . 'nexo_articles_meta' );
		
		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_TIME' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'NP_TIME',
				'VALUE'				=>	$array[ 'NP_TIME' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_TIME' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'NP_TIME' ]
			) );
		}
		
		$query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_ENABLED' )
		->get( store_prefix() . 'nexo_articles_meta' );
		
		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_ENABLED' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'NP_ENABLED',
				'VALUE'				=>	$array[ 'NP_ENABLED' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_ENABLED' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'NP_ENABLED' ]
			) );
		}
	}
}
new NexoPlayGroundMain;