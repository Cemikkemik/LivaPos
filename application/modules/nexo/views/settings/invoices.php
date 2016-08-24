<?php
/**
 * Add support for Multi Store
 * @since 2.8
**/

global $store_id, $CurrentStore;

$option_prefix		=	'';

if( $store_id != null ) {
	$option_prefix	=	'store_' . $store_id . '_' ;
}

$this->Gui->col_width( 1, 2 );

$this->Gui->add_meta( array(
	'col_id'		=>	1,
	'namespace'		=>	'invoice1',
	'type'			=>	'box',
	'title'			=>	__( 'Réglages des reçus de caisse', 'nexo' ),
	'gui_saver'		=>	true,
	'footer'		=>	array(
		'submit'	=>	array(
			'label'	=>	__( 'Sauvegarder les réglages', 'nexo' )
		)
	)
) );

$this->Gui->add_item( array(
	'type'			=>	'select',
	'options'		=>	$this->config->item( 'nexo_receipts_namespaces' ),
	'name'			=>	$option_prefix . 'nexo_receipt',
	'label'			=>	__( 'Veuillez choisir le format du reçu par défaut', 'nexo' )
), 'invoice1', 1 );

$this->Gui->add_item( array(
	'type'			=>	'textarea',
	'name'			=>	$option_prefix . 'receipt_col_1',
	'label'			=>	__( 'Colonne 1 du reçu par défaut', 'nexo' ),
), 'invoice1', 1 );

$this->Gui->add_item( array(
	'type'			=>	'textarea',
	'name'			=>	$option_prefix . 'receipt_col_2',
	'label'			=>	__( 'Colonne 2 du reçu par défaut', 'nexo' ),
), 'invoice1', 1 );

$this->Gui->add_item( array(
	'type'			=>	'textarea',
	'name'			=>	$option_prefix . 'custom_receipt',
	'label'			=>	__( 'Reçu personnalisé', 'nexo' ),
	'description'	=>	__( 'Vous permet de personnaliser les informations affichés sur la colonne 1 du reçu de caisse.', 'nexo' ) . '<br>' . __( 'Utilisez les balises suivantes : <br> {{shop_name}} poura afficher le nom de la boutique<br>{{shop_phone}} pour afficher le numéro de téléphone de la boutique<br>{{shop_fax}} pour afficher le fax de la boutique<br>{{shop_pobox}} pour afficher la boite postale de la boutique<br> {{shop_streetshop}} pour afficher la rue de la boutique<br>{{shop_email}} pour afficher l\'email de la boutique<br>{{shop_ordertable}} pour afficher le tableau avec les produits<br>{{shop_details}} pour afficher les détails de la boutique.', 'nexo' )
), 'invoice1', 1 );



$this->Gui->output();