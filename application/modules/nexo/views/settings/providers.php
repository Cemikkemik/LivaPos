<?php
$this->Gui->col_width(1, 2);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nexo_providers',
     'gui_saver' =>  true,
     'footer'    =>  [
         'submit'    =>  [
             'label' =>  __( 'Sauvegarder', 'nexo' )
         ]
     ]
));

$this->Gui->add_item( array(
     'type' =>    'select',
     'options'      =>   [
          ''        =>   __( 'Veuillez choisir une option', 'nexo' ),
          'yes'     =>   __( 'Oui', 'nexo' ),
          'no'      =>   __( 'Non', 'nexo' ),
     ],
     'name' =>	store_prefix() . 'enable_providers_account',
     'label' =>   __( 'Activer l\'historique des fournisseurs', 'nexo' ),
     'description' =>   __( 'Cette fonctionnalité activera les comptes créditeurs pour les fournisseurs. Par défaut cette fonctonnalité est désactivée.', 'nexo' ),
     'placeholder' =>   ''
), 'nexo_providers', 1 );

$rawCategories      =   $this->db->get( store_prefix() . 'nexo_premium_factures_categories' )
->result_array();

$options            =   [];

$options            =   [
    ''              =>  __( 'Veuillez choisir une option', 'nexo' )
];

foreach( $rawCategories as $category ) {
    $options[ $category[ 'ID' ] ]       =   $category[ 'NAME' ];
}

if( $rawCategories ) {
    $this->Gui->add_item( array(
        'type' =>    'select',
        'options'      =>   $options,
        'name' =>	store_prefix() . 'providers_account_category',
        'label' =>   __( 'Catégorie des comptes créditeurs', 'nexo' ),
        'description' =>   __( 'En regroupant les compteurs créditeurs sous une catégorie, il sera possible de répertorier facilement chaque transaction dans le rapport des dépenses.', 'nexo' ),
        'placeholder' =>   ''
    ), 'nexo_providers', 1 );

} else {
    $this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    sprintf( 
        __( 'Vous devez <a href="%s">créer une catégorie</a> afin de l\'assigner en tant que catégories pour les comptes créditeurs des fournisseurs.<br>', 'nexo' ),
        site_url([ 'dashboard', store_slug(), 'nexo_premium', 'expenses_list', 'add' ])
    )
    ), 'nexo_providers', 1 );
}


$this->Gui->output();