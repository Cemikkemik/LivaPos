<?php
class Nexo_Stores extends CI_Model
{
	/**
	 * Construct
	**/

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get Store
	 * @param int store id
	 * @return array
	**/

	public function get( $id = null, $filter = 'ID' )
	{
		if( $id != null ) {
			$this->db->where( $filter, $id );
		}

		return $this->db->get( 'nexo_stores' )->result_array();
	}

	/**
	 * Insert Store
	**/

	public function __insert_store( $post )
	{
		$post[ 'DATE_CREATION' ]	=	date_now();
		$post[ 'AUTHOR' ]			=	User::id();

		return $post;
	}

	/**
	 * Callback after insert
	**/

	public function __callback_after_insert( $post, $id )
	{
		$install	=	new Nexo_Install;

		$install->install_tables( 'store', 'store_' . $id . '_' );

		$this->options->set( 'store_' . $id . '_site_name', $post[ 'NAME' ], true );
		$this->options->set( 'store_' . $id . '_nexo_enable_autoprint', 'yes', true );
		$this->options->set( 'store_' . $id . '_nexo_enable_smsinvoice', 'yes', true );
		$this->options->set( 'store_' . $id . '_nexo_devis_expiration', 7, true );
		$this->options->set( 'store_' . $id . 'nexo_compact_enabled', 'yes', true );
		$this->options->set( 'store_' . $id . 'nexo_enable_registers', 'no', true );
		$this->options->set( 'store_' . $id . 'nexo_enable_vat', 'no', true );

		@mkdir( PUBLICPATH . '/upload/store_' . $id );
		@mkdir( PUBLICPATH . '/upload/store_' . $id . '/customers' );
		@mkdir( PUBLICPATH . '/upload/store_' . $id . '/codebar' );
		@mkdir( PUBLICPATH . '/upload/store_' . $id . '/categories' );
		@mkdir( PUBLICPATH . '/upload/store_' . $id . '/items-images' );
	}

	/**
	 * Update Store
	**/

	public function __update_store( $post )
	{
		$post[ 'DATE_MOD' ]			=	date_now();
		$post[ 'AUTHOR' ]			=	User::id();

		return $post;
	}

	/**
	 * Delete Store
	**/

	public function __delete_store( $store_id )
	{
		$store		=	$this->get( $store_id );
		$prefix		=	'store_' . $store_id . '_';

		// $this->load->library( 'SimpleFileManager' );
		SimpleFileManager::drop( PUBLICPATH . '/upload/store_' . $store_id );

		$this->events->do_action( 'delete_nexo_store', array( $store_id, $store, $prefix ) );

		// Delete all tables
		$Install	=	new Nexo_Install;
		$Install->uninstall( 'nexo', 'store', $prefix );

		// Delete All store options
		$this->db->like( 'key', 'store_' . $store_id . '_' )->delete( 'options' );

		return $store_id;
	}
}
