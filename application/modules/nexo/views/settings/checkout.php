<?php
$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_checkout',
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

$this->Gui->add_meta(array(
    'namespace'        =>        'Nexo_checkout2',
    'title'            =>        __('Réglages de la caisse', 'nexo'),
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
    'name'        =>    'nexo_enable_vat',
    'label'        =>    __('Activer la TVA', 'nexo'),
    'options'    =>    array(
        'oui'        =>    __('Oui', 'nexo'),
        'non'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Définir le taux de la TVA (%)', 'nexo'),
    'name'        =>    'nexo_vat_percent',
    'placeholder'    =>    __('Exemple : 20', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    '<h4>' . __('Configuration de la devise', 'nexo') . '</h4>'
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_currency',
    'label'        =>    __('Symbole de la devise', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    'nexo_currency_iso',
    'label'        =>    __('Format ISO de la devise', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nexo_currency_position',
    'label'        =>    __('Position de la devise', 'nexo'),
    'options'    =>    array(
        'before'    =>    __('Avant le montant', 'nexo'),
        'after'        =>    __('Après le montant', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nexo_receipt_theme',
    'label'        =>    __('Thème des tickets de caisse', 'nexo'),
    'options'    =>    array(
        'default'    =>    __('Par défaut', 'nexo'),
    )
), 'Nexo_checkout2', 2);

/**
 * @since 2.3
**/

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nexo_enable_autoprint',
    'label'        =>    __('Activer l\'impression automatique des tickets de caisse ?', 'nexo'),
    'description'        =>    __('Par défaut vaut : "Non"', 'nexo'),
    'options'    =>    array(
        ''            =>    __('Veuillez choisir une option', 'nexo'),
        'yes'        =>    __('Oui', 'nexo'),
        'no'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nexo_enable_numpad',
    'label'        =>    __('Activer le clavier numérique', 'nexo'),
    'options'    =>    array(
        'oui'        =>    __('Oui', 'nexo'),
        'non'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Validité des commandes devis (en jours)', 'nexo'),
    'name'        =>    'nexo_devis_expiration',
    'placeholder'    =>    __('Par défaut: Illimité', 'nexo')
), 'Nexo_checkout2', 2);

$this->events->do_action('load_nexo_checkout_settings', $this->Gui);

$this->Gui->output();
