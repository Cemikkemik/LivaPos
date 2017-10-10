<?php
class Awesome_Crud
{
    private $baseUrl   =   '';
    private $table;
    private $listSlug;
    private $addSlug;
    private $editSlug;
    private $id;
    private $isValid    =   false;
    private $slug_files     =   [
        'listSlug'     =>  'list',
        'editSlug'     =>  'edit',
        'deleteSlug'   =>  'delete',
        'addSlug'      =>  'add'
    ];
    private $config         =   [];
    private $showwActions   =   true;
    private $showCheckboxes     =   true;
    private $primaryKey     =   'id';
    private $fields;

    public function __construct( $config )
    {
        extract( $config );

        if( isset( $baseUrl ) ) {
            $this->baseUrl          =   $baseUrl;
            $this->table            =   $table;
            $this->listSlug         =   @$listSlug ? $listSlug : 'list';
            $this->addSlug          =   @$addSlug ? $addSlug : 'add';
            $this->editSlug         =   @$editSlug ? $editSlug : 'edit';
            $this->deleteSlug       =   @$deleteSlug ? $deleteSlug : 'delete';
            $this->name             =   @$name  ? $name : 'Unamme Crud';
            $this->showActions      =   @$showActions ? $showActions : true;
            $this->primaryKey       =   @$primaryKey ? $primaryKey  :   'id';
        }

        // detecting the current UI
        $url    =   current_url();
        if( preg_match( '/' . str_replace( '/', '\/', $this->baseUrl ). '/', $url ) ) {
            $params  =   explode( $this->baseUrl, $url );
            if( count( $params ) > 1 ) {
                $slugs  =   explode( '/', $params[1] );

                // if the current screen can't be detected, we assume it's the index
                $this->current_screen   =   in_array( @$slugs[1], [ $this->listSlug, $this->addSlug, $this->editSlug, $this->deleteSlug ] ) ? $slugs[1] : $this->listSlug;
                $this->page_index       =   @$slugs[1] ? intval( $slugs[1] ) : 1; // if index/page is not set we assume it's the first page.
            } else {
                $this->current_screen   =   $this->listSlug;
                $this->page_index       =   1;
            }            
        }

        // avoid multiple run
        global $hasRun;

        if( $hasRun == true ) {
            return;
        }
        // add angular dependencies
        get_instance()->events->add_filter( 'dashboard_dependencies', function( $deps ) {
            $deps[]     =   'ngResource';
            $deps[]     =   'angular-icheck'; // icheck directive
            return $deps;
        });

        // add resource dependencies
        get_instance()->enqueue->js( '../bower_components/angular-resource/angular-resource.min' );
        get_instance()->enqueue->js( '../plugins/iCheck/icheck.min' );
        get_instance()->enqueue->css( '../plugins/iCheck/flat/blue' );
        get_instance()->enqueue->js( 'js/icheck-directive', module_url( 'awesome-crud' ) );
        get_instance()->enqueue->css( 'css/icheck-style', module_url( 'awesome-crud' ) );
        get_instance()->enqueue->css( '../bower_components/sweetalert/dist/sweetalert' );
        get_instance()->enqueue->js( '../bower_components/sweetalert/dist/sweetalert.min' );
    }

    /**
     * Add Columns
     * @param array
     * @return void
    **/

    public function columns( $config ) 
    {
        $this->columns   =   $config;
    }

    /**
    * Ge Columns
    * @return array
    **/

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add fields
     * @param array
     * @return void
    **/

    public function fields( $fields ) 
    {
        $this->fields   =   $fields;
    }

    /**
     * Render
     *
    **/

    public function render( $options = [] )
    {
        extract( $options );
        if( in_array( $this->current_screen, [ $this->listSlug, $this->deleteSlug, $this->editSlug, $this->addSlug ] ) ) {
            $file_name           =   'list';
            foreach( $this->slug_files as $key => $file ) {
                if( $this->$key == $this->current_screen ) {
                    $file_name   =   $file;
                }
            }

            if( $file_name == 'list' ) {
                $obj    =   $this;
                get_instance()->events->add_action( 'dashboard_footer', function() use ( $obj ){
                    get_instance()->load->module_view( 'awesome-crud', 'list-script', [
                        'awesome_crud'  =>  $obj
                    ]);

                    get_instance()->load->module_view( 'awesome-crud', 'crud-resource', [
                        'awesome_crud'  =>  $obj
                    ]);
                });
            }

            if( @$return === true ) {
                return get_instance()->load->module_view( 'awesome-crud', $file_name, [
                    'crud_index'    =>  $this->page_index,
                    'awesome_crud'  =>  $this
                ], true );
            }

            return get_instance()->load->module_view( 'awesome-crud', $file_name, [
                'crud_index'    =>  $this->page_index,
                'awesome_crud'  =>  $this
            ]);
        }
    }

    /**
     * Get 
     * @return any
    **/

    public function get( $param )
    {
        return @$this->$param ? $this->$param : null;
    }

    /**
     * Config. Use it before Render
     * @param string column namespace
     * @param array 
     * @return void
    **/

    public function config( $namespace, $config )
    {
        $this->config[ $namespace ]     =   $config;
    }

    /**
     * Get Config
     * @param string column namespace
     * @return array
    **/

    public function getConfig( $namespace )
    {
        return @$this->config[ $namespace ];
    }
}