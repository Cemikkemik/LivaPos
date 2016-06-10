<?php
$this->Gui->col_width(1, 3);

$this->Gui->add_meta(array(
    'namespace'    =>    'woo_settings',
    'title'        =>    __('Réglages WooCommerce', 'nexo'),
    'col_id'    =>    1,
    'type'        =>    'box',
    'gui_saver'    =>    true,
    'use_namespace'    =>    false,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    )
));

$this->Gui->add_item( array(
	'type'			=>	'dom',
	'content'		=>	tendoo_info( __( 'Vous devez enregistrer les clés et l\'adresse du site avant de lancer l\'importation des données de la boutique WooCommerce.', 'nexo' ) )
), 'woo_settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_woo_link',
    'label'        =>    __('Lien vers site web WooCommerce', 'nexo'),
    'description'    =>    __('Veuillez spécifier l\'URL vers laquelle récupérer les données WooCommerce.', 'nexo')
), 'woo_settings', 1);

// Publishable API Key
$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_woo_ckey',
    'label'        =>    __('Clé du consommateur', 'nexo'),
    'description'    =>    sprintf(__('Générez des clées comme indiqué dans la documentation de <a href="%s" target="_blank">WooCommerce</a>.', 'nexo'), 'https://docs.woothemes.com/document/woocommerce-rest-api/')
), 'woo_settings', 1);

// API Secret Key
$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_woo_csecret',
    'label'        =>    __('Clé secrète', 'nexo'),
    'description'    =>    sprintf(__('Générez des clées comme indiqué dans la documentation de <a href="%s" target="_blank">WooCommerce</a>.', 'nexo'), 'https://docs.woothemes.com/document/woocommerce-rest-api/')
), 'woo_settings', 1);

$this->Gui->add_item( array(
	'type'			=>	'dom',
	'content'		=>	$this->load->module_view( 'nexo', 'settings/woocommerce-script', array(), true )
), 'woo_settings', 1 );

$this->Gui->output();
