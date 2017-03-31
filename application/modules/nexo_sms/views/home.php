<?php

/**
 * Add support for Multi Store
 * @since 2.8
**/

global $store_id, $CurrentStore, $Options;

$option_prefix		=	'';

if( $store_id != null ) {
	$option_prefix	=	'store_' . $store_id . '_' ;
}

$this->Gui->col_width(1, 2);

$this->Gui->col_width(2, 2);

// Create meta
$this->Gui->add_meta(array(
    'title'    =>    __('Réglages SMS', 'nexo_sms'),
    'namespace'    =>    'nexo_sms',
    'gui_saver'    =>    true,
    'type'        =>    'box',
    'col_id'    =>    1,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
        )
    )
));

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_sms_service',
    'options'    =>    $this->config->item('nexo_sms_providers'),
    'label'        =>    __('Veuillez choisir votre fournisseurs SMS', 'nexo_sms')
), 'nexo_sms', 1);

// SMS invoice templating
$this->Gui->add_meta(array(
    'title'    =>    __('Template SMS', 'nexo_sms'),
    'namespace'    =>    'nexo_sms_template',
    'gui_saver'    =>    true,
    'type'        =>    'box',
    'col_id'    =>    1,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
        )
    )
));

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'name'        =>    $option_prefix . 'nexo_sms_invoice_template',
    'label'        =>    __('Facture par SMS', 'nexo_sms'),
    'description'        =>    __('<br><h4>Utilisez les variables suivante pour afficher des données personnalisée : </H4><br><strong>{{ site_name }}</strong> : Affichera le nom de la boutique.<br><strong>{{ name }}</strong> : Affichera le nom du client.<br><strong>{{ order_topay }}</strong> : Affichera la valeur de la commande.<br><strong>{{ order_code }}</strong> : Affichera le code de la commande', 'nexo_sms')
), 'nexo_sms_template', 1);

// If Twilio is enabled
if (@$Options[ store_prefix() . 'nexo_sms_service' ] == 'twilio') {

    // Meta for Twilio
    $this->Gui->add_meta(array(
        'title'            =>    __('Réglages Twilio', 'nexo_sms'),
        'namespace'        =>    'nexo_twilio',
        'gui_saver'        =>    true,
        'type'            =>    'box',
        'col_id'        => 2,
        'footer'        =>        array(
            'submit'    =>        array(
                'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
            )
        )
    ));

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_twilio_account_sid',
        'label'            =>    __('SID du compte', 'nexo_sms'),
        'description'    =>    sprintf(__('Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms'), 'http://twilio.com/console')
    ), 'nexo_twilio', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_twilio_account_token',
        'label'            =>    __('Jeton d\'accès', 'nexo_sms'),
        'description'    =>    sprintf(__('Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms'), 'http://twilio.com/console')
    ), 'nexo_twilio', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_twilio_from_number',
        'label'            =>    __('Numéro d\'envoi', 'nexo_sms'),
        'description'    =>    sprintf(__('Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms'), 'http://twilio.com/console')
    ), 'nexo_twilio', 2);
} elseif (@$Options[ store_prefix() . 'nexo_sms_service' ] == 'plivo') {
    // Meta for Twilio
    $this->Gui->add_meta(array(
        'title'            =>    __('Réglages Plivo', 'nexo_sms'),
        'namespace'        =>    'nexo_plivo',
        'gui_saver'        =>    true,
        'type'            =>    'box',
        'col_id'        => 2,
        'footer'        =>        array(
            'submit'    =>        array(
                'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
            )
        )
    ));

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_plivo_auth_id',
        'label'            =>    __('ID d\'accès', 'nexo_sms'),
        'description'    =>    ''
    ), 'nexo_plivo', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_plivo_auth_key',
        'label'            =>    __('Jeton d\'accès', 'nexo_sms'),
        'description'    =>    ''
    ), 'nexo_plivo', 2);
} elseif (@$Options[ store_prefix() . 'nexo_sms_service' ] == 'bulksms') {
    // Meta for Twilio
    $this->Gui->add_meta(array(
        'title'            =>    __('Réglages BulkSMS', 'nexo_sms'),
        'namespace'        =>    'nexo_plivo',
        'gui_saver'        =>    true,
        'type'            =>    'box',
        'col_id'        => 2,
        'footer'        =>        array(
            'submit'    =>        array(
                'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
            )
        )
    ));

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_bulksms_username',
        'label'            =>    __('Nom d\'utilisateur', 'nexo_sms'),
        'description'    =>    ''
    ), 'nexo_plivo', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_bulksms_password',
        'label'            =>    __('Mot de passe', 'nexo_sms'),
        'description'    =>    ''
    ), 'nexo_plivo', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_bulksms_url',
        'label'            =>    __('Url d\'accès', 'nexo_sms'),
        'description'    =>    __('Généralement cette adresse est : "https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0". Consultez la FAQ BulkSMS pour en savoir plus', 'nexo_sms')
    ), 'nexo_plivo', 2);

    $this->Gui->add_item(array(
        'type'            =>    'text',
        'name'            =>    $option_prefix . 'nexo_bulksms_port',
        'label'            =>    __('Port d\'accès', 'nexo_sms'),
        'description'    =>    __('Le port <strong>443</strong> est celui utilisé par défaut.', 'nexo_sms')
    ), 'nexo_plivo', 2);
}

$this->Gui->output();
