<?php 
class AwesomeCrud_Controller extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
     } 

     public function index()
     {
          $this->crud    =    new Awesome_Crud([
               'baseUrl'      =>   current_url(),
               'table'        =>   'nexo_articles'
          ]);

          $this->crud->columns([
               'DESIGN'       =>   __( 'Nom' ),
               'PRIX_DE_VENTE'     =>   __( 'Prix de vente' )
          ]);

          return $this->crud->render();
     }
}