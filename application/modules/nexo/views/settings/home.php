<?php
$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

$this->Gui->add_meta(array(
    'type'            =>        'unwrapped',
    'namespace'        =>        'Nexo_shop_details',
    'title'            =>        __('Détails de la boutique', 'nexo'),
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
    'type'        =>    'text',
    'name'        =>    'site_name',
    'label'        =>    __('Nom de la boutique', 'nexo'),
    'desc'        =>    __('Vous pouvez utiliser le nom du site', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_shop_phone',
    'label'        =>    __('Téléphone pour la boutique', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_shop_street',
    'label'        =>    __('Rue de la boutique', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_shop_pobox',
    'label'        =>    __('Boite postale', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_shop_email',
    'label'        =>    __('Email pour la boutique', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_shop_fax',
    'label'        =>    __('Fax pour la boutique', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'name'        =>    'nexo_bills_notices',
    'label'        =>    __('Notes pour factures', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'name'        =>    'nexo_other_details',
    'label'        =>    __('Détails supplémentaires', 'nexo'),
    'description'    =>    __('Ce champ est susceptible d\'être utilisé au pied de page des rapports', 'nexo')
), 'Nexo_shop_details', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nexo_disable_frontend',
    'label'        =>    __('Masquer le FrontEnd', 'nexo'),
    'options'    =>    array(
        'enable'        =>    __('Oui', 'nexo'),
        'disable'        =>    __('Non', 'nexo')
    ),
	'description'	=>	__( 'Cette option vous permet d\'effectuer une redirection vers le tableau de bord durant l\'accès à l\'interface publique', 'nexo' )
), 'Nexo_shop_details', 1);

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_soundfx',
    'title'            =>        __('Détails de la boutique', 'nexo'),
    'col_id'        =>        2,
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
    'name'        =>    'nexo_soundfx',
    'label'        =>    __('Activer les effets sonores', 'nexo'),
    'options'    =>    array(
        'disable'        =>    __('Désactiver', 'nexo'),
        'enable'        =>    __('Activer', 'nexo')
    )
), 'Nexo_soundfx', 2);

$this->events->do_action('load_nexo_general_settings', $this->Gui);

$this->Gui->output();
