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

$this->Gui->col_width(1, 2);

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_discount_customers',
    'title'            =>        __('Réglages de la caisse', 'nexo'),
    'col_id'        =>        1,
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    ),
    'use_namespace'    =>        false,
));

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'enable_group_discount',
    'label'        =>    __('Activer les remises de groupe', 'nexo'),
    'options'    =>    array(
        'disable'    =>    __('Désactiver', 'nexo'),
        'enable'    =>    __('Activer', 'nexo')
    )
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'discount_type',
    'label'        =>    __('Type de la remise', 'nexo'),
    'options'    =>    array(
        'disable'    =>    __('Désactiver', 'nexo'),
        'percent'    =>    __('Au pourcentage', 'nexo'),
        'amount'    =>    __('Montant fixe', 'nexo'),
    )
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'how_many_before_discount',
    'label'        =>    __('Reduction Automatique', 'nexo'),
    'description'    =>    __("Après combien de commandes un client peut-il profiter d'une remise automatique. Veuillez définir une valeur numérique. \"0\" désactive la fonctionnalité.", 'nexo')
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'discount_percent',
    'label'        =>    __('Pourcentage de la remise', 'nexo')
), 'Nexo_discount_customers', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'discount_amount',
    'label'        =>    __('Montant fixe', 'nexo')
), 'Nexo_discount_customers', 1);

/** 
 * Fetch Clients
**/

$this->load->model( 'Nexo_Misc' );
$result			=	$this->Nexo_Misc->get_customers();
$options        =    array();

foreach ($result as $_r) {
    $options[ $_r[ 'ID' ] ]        =    $_r[ 'NOM' ];
}

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'default_compte_client',
    'label'        =>    __('Compte Client par défaut', 'nexo'),
    'description'    =>    __('Ce client ne profitera pas des réductions automatique.', 'nexo'),
    'options'    =>    $options
), 'Nexo_discount_customers', 1);

$this->Gui->output();
