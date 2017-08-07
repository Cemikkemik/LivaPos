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
$this->Gui->col_width(2, 2);

/**
 * ToUpper
 *
 * @param string
 * @return string
**/

function ___toUpper($key, $value)
{
    return strtoupper($value);
}

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_product_settings',
    'title'            =>        __('Réglages des produits', 'nexo'),
    'col_id'        =>        1,
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo')
        )
    ),
    'use_namespace'    =>        false,
));

$codebar            =    get_instance()->events->apply_filters('nexo_barcode_type', $this->config->item( 'nexo_barcode_supported' ) ); // 'codabar', 'code11', 'code39',

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_product_codebar',
    'label'        =>    __('Choisir le type de Code Barre', 'nexo'),
    'options'    =>    $codebar
), 'Nexo_product_settings', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'nexo_codebar_limit_nbr',
    'label'        =>    __('Limite en chiffre sur le code barre', 'nexo'),
    'description'    =>    __('S\'applique à tout type de code sauf aux suivants : EAN8, EAN13', 'nexo')
), 'Nexo_product_settings', 1);

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $this->load->view('../modules/nexo/views/settings/items-script', array(), true)
), 'Nexo_product_settings', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_products_labels',
    'label'        =>    __('Thème des étiquettes des produits', 'nexo'),
    'description'    =>    __('Choisir un template pour les étiquettes des produits.', 'nexo'),
    'options'    =>    array(
        '10'    =>    __('Produits 1/10 sur A4', 'nexo'),
		'7'    =>    __('Produits 1/7 sur A4', 'nexo'),
        '5'    =>    __('Produits 1/5 sur A4', 'nexo'),
		'4'    =>    __('Produits 1/4 sur A4', 'nexo'),
        '2'    =>    __('Produits 1/2 sur A4', 'nexo'),
    )
), 'Nexo_product_settings', 1);

/**
 * Col 2*
**/

$this->Gui->add_meta( array(
    'namespace'        =>        $option_prefix . 'nexo_product_col2',
    'title'            =>        '',
    'col_id'        =>        2,
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>     __( 'Enregistrer les réglages', 'nexo' )
        )
    ),
    'use_namespace'    =>        false,
) );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_stock_warning',
    'label'        =>    __('Activer les alertes de stock', 'nexo'),
    'options'    =>    array(
		'default'		=>	__( 'Par défaut', 'nexo' ),
		'yes'			=>	__( 'Oui', 'nexo' ),
		'no'			=>	__( 'Non', 'nexo' )
    )
), $option_prefix . 'nexo_product_col2', 2 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Pourcentage de Stock Minimale', 'nexo'),
    'name'        =>    $option_prefix . 'nexo_stock_percentage_warning',
    'placeholder'    =>    __( 'Veuillez définir une valeur numérique', 'nexo' ),
	'description'	=>	__( 'Lorsque le pourcentage restant d\'un stock atteindra un seuil, une alerte sera émise pour ce dernier.', 'nexo' )
), $option_prefix . 'nexo_product_col2', 2 );

$this->Gui->output();
