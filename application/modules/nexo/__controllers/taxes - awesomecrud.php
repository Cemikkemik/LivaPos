<?php
class Nexo_Taxes_Controller extends Tendoo_Module
{
    public function index()
    {
        $crud   =   new Awesome_Crud([
            'table'         =>   store_prefix() . 'nexo_taxes',
            'baseUrl'       =>  site_url([ 'dashboard', store_slug(), 'nexo_taxes' ]),
            'listUrl'       =>  'index',
            'primaryKey'    =>  'ID'
        ]);

        $crud->columns([
            'NAME'              =>  __( 'Nom de la taxe', 'nexo' ),
            'PERCENTAGE'        =>  __( 'Taux', 'nexo' ),
            'DATE_CREATION'     =>  __( 'Date Création', 'nexo' ),
            'AUTHOR'            =>  __( 'Auteur', 'nexo' ),
            'DESCRIPTION'       =>  __( 'Description', 'nexo' )
        ]);

        $crud->fields([ 
            'NAME'          =>  'string|required',
            'PERCENTAGE'    =>  'numeric|required',
            'DESCRIPTION'   =>  'textarea'
        ]);

        foreach([ 'AUTHOR', 'DESCRIPTION', 'PERCENTAGE', 'DATE_CREATION', 'AUTHOR' ] as $column ) {
            $config             =   [
                'filters'    =>  'enablePlaceholders'
            ];

            // specific to AUTHOR
            if( in_array( $column, [ 'AUTHOR', 'DATE_CREATION' ])  ) {
                $config[ 'width' ]      =   '150px';
            }

            // specific to AUTHOR
            if( in_array( $column, [ 'PERCENTAGE' ])  ) {
                $config[ 'width' ]      =   '100px';
                $config[ 'description' ]    =   __( 'Définir le taux d\'imposition en pourcentage.', 'nexo' );
            }

            // 
            if( $column == 'PERCENTAGE' ) {
                $config[ 'filters' ]  .=    ',isPercentage';
            }

            // for description
            if( $column == 'DESCRIPTION' ) {
                $config[ 'description' ]    =   __( 'Ajouter des détails sur la taxe.', 'nexo' );
                $config[ 'hideColumn' ]     =   true;
            }

            $crud->config( $column, $config);
        }
        

        $crud_data      =   $crud->render([ 'return' => true ]);

        $this->load->module_view( 'nexo', 'taxes.list', [
            'crud_data'     =>  $crud_data
        ]);
    }

    /**
     * Add Taxes
    **/

    public function add()
    {
        $this->Gui->set_title( store_title( 'Ajouter une taxe', 'nexo' ) );
        $this->index();
    }
}