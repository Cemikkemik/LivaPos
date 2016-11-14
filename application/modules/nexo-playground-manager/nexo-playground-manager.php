<?php

include_once( dirname( __FILE__ ) . '/inc/controller.php' );

class NexoPlayGroundMain extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
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

			$field_hours	=	new stdClass;
			$field_hours->field_name		=	'NP_HOURS';
			$field_hours->display_as		=	__( 'Hours', 'nexo-playground-manager' );
			$field_hours->description		=	tendoo_info( __( 'You can set how many hours you would like to add to the order.', 'nexo-playground-manager' ) );

			$fields[]	=	$field_hours;

			$field_time	=	new stdClass;
			$field_time->field_name		=	'NP_MINUTES';
			$field_time->display_as		=	__( 'Minutes', 'nexo-playground-manager' );
			$field_time->description	=	tendoo_info( __( 'You can set how many minutes you would like to add to the order.', 'nexo-playground-manager' ) );

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
				'href'		=>	site_url( array( 'dashboard', store_slug(), 'nexo-playground-manager', 'manager' ) ),
				'icon'		=>	'fa fa-paw'
			)
		) );

		return $array ? $array : $menus;
	}

	/**
	 * Controller Callback
	**/

	public function controller_callback( $controllers )
	{
		$controllers[ 'nexo-playground-manager' ]	=	$this->Controller;
		return $controllers;
	}

	/**
	 * Crud Load
	**/

	public function crud_load( $crud )
	{
		$crud->add_group( 'np_options', __( 'PlayGround Options', 'nexo-playground-manager' ), array( 'NP_MINUTES', 'NP_ENABLED', 'NP_HOURS' ), 'fa-star' );
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

			  /**
			   * Hours
			  **/

			  $NP_HOURS		=		null;
			  if( $id ) {
				  $data		=		$this->db->where( 'REF_ARTICLE', $id )
				  ->where( 'KEY', 'NP_HOURS' )
				  ->get( store_prefix() . 'nexo_articles_meta' )
				  ->result_array();

				  $NP_HOURS	=		@$data[0][ 'VALUE' ];
			  }

			  ob_start();
			  ?>
			  <select type="text" class="form-control" id="" placeholder="" id="field-NP_HOURS"  name="NP_HOURS">
				  <?php for( $i = 0; $i <= 24; $i++ ):?>
					  <option value="<?php echo $i;?>" <?php echo $NP_HOURS == $i ? 'selected="selected"' : '';?>><?php echo $i;?></option>
				  <?php endfor;?>
			  </select>
			  <?php

			  $dom			=	ob_get_clean();

			  $input_fields[ 'NP_HOURS' ]						=	new stdClass;
			  $input_fields[ 'NP_HOURS' ]->name				=	'NP_HOURS';
			  $input_fields[ 'NP_HOURS' ]->type				=	'varchar';
			  $input_fields[ 'NP_HOURS' ]->max_length			=	200;
			  $input_fields[ 'NP_HOURS' ]->primary_key		=	0;
			  $input_fields[ 'NP_HOURS' ]->default			=	null;
			  $input_fields[ 'NP_HOURS' ]->db_max_length		=	11;
			  $input_fields[ 'NP_HOURS' ]->db_type			=	'varchar';
			  $input_fields[ 'NP_HOURS' ]->db_null			=	false;
			  $input_fields[ 'NP_HOURS' ]->required			=	true;
			  $input_fields[ 'NP_HOURS' ]->display_as			=	__( 'Hours', 'nexo-playground-manager' );
			  $input_fields[ 'NP_HOURS' ]->crud_type			=	false;
			  $input_fields[ 'NP_HOURS' ]->extras				=	false;
			  $input_fields[ 'NP_HOURS' ]->input				=	$dom;

			// Minutes

			$NP_MINUTES		=		null;
			if( $id ) {
				$data		=		$this->db->where( 'REF_ARTICLE', $id )
				->where( 'KEY', 'NP_MINUTES' )
				->get( store_prefix() . 'nexo_articles_meta' )
				->result_array();

				$NP_MINUTES	=		@$data[0][ 'VALUE' ];
			}

			ob_start();
			?>
			<select type="text" class="form-control" id="" placeholder="" id="field-NP_MINUTES"  name="NP_MINUTES">
				<?php for( $i = 0; $i <= 59; $i++ ):?>
					<option value="<?php echo $i;?>" <?php echo $NP_MINUTES == $i ? 'selected="selected"' : '';?>><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<?php
			$np_minutes			=	ob_get_clean();

			$input_fields[ 'NP_MINUTES' ]					=	new stdClass;
			$input_fields[ 'NP_MINUTES' ]->name				=	'NP_MINUTES';
			$input_fields[ 'NP_MINUTES' ]->type				=	'varchar';
			$input_fields[ 'NP_MINUTES' ]->max_length		=	200;
			$input_fields[ 'NP_MINUTES' ]->primary_key		=	0;
			$input_fields[ 'NP_MINUTES' ]->default			=	null;
			$input_fields[ 'NP_MINUTES' ]->db_max_length	=	11;
			$input_fields[ 'NP_MINUTES' ]->db_type			=	'varchar';
			$input_fields[ 'NP_MINUTES' ]->db_null			=	false;
			$input_fields[ 'NP_MINUTES' ]->required			=	true;
			$input_fields[ 'NP_MINUTES' ]->display_as		=	__( 'Minutes', 'nexo-playground-manager' );
			$input_fields[ 'NP_MINUTES' ]->crud_type		=	false;
			$input_fields[ 'NP_MINUTES' ]->extras			=	false;
			$input_fields[ 'NP_MINUTES' ]->input			=	$np_minutes;
		}

		return $input_fields;
	}

	/**
	 * Load Dashboard
	**/

	public function load_dashboard()
	{
		$Nexo    =    Modules::get('nexo');

        // If Nexo exists

        if (! $Nexo) {
            $this->notice->push_notice( tendoo_warning( __( 'NexoPlayGround Manager require NexoPOS module.', 'nexo_premium')));
            return false;
        }

        $isActive =     Modules::is_active( 'nexo' );

        if( ! $isActive ) {
            $this->notice->push_notice( tendoo_warning( __( 'NexoPlayGround Manager require NexoPOS module to be enabled.', 'nexo_premium')));
            return false;
        }

		$this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ), 20 );
		// $this->events->add_filter( 'grocery_get_add_fields', array( $this, 'add_fields' ) );
		$this->Controller		=	new NexoPlayGroundController;
		$this->events->add_filter( 'stores_controller_callback', array( $this, 'controller_callback' ) );
		$this->events->add_filter( 'grocery_registered_fields', array( $this, 'add_fields' ) );
		$this->events->add_filter( 'grocery_edit_fields', array( $this, 'add_fields' ) );
		$this->events->add_filter( 'grocery_input_fields', array( $this, 'input_fields' ) );
		$this->events->add_action( 'nexo_after_save_product', array( $this, 'save_item' ), 10, 2 );
		$this->events->add_action( 'nexo_after_update_product', array( $this, 'update_item' ), 10, 2 );
		$this->events->add_filter( 'product_required_fields', array( $this, 'required_fields' ) );
		$this->events->add_filter( 'load_product_crud', array( $this, 'crud_load' ) );

		$this->enqueue->js( 'moment.min', module_url( 'nexo-playground-manager' )	. 'bower_components/moment/min/' );
		$this->enqueue->js( 'countdown', module_url( 'nexo-playground-manager' )	. 'bower_components/countdown/dest/' );

		$this->Gui->register_page_object( 'nexo_playground_manager', $this->Controller );
	}

	/**
	 * Required field
	**/

	public function required_fields( $fields )
	{
		$fields[]	=	'NP_MINUTES';
		$fields[]	=	'NP_HOURS';
		$fields[]	=	'NP_ENABLED';
		return $fields;
	}

	/**
	 * While Saving item
	**/

	public function save_item( $array, $id )
	{
		$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_MINUTES' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'NP_MINUTES',
			'VALUE'				=>	$array[ 'NP_MINUTES' ],
			'REF_ARTICLE'		=>	$id
		) );

		$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_HOURS' )
		->insert( store_prefix() . 'nexo_articles_meta', array(
			'DATE_CREATION'		=>	date_now(),
			'KEY'				=>	'NP_HOURS',
			'VALUE'				=>	$array[ 'NP_HOURS' ],
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
		->where( 'KEY', 'NP_MINUTES' )
		->get( store_prefix() . 'nexo_articles_meta' );

		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_MINUTES' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'NP_MINUTES',
				'VALUE'				=>	$array[ 'NP_MINUTES' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_MINUTES' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'NP_MINUTES' ]
			) );
		}

		$query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_ENABLED' )
		->get( store_prefix() . 'nexo_articles_meta' );

		// Hours
		$query		=	$this->db->where( 'REF_ARTICLE', $id )
		->where( 'KEY', 'NP_HOURS' )
		->get( store_prefix() . 'nexo_articles_meta' );

		if( ! $query->result_array() ) {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_HOURS' )
			->insert( store_prefix() . 'nexo_articles_meta', array(
				'DATE_CREATION'		=>	date_now(),
				'KEY'				=>	'NP_HOURS',
				'VALUE'				=>	$array[ 'NP_HOURS' ],
				'REF_ARTICLE'		=>	$id
			) );
		} else {
			$this->db->where( 'REF_ARTICLE', $id )
			->where( 'KEY', 'NP_HOURS' )
			->update( store_prefix() . 'nexo_articles_meta', array(
				'DATE_MOD'		=>	date_now(),
				'VALUE'			=>	$array[ 'NP_HOURS' ]
			) );
		}

		// Enabled

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
