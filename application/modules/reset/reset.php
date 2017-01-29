<?php
! defined( 'APPPATH' ) ? die() : NULL;

class Tendoo_Reset extends Tendoo_Module
{
	function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'load_dashboard', array( $this, 'reset_table' ) );
	}

	/**
	 * Reset table
	 * @return void
	**/

	function reset_table()
	{
		$this->SimpleFileManager	=	new SimpleFileManager;

		$this->SimpleFileManager->drop( APPPATH . 'cache/app' );
		mkdir( APPPATH . 'cache/app' );
		$this->SimpleFileManager->file_copy( APPPATH . 'index.html', APPPATH . 'cache/app/index.html' );

		$this->SimpleFileManager->drop( APPPATH . 'cache/sessions' );
		mkdir( APPPATH . 'cache/sessions' );
		$this->SimpleFileManager->file_copy( APPPATH . 'index.html', APPPATH . 'cache/sessions/index.html' );

		unlink( APPPATH . '/config/database.php' );
		$this->load->dbforge();

		$this->dbforge->drop_database( $this->db->database );
		$this->dbforge->create_database( $this->db->database );

		redirect( array( 'do-setup' ) );
	}
}
new Tendoo_Reset;
