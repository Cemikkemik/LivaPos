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
    'name'        =>    $option_prefix . 'nexo_enable_registers',
    'label'        =>    __('Utiliser les caisses enregistreuses', 'nexo'),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'oui'        =>    __('Oui', 'nexo'),
        'non'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout', 1);


$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_vat',
    'label'        =>    __('Activer la TVA', 'nexo'),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'oui'        =>    __('Oui', 'nexo'),
        'non'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Définir le taux de la TVA (%)', 'nexo'),
    'name'        =>    $option_prefix . 'nexo_vat_percent',
    'placeholder'    =>    __('Exemple : 20', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    '<h4>' . __('Configuration de la devise', 'nexo') . '</h4>'
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'nexo_currency',
    'label'        =>    __('Symbole de la devise', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'name'        =>    $option_prefix . 'nexo_currency_iso',
    'label'        =>    __('Format ISO de la devise', 'nexo')
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_currency_position',
    'label'        =>    __('Position de la devise', 'nexo'),
    'options'    =>    array(
        'before'    =>    __('Avant le montant', 'nexo'),
        'after'        =>    __('Après le montant', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_compact_enabled',
    'label'        =>    __('Activer le mode plein écran', 'nexo'),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'    =>    __('Non', 'nexo'),
        'yes'        =>    __('Oui', 'nexo')
    ),
	'description'	=>	__( 'Permettra de masquer certains éléments inutiles sur l\'interface du point de vente.', 'nexo' )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'unit_item_discount_enabled',
    'label'        =>    __('Activer la remise par article ?', 'nexo'),
	'description'	=>	__( 'Permet d\'appliquer une remise sur un produit unique. Ce type de remise est différent à la remise du panier, qui s\'applique sur tout les produits du panier.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    '<h4>' . __('Visibilité des bouttons', 'nexo') . '</h4>'
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'hide_discount_button',
    'label'        =>    __('Masquer le bouton des remises ?', 'nexo'),
	'description'	=>	__( 'Cette fonctionnalité vous permet de restreindre l\'utilisation du bouton des remises sur le point de vente. Si la fonctionnalité des coupons est active, les remises de ces dernières peuvent toujours s\'appliquer à une commande.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'disable_coupon',
    'label'        =>    __('Désactiver les coupons ?', 'nexo'),
	'description'	=>	__( 'Désactiver l\'option des coupons empêcheront à ces dernièrs de s\'appliquer aux commandes. La désactivation des coupons n\'empêchera pas au délais des coupons déjà émis de s\'écouler.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'disable_shipping',
    'label'        =>    __('Désactiver les livraisons ?', 'nexo'),
	'description'	=>	__( 'Désactiver l\'option des livraisons.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'disable_customer_creation',
    'label'        =>    __('Désactiver la création des clients ?', 'nexo'),
	'description'	=>	__( 'Permet de désactiver la création des clients. Ces dernièrs pourront toujours être créés depuis l\'interface classique.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'disable_quick_item',
    'label'        =>    __('Désactiver la création rapide de produits ?', 'nexo'),
	'description'	=>	__( 'Par défaut, il est possible d\'ajouter des produits et services directement depuis le point de vente. En choisissant "oui", cette fonctionnalité ne sera plus disponible.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout', 1);

$receipt_themes 	=	$this->events->apply_filters( 'nexo_receipt_theme', array(
    'default'       =>    __('Par défaut', 'nexo'),
    'light'		    =>	__( 'Léger', 'nexo' ),
	'simple'		=>	__( 'Simple', 'nexo' )
) );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_receipt_theme',
    'label'        =>    __('Thème des tickets de caisse', 'nexo'),
    'options'    =>    $receipt_themes
), 'Nexo_checkout2', 2);

/**
 * @since 2.3
**/

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_autoprint',
    'label'        =>    __('Activer l\'impression automatique des tickets de caisse ?', 'nexo'),
    'description'        =>    __('Par défaut vaut : "Non"', 'nexo'),
    'options'    =>    array(
        ''            =>    __('Veuillez choisir une option', 'nexo'),
        'yes'        =>    __('Oui', 'nexo'),
        'no'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

// @since 2.6.1

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_smsinvoice',
    'label'        =>    __('Envoyer une facture par SMS', 'nexo'),
    'description'        =>    __('Permet d\'envoyer une facture par SMS pour les commandes complètes aux clients enregistrés.', 'nexo'),
    'options'    =>    array(
        ''            =>    __('Veuillez choisir une option', 'nexo'),
        'yes'        =>    __('Oui', 'nexo'),
        'no'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_shadow_price',
    'label'        =>    __('Utiliser les prix fictif', 'nexo'),
    'description'        =>    __('Permet d\'afficher un prix fictif "discutable", qui ne doit pas être inférieure au prix de vente réel d\'un article.', 'nexo'),
    'options'    =>    array(
        ''            =>    __('Veuillez choisir une option', 'nexo'),
        'yes'        =>    __('Oui', 'nexo'),
        'no'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'enable_quick_search',
    'label'        =>    __('Activer la recherche rapide ?', 'nexo'),
	'description'	=>	__( 'Si votre boutique contient beaucoup de produits, l\'utilisation de la recherche rapide est indispensable.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout2', 2 );


$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'unit_price_changing',
    'label'        =>    __('Prix unitaire modifiable ?', 'nexo'),
	'description'	=>	__( 'Permet au prix d\'être modifié. La modification du prix unitaire s\'applique uniquement à la vente en cours. Cette modification portera sur le prix de vente, le prix promotionnel et sur le prix fictif.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'nexo_enable_numpad',
    'label'        =>    __('Activer le clavier numérique', 'nexo'),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'oui'        =>    __('Oui', 'nexo'),
        'non'        =>    __('Non', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Validité des commandes devis (en jours)', 'nexo'),
    'name'        =>    $option_prefix . 'nexo_devis_expiration',
    'placeholder'    =>    __('Par défaut: Illimité', 'nexo')
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Touches Raccourcis', 'nexo'),
    'name'        =>    $option_prefix . 'keyshortcuts',
    'description'    =>    __('Définissez des valeurs numériques séparée par des tirets verticaux. Exemple : 50|75|99.5|200.', 'nexo')
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'disable_partial_order',
    'label'        =>    __('Désactiver les commandes incomplètes ?', 'nexo'),
	'description'	=>	__( 'Cette option permettra de désactiver l\'enregistrement des commandes incomplètes dans le système.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    $option_prefix . 'show_item_taxes',
    'label'        =>    __('Afficher le prix hors taxe', 'nexo'),
	'description'	=>	__( 'Les taxes sur les produits sont calculé automatiquement. Vous pouvez afficher le prix hors taxe et afficher la charge fiscale total sur tout les produits.', 'nexo' ),
    'options'    =>    array(
		''		=>	__( 'Veuillez choisir une option', 'nexo' ),
        'no'        =>    __('Non', 'nexo'),
		'yes'        =>    __('Oui', 'nexo')
    )
), 'Nexo_checkout2', 2);

$this->events->do_action('load_nexo_checkout_settings', $this->Gui);

$this->Gui->output();
