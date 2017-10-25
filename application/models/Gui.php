<?php defined('BASEPATH') or exit('No direct script access allowed');

use Pecee\SimpleRouter\SimpleRouter as Route;
use Pecee\Handlers\IExceptionHandler;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

class Gui extends CI_Model
{
    public $cols    				=    array(
        1            =>    array(),
        2            =>    array(),
        3            =>    array(),
        4            =>    array(),
    );

    private $created_page   		=    array();
	private $created_page_objet		=	array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page for dashboard
     * @param string Page Slug
     * @param Function
     * @return void
    **/

    public function register_page($page_slug, $function)
    {
        $this->created_page[ $page_slug ]    =    array(
            'page-slug'        =>    $page_slug,
            'function'        =>    $function
        );
    }

	/**
	 * Regsiter Page Object
	 * @param string page slug
	 * @param obj page obj
	 * return void
	**/

	public function register_page_object($page_slug, $obj)
    {
        $this->created_page_objet[ $page_slug ]    =    array(
            'page-slug' 	=>    $page_slug,
            'object'       	=>    $obj
        );
    }

    /**
     * Load created page
     * @param String page slug
     * @param Array params
    **/

    public function load_page($page_slug, $params)
    {
        // load created pages
        // $this->events->do_action_ref_array('create_dashboard_pages', $params); // ??

        global $Route;
        
        $Route          =   new Route();
        
        $Route->group([ 'prefix' => substr( request()->getHeader( 'script-name' ), 0, -10 ) . '/dashboard' ], function() use ( $page_slug ) {
            $modules        =   Modules::get();
            foreach( $modules as $namespace => $module ) {
                if( is_dir( $dir = MODULESPATH . $namespace . '/controllers/' ) ) {
                    foreach( glob( $dir . "*.php") as $filename) {
                        include_once( $filename );
                    }
                }
    
                if( is_file( MODULESPATH . $namespace . '/routes.php' ) ) {
                    include_once( MODULESPATH . $namespace . '/routes.php' );
                }
            }
        });

        $Route->error(function($request, \Exception $exception) {
            if($exception instanceof NotFoundHttpException && $exception->getCode() == 404) {
                Html::set_title(sprintf(__('Error : 404 &mdash; %s'), get('core_signature')));
                Html::set_description(__('Error page'));
                $this->load->view('dashboard/error/404');
            }
        });
        
        $Route->start();
    }

    /**
     * Page title
     * @string Page Title
    **/

    public function set_title($title)
    {
        Html::set_title($title);
    }

    /**
     * New Gui
    **/
    /**
     * Set cols width
     *
     * col_id should be between 1 and 4. Every cols are loaded even if they width is not set
     * @access : public
     * @param : int cold id
     * @param : int width
     * @return : void
    **/

    public function col_width($col_id, $width)
    {
        if (in_array($col_id, array( 1, 2, 3, 4 ))) {
            $this->cols[ $col_id ][ 'width' ]    =    $width;
        }
    }

    /**
     * Get Col
     *
     * @param int Col Id
     * @return bool
    **/

    public function get_col($col_id)
    {
        return riake($col_id, $this->cols);
    }

    /**
     * Add Meta to gui
     *
     * @access public
     * @param string/array namespace, config array
     * @param string meta title
     * @param string meta type
     * @param int col id
     * @return void
    **/

    public function add_meta($namespace, $title = 'Unamed', $type = 'box-default', $col_id = 1)
    {
        if (in_array($col_id, array( 1, 2, 3, 4 ))) {
            if (is_array($namespace)) {
                $rnamespace            =    riake('namespace', $namespace);
                $col_id                =    riake('col_id', $namespace);
                $title                =    riake('title', $namespace);
                $type                =    riake('type', $namespace);

                foreach ($namespace as $key => $value) {
                    $this->cols[ $col_id ][ 'metas' ][ $rnamespace ][ $key ]    =    $value;
                }
            } else {
                $this->cols[ $col_id ][ 'metas' ][ $namespace ]    =    array(
                    'namespace'        =>    $namespace,
                    'type'            =>    $type,
                    'title'            =>    $title
                );
            }
        }
    }

    /**
     * Add Item
     * Add item meta box
     *
     * @param Array Config
     * @param String meta namespace
     * @param int Col id
     * @return void
    **/

    public function add_item($config, $metanamespace, $col_id)
    {
        if (in_array($col_id, array( 1, 2, 3, 4 )) && riake('type', $config)) {
            $this->cols[ $col_id ][ 'metas' ][ $metanamespace ][ 'items' ][]    =    $config;
        }
    }

    /**
     * Output
     * Output GUI content
     * @return void
    **/

    public function output()
    {
        $this->load->view('dashboard/header');
        $this->load->view('dashboard/horizontal-menu');
        $this->load->view('dashboard/aside');
        $this->load->view('dashboard/gui/body', array(
            'page_header'    =>    $this->load->view('dashboard/gui/page-header', array(), true),
            'cols'            =>    $this->cols
        ));
        $this->load->view('dashboard/footer');
        $this->load->view('dashboard/aside-right');
    }

    /**
     * 	Get GUI cols
     *	@access		:	Public
     *	@returns	:	Array
    **/

    public function get_cols()
    {
        return $this->cols;
    }

    /**
     * Allow Gui customization.
     *
     * @access public
     * @param mixed
     * @return void
    **/

    public function config($config)
    {
        $this->config    =    $config;
    }
}
