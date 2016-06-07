<?php
$this->Gui->col_width( 1, 3 );

$this->Gui->add_meta( array(
	'namespace'	=>	'stripe_settings',
	'title'		=>	__( 'Réglages Stripe', 'nexo' ),
	'col_id'	=>	1,
	'type'		=>	'box',
	'gui_saver'	=>	true,
	'use_namespace'	=>	false,
	'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    )
) );

$this->Gui->add_item( array(
	'type'		=>	'select',
	'name'		=>	'nexo_enable_stripe',
	'label'		=>	__( 'Activer Stripe', 'nexo' ),
	'options'	=>	array(
		'no'	=>	__( 'Non', 'nexo' ),
		'yes'	=>	__( 'Oui', 'nexo' )
	),
	'description'	=>	__( 'Désactiver Stripe empêchera au ressource de ce dernier de se charger dans l\'interface de la caisse.', 'nexo' )
), 'stripe_settings', 1 );

// Publishable API Key
$this->Gui->add_item( array(
	'type'		=>	'text',
	'name'		=>	'nexo_stripe_publishable_key',
	'label'		=>	__( 'Publishable Key', 'nexo' ),
	'description'	=>	sprintf( __( 'Récupérez les informations de votre "publishable key" sur votre compte <a href="%s">Stripe</a>.', 'nexo' ), 'https://dashboard.stripe.com/account/apikeys' )
), 'stripe_settings', 1 );

// API Secret Key
$this->Gui->add_item( array(
	'type'		=>	'text',
	'name'		=>	'nexo_stripe_secret_key',
	'label'		=>	__( 'Secret Key', 'nexo' ),
	'description'	=>	sprintf( __( 'Récupérez les informations de votre clé secrete sur votre compte <a href="%s">Stripe</a>.', 'nexo' ), 'https://dashboard.stripe.com/account/apikeys' )
), 'stripe_settings', 1 );

$this->Gui->output();
