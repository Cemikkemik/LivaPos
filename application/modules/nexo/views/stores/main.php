<?php
/**
 * Store Main File
**/

$this->Gui->col_width( 1, 2 );

$this->Gui->add_meta( array(
	'col_id'			=>	1,
	'title'				=>	__( 'Réglages des boutiques', 'nexo' ),
	'namespace'			=>	'nexo_shop_settings',
	'use_namespace'		=>	false,
	'gui_saver'			=>	true,
	'footer'        	=>	array(
        'submit'    	=>  array(
            'label'    	=>  __('Sauvegarder les réglages', 'nexo')
        )
    ),
) );

// Add Item
$this->Gui->add_item( array(
	'type'		=>		'select',
	'label'		=>		__( 'Activer le mode "Multi Boutique"', 'nexo' ),
	'options'	=>		array(
		'disabled'	=>	__( 'Désactiver', 'nexo' ),
		'enabled'	=>	__( 'Activer', 'nexo' )
	),
	'description'	=>	__( 'Permet d\'utiliser les fonctionnalités Multi-boutique sur NexoPOS.', 'nexo' ),
	'name'			=>	'nexo_store'
), 'nexo_shop_settings', 1 );


// Render Page
$this->Gui->output();