<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 2);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'keyboard_shortcuts',
    'title'     =>  __( 'Raccourcis Clavier', 'nexo' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'    =>  [
            'label' =>  __( 'Sauvegarder', 'nexo' )
        ]
    ]
) );

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    tendoo_info( __( 'Les valeurs par défauts seront utilisées sur champs omis. Vous pouvez utilier un raccourcis comme "ctrl+A" ou "maj+p". Les majuscules ne sont pas prisent en charge.'))
), 'keyboard_shortcuts', 1 );

$keyboards          =   [
    'open_paywindow'        =>  [
        'text'  =>  __( 'Ouvrir la fenetre de paiement', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+p', 'nexo' )
    ],
    'order_in_hold'         =>  [
        'text'  =>  __( 'Mettre une commande en attente', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+h', 'nexo' )
    ],
    'sales_list'            =>  [
        'text'  =>  __( 'Retour à la liste des ventes', 'nexo' ),
        'description'   =>  __( 'Par défaut : home', 'nexo' )
    ],
    'search_item'           =>  [
        'text'  =>  __( 'Rechercher un produit', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+f', 'nexo' )
    ],
    'discount_window'       =>  [
        'text'  =>  __( 'Ajouter un remise', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+d', 'nexo' )
    ],
    'pending_order'         =>  [
        'text'  =>  __( 'Ouvrir les commandes en attente', 'nexo' ),
        'description'       =>  __( 'Par défaut : shift+s', 'nexo' )
    ],
    'open_calculator'       =>  [
        'text'  =>  __( 'Ouvrir la calculatrice', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+w', 'nexo' )
    ],
    'order_note'            =>  [
        'text'  =>  __( 'Ajouter une note a la commande', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+n', 'nexo' )
    ],
    'add_customer'          =>  [
        'text'  =>  __( 'Ajouter un client', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+c', 'nexo' )
    ],
    'void_order'            =>  [
        'text'  =>  __( 'Vider le panier', 'nexo' ),
        'description'   =>  __( 'Par défaut : del', 'nexo' )
    ],
    'close_register'        =>  [
        'text'  =>  __( 'Fermer la caisse', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+4', 'nexo' )
    ],
    'toggle_fullscreen'     =>  [
        'text'  =>  __( 'Faire basculer le mode plein écran', 'nexo' ),
        'description'   =>  __( 'Par défaut: shift+0', 'nexo' ),
    ],
    'cancel_discount'   =>  [
        'text'      =>  __( 'Supprimer une remise', 'nexo' ),
        'description'   =>  __( 'Par défaut : shift+del', 'nexo' )
    ]
];

foreach( $keyboards as $option_name => $data ) {
    $this->Gui->add_item( array(
        'type'          =>      'text',
        'name'          =>      $option_name,
        'label'         =>      $data[ 'text' ],
        'description'   =>      @$data[ 'description' ]
    ), 'keyboard_shortcuts', 1 );
}

$this->Gui->output();