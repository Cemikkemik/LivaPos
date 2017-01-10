<?php
class AngularCrudLibrary
{
    public $limit               =   [ 5, 10, 20, 50, 100, 250 ];
    public $searchLabel;
    public $fieldsType          =   [];
    public $selectOptions       =   [];
    private $showOnListOnly     =   [ 'ID', 'id', '$AnguCrudActions' ];
    private $columns            =   [];
    public $baseUrl             =   '#';
    public $uriSegments         =   null;
    private $relations          =   [];
    private $matching           =   [];
    private $validations         =   [];
    private $fieldDescription   =   [];

    /**
     *  Definition
     *  @param
     *  @return
    **/

    public function __construct( $table_name )
    {
        $this->table_name       =   $table_name;
        $this->rest_url         =   site_url( array( 'rest', 'angular', $table_name, ':id' ) );
        $this->searchLabel      =   __( 'Search', 'angular_material' );
        $this->editLabel        =   __( 'Edit', 'angular_material' );
        $this->crudTitle        =   __( 'Unamed Crud', 'angular_material' );
        $this->itemLabel        =   __( 'Item', 'angular_material' );
        $this->itemsLabel       =   __( 'Items', 'angular_material' );
        $this->crudAddNewLabel  =   __( 'Add new item', 'angular_material' );
        $this->primaryCol       =   'ID';
        $this->entriesPerPage   =   10;
        $this->ofLabel          =   __( 'Of', 'angular_material' );
        $this->pageLabel        =   __( 'Page', 'angular_material' );
        $this->rowsPerPageLabel =   __( 'Rows', 'angular_material' );
        $this->addNewLabel      =   __( 'Add New', 'angular_material' );
        $this->showListLabel    =   __( 'Go back to list', 'angular_material' );
        $this->deleteSingleTextLabel        =   __( 'Would you delete this ?', 'angular_material' );
        $this->deleteBulkTextLabel          =   __( 'Would you delete these entries', 'angular_material' );
        $this->deleteSingleTitleLabel       =   __( 'Confirm your action', 'angular_material' );
        $this->exportToCSV      =   __( 'Export', 'angular_material' );
        $this->searchSelectLabel    =   __( 'Search', 'angular_material' );
    }

    /**
     *  Configuration
     *  @param array
     *  @return object
    **/

    public function config( $config )
    {
        foreach( $config as $key => $conf ){
            $this->$key =   $conf;
        }
        return $this;
    }

    /**
     *  Field Description
     *  @param string field name
     *  @return array
    **/

    public function getFieldDescription( $field = null )
    {
        if( $field != null ) {
            return @$this->fieldDescription[ $field ];
        }
        return $this->fieldDescription;
    }

    /**
     *  Get Field Type
     *  @param string field name
     *  @return array / string
    **/

    public function getFieldType( $field = null )
    {
        if( $field != null ) {
            return @$this->fieldsType[ $field ];
        }
        return $this->fieldsType;
    }

    /**
     *  Load Script
     *  @param
     *  @return
    **/

    public function loadScript()
    {
        get_instance()->load->module_view( 'angular_material', 'angular-script', [
            'table_name'    =>  $this->table_name,
            'relations'     =>  $this->relation_options
        ]);
    }

    /**
     *  Load View
     *  @param
     *  @return
    **/

    public function LoadView()
    {
        /**
         * Load Joined table content
         * this will only be available when a dropdown menu is set as fieldsType
        **/

        $this->relation_options        =   [];
        if( $this->getRelations() ) {
            foreach( $this->getRelations() as $field => $relation ) {
                $entryList      =   get_instance()->db->get( $relation[ 'table' ] )->result_array();
                $entryOptions   =   [];
                foreach( $entryList as $entry ) {
                    $entryOptions[ $entry[ $relation[ 'comparison' ] ] ] =   $entry[ $relation[ 'col' ] ];
                }
                $this->relation_options[ $field ]           =   $entryOptions;
                $this->matching[ $relation[ 'alias' ] ]     =   $field;
            }
        }

        get_instance()->events->add_filter( 'gui_page_title', '__return_false' );
        get_instance()->events->add_action( 'dashboard_footer', [ $this, 'loadScript']);
        if( $this->page == 'list' ) {
            get_instance()->Gui->set_title( sprintf( __( '%s - List', 'angular_material' ), $this->crudTitle ) );
            get_instance()->load->module_view( 'angular_material', 'basic', [
                'AnguCrud'  =>  $this,
                'view'      =>  'main'
            ]);
        } else if( $this->page == 'add_new' ) {
            get_instance()->Gui->set_title( sprintf( __( '%s - Add New', 'angular_material' ), $this->crudTitle ) );
            get_instance()->load->module_view( 'angular_material', 'basic', [
                'AnguCrud'  =>  $this,
                'view'      =>  'add_new'
            ]);
        } else if( $this->page == 'edit' ) {
            get_instance()->Gui->set_title( sprintf( __( '%s - Edit', 'angular_material' ), $this->crudTitle ) );
            get_instance()->load->module_view( 'angular_material', 'basic', [
                'AnguCrud'  =>  $this,
                'view'      =>  'edit'
            ]);
        } else if( $this->page == 'export_xls' ) {

            include_once( LIBPATH . '/Excel.php' );

            /**
             * No need to use an external library here. The only bad thing without using external library is that Microsoft Excel is complaining
             * that the file is in a different format than specified by the file extension. If you press "Yes" everything will be just fine.
             * */

             $filename  =    "export-".date("Y-m-d_H:i:s");

             $Excel     =   new Excel( $filename );

             $Excel->home();

             foreach ( $this->columns as $key => $column) {
                 if( $key != '$AnguCrudActions' ) {
                     $Excel->label( $column );
                     $Excel->right();
                 }
             }

             $Excel->down();

             foreach ($data->list as $num_row => $row) {
                 $Excel->home();
                 foreach ($data->columns as $key => $column) {
                      $Excel->label( $this->_trim_export_string($row->{$column->field_name}) );
                      $Excel->right();
                 }
                 $Excel->down();
             }

            // $Excel->send();

            // die();
        }

    }

    /**
     *  Get Matching
     *  @param
     *  @return
    **/

    public function getMatching()
    {
        return $this->matching;
    }

    /**
     *  get primary Col
     *  @param
     *  @return
    **/

    public function getPrimaryCol()
    {
        return $this->primaryCol;
    }

    /**
     *  Get Relation
     *  @param
     *  @return
    **/

    public function getRelations()
    {
        return $this->relations;
    }

    /**
     *  getShowOnListOnly
     *  @param
     *  @return
    **/

    public function getShowOnListOnly()
    {
        return $this->showOnListOnly;
    }

    /**
     *  Get Validations
     *  @param
     *  @return
    **/

    public function getValidations( $key = null )
    {
        if( $key != null ) {
            return ( array ) @$this->validations[ $key ];
        }
        return $this->validations;
    }

    /**
     *  Create Columns
     *  @param array columns
     *  @return object
    **/

    public function setColumns( $data )
    {
        $data[ '$AnguCrudActions' ]     =   __( 'Actions', 'angular_material' );
        $this->columns  =   $data;
        return $this;
    }

    /**
     *  Set Relation
     *  @param array relation array
     *  @return void
    **/

    public function setRelation( $relation_array )
    {
        $this->relations     =   $relation_array;
    }

    /**
     *  Set Primary Col
     *  @param string
     *  @return
    **/

    public function setPrimaryCol( $col )
    {
        $this->primaryCol       =   $col;
    }

    /**
     *  Set Show on List Only
     *  @param
     *  @return
    **/

    public function setShowOnListOnly( $options )
    {
        $this->showOnListOnly   =   array_merge( $this->showOnListOnly, $options );
    }

    /**
     *  Get Columns
     *  @param
     *  @return
    **/

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *  Get Select Options
     *  @return void
    **/

    public function getSelectOptions()
    {
        return $this->selectOptions;
    }

}
