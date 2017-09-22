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
$this->Gui->col_width( 2, 2 );

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
	),
	'use_namespace'	=>	false
) );

/**
$this->Gui->add_item( array(
	'type'			=>	'select',
	'options'		=>	$this->config->item( 'nexo_receipts_namespaces' ),
	'name'			=>	$option_prefix . 'nexo_receipt',
	'label'			=>	__( 'Veuillez choisir le format du reçu par défaut', 'nexo' )
), 'invoice1', 1 );
**/

$this->Gui->add_item( array(
	'type'			=>	'text',
	'name'			=>	$option_prefix . 'url_to_logo',
	'label' 			=>	__( 'Url logo', 'nexo' ),
	'description'			=>	__( 'Si ce champ est rempli, l\'image sera affichée sur le reçu de vente à la place du nom de la boutique.', 'nexo' ),
), 'invoice1', 1 );

$this->Gui->add_item( array(
	'type'			=>	'text',
	'name'			=>	$option_prefix . 'logo_height',
	'label' 			=>	__( 'Hauteur Logo(px)', 'nexo' ),
	'description'			=>	__( 'Forcer la hauteur du logo. Veuillez définir une valeur numérique, par exemple : "30" et non "30px"', 'nexo' ),
), 'invoice1', 1 );

$this->Gui->add_item( array(
	'type'			=>	'text',
	'name'			=>	$option_prefix . 'logo_width',
	'label' 			=>	__( 'Largeur Logo(px)', 'nexo' ),
	'description'			=>	__( 'Forcer la largeur du logo. Veuillez définir une valeur numérique, par exemple : "30" et non "30px"', 'nexo' ),
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

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'name'        =>    $option_prefix . 'nexo_bills_notices',
    'label'        =>    __('Notes pour factures', 'nexo')
), 'invoice1', 1);

$this->Gui->add_item( array(
	'type'			=>	'dom',
	'content'		=>
	__( '<h4>Utilisez les balises suivantes : </h4>', 'nexo' ) .
	__( '{shop_name} pour afficher le nom de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_phone} pour afficher le numéro de téléphone de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_fax} pour afficher le fax de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_pobox} pour afficher la boite postale de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_street} pour afficher la rue de la boutique', 'nexo' ). '<br>' .
	__( '{shop_email} pour afficher l\'email de la boutique', 'nexo' ). '<br>' .
	__( '{order_date}, pour afficher la date de la commande.', 'nexo' ) . '<br>' .
	__( '{order_updated}, pour afficher la date de modification de la commande.', 'nexo' ) . '<br>' .
	__( '{order_code}, pour afficher le code de la commande.', 'nexo' ) . '<br>' .
	__( '{order_id}, pour afficher l\'identifiant de la commande.', 'nexo' ) . '<br>' .
	__( '{order_note}, pour afficher les notes de la commande.', 'nexo' ) . '<br>' .
	__( '{order_cashier}, pour afficher l\'auteur de la commande.', 'nexo' ) . '<br>' .
	__( '{customer_name}, pour afficher le nom du client.', 'nexo' ) . '<br>' .
	__( '{customer_phone}, pour afficher le numéro de téléphone du client.', 'nexo' ) . '<br>'
), 'invoice1', 1 );

/** 
 *	-----------------------------------------------------------------------------
 *  						Supply Invoice Settings
 * 	-----------------------------------------------------------------------------
**/

$this->Gui->add_meta( array(
	'col_id'		=>	2,
	'namespace'		=>	'invoice2',
	'type'			=>	'box',
	'title'			=>	__( 'Reçu d\'approvisionnement', 'nexo' ),
	'gui_saver'		=>	true,
	'footer'		=>	array(
		'submit'	=>	array(
			'label'	=>	__( 'Sauvegarder les réglages', 'nexo' )
		)
	),
	'use_namespace'	=>	false
) );

$this->Gui->add_item( array(
	'type'			=>	'textarea',
	'name'			=>	$option_prefix . 'supply_receipt_col_1',
	'label'			=>	__( 'Colonne 1 du reçu par défaut', 'nexo' ),
), 'invoice2', 2 );

$this->Gui->add_item( array(
	'type'			=>	'textarea',
	'name'			=>	$option_prefix . 'supply_receipt_col_2',
	'label'			=>	__( 'Colonne 2 du reçu par défaut', 'nexo' ),
), 'invoice2', 2 );

$this->Gui->add_item( array(
	'type'			=>	'dom',
	'content'		=>
	__( '<h4>Utilisez les balises suivantes : </h4>', 'nexo' ) .
	__( '{shop_name} pour afficher le nom de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_phone} pour afficher le numéro de téléphone de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_fax} pour afficher le fax de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_pobox} pour afficher la boite postale de la boutique', 'nexo' ) . '<br>' .
	__( '{shop_street} pour afficher la rue de la boutique', 'nexo' ). '<br>' .
	__( '{shop_email} pour afficher l\'email de la boutique', 'nexo' ). '<br>'
), 'invoice2', 2 );

$this->Gui->output();
