<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AngularMaterialController extends Tendoo_Module
{
    /**
     *  Index Page
     *  @return void
    **/

    public function index( $page  =  'list' )
    {
        $Crud               =   new AngularCrudLibrary( 'nexo_articles' );

        $Crud->setColumns([
            'ID'            =>  __( 'Id', 'nexo' ),
            'DESIGN'        =>  __( 'Désignation', 'nexo' ),
            'SKU'           =>  __( 'Unité de Gestion de Stock', 'nexo' ),
            'CODEBAR'       =>  __( 'Code Barre', 'nexo' ),
            'PRIX_DE_VENTE' =>  __( 'Prix de vente', 'nexo' ),
            'PRIX_DACHAT'   =>  __( 'Prix d\'achat', 'nexo' ),
            'PSEUDO'        =>  __( 'Auteur', 'nexo' ),
            'QUANTITY'      =>  __( 'Quantité', 'nexo' ),
            'CATEGORY_NAME' =>  __( 'Categorie', 'nexo' ),
            // 'DATE_CREATION' =>  __( 'Crée le', 'nexo' ),
            'PSEUDO'        =>  __( 'Auteur', 'nexo' )
        ]);

        $Crud->setRelation([
            'AUTHOR'        =>  [
                'table'     =>  'aauth_users',
                'col'       =>  'name',
                'comparison'=>  'id',
                'alias'     =>  'PSEUDO'
            ],
            'REF_CATEGORIE' =>  [
                'table'     =>  'nexo_categories',
                'col'       =>  'NOM',
                'comparison'    =>  'ID',
                'alias'     =>  'CATEGORY_NAME',
            ]
        ]);

        $Crud->config([
            'baseUrl'           =>  site_url( array( 'dashboard', 'angular', 'index' ) ),
            'page'              =>  $page,
            'crudTitle'         =>  __( 'Articles', 'nexo' ),
            'fieldsType'        =>  [
                'DATE_CREATION' =>  'datetime',
                'PSEUDO'        =>  'select_relation',
                'CATEGORY_NAME' =>  'select_relation',
            ],
            'validations'       => [
                'SKU'           =>  [ 'required' ],
            ]
        ]);

        return $Crud->LoadView();
    }
}
