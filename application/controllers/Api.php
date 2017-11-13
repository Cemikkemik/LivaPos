<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Pecee\SimpleRouter\SimpleRouter as Route;
use Pecee\Handlers\IExceptionHandler;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

class Api extends Tendoo_Controller
{
	public function __construct()
	{
		parent::__construct();
		// var_dump( get_instance() );
		// get_instance()->load->helper( 'request_helper' );
	}
	
	/**
	* Index for API
	* @return void
	*/
	public function index( $page_slug ) 
	{
		global $Route;
		
		$Route          =   new Route();
		
		$Route->group([ 'prefix' => substr( request()->getHeader( 'script-name' ), 0, -10 ) . '/api' ], function() use ( $page_slug, $Route ) {
			$modules        =   Modules::get();
			foreach( $modules as $namespace => $module ) {

				if( ! Modules::is_active( $namespace ) ) {
					continue;
				}

				if( is_dir( $dir = MODULESPATH . $namespace . '/api/' ) ) {
					foreach( glob( $dir . "*.php") as $filename) {
						include_once( $filename );
					}
				}

				if( is_file( MODULESPATH . $namespace . '/api.php' ) ) {
					include_once( MODULESPATH . $namespace . '/api.php' );
				}
			}

			// include_once( dirname( __FILE__ ) . '/TendooWidgetsController.php' );
			// // System Routes
			// $Route->post( '/widgets', 'TendooWidgetsController@index' );
			// $Route->get( '/widgets/sample', 'TendooWidgetsController@sample' );
			// $Route->get( '/widgets/foo', 'TendooWidgetsController@foo' );
		});

		$Route->error(function($request, \Exception $exception) {
			if($exception instanceof NotFoundHttpException && $exception->getCode() == 404) {
				return response()->httpCode( 404 )->json([
					'status'  	=>   'failed',
					'message' 	=>   $exception->getMessage()
				], 404 );
			}
		});
		
		$Route->start();    
	}
}