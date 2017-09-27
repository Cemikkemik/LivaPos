<?php
$this->Gui->col_width(1, 2);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nexo_orders',
     'gui_saver'    =>   true,
     'footer'       =>   [
          'submit'  =>   [
               'label'   =>   __( 'Enregistrer', 'nexo' )
          ]
     ]
));

$this->Gui->add_item( array(
     'type' =>    'select',
     'options'      =>   [ 
          ''        =>   __( 'Choisissez une option', 'nexo' ),
          'yes'     =>   __( 'Oui', 'nexo' ),
          'no'      =>   __( 'Non', 'nexo' )
     ],
     'name' =>	store_prefix() . 'enable_order_aging',
     'label' =>   __( 'Expiration des commandes', 'nexo' ),
     'description' =>   __( 'Activer une date d\'expiration des commandes qui ne sont pas totalement réglées', 'nexo' ),
), 'nexo_orders', 1 );

$this->Gui->add_item( array(
     'type'                   =>    'select',
     'options'                =>   [ 
          ''                  =>   __( 'Choisissez une option', 'nexo' ),
          'quotes'            =>   __( 'Devis', 'nexo' ),
          'incompletes'       =>   __( 'Incomplètes', 'nexo' ),
          'both'              =>   __( 'Devis & Incomplètes', 'nexo' )
     ],
     'name' =>	store_prefix() . 'expiring_order_type',
     'label' =>   __( 'Commandes Concernée', 'nexo' ),
     'description' =>   __( 'Définir les commandes qui sont susceptibles d\'expirer.', 'nexo' ),
), 'nexo_orders', 1 );

$this->Gui->add_item( array(
     'type' =>    'text',
     'name' =>	store_prefix() . 'expiration_time',
     'label' =>   __( 'Expiration (Jours)', 'nexo' ),
     'description' =>   __( 'Définir après combien de temps une commande expire.', 'nexo' ),
), 'nexo_orders', 1 );

$this->Gui->output();