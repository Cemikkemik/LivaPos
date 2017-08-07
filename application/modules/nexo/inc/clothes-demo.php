<?php
// Collection

$this->db->insert( store_prefix() . 'nexo_arrivages', array(
    'TITRE'            =>    __('Charlottes Russe 2016', 'nexo'),
    'DESCRIPTION'    =>    __( 'Collection spéciale de vêtement Charlotte Russe', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'            =>    User::id(),
    'FOURNISSEUR_REF_ID'    =>    1
) );

$this->db->insert( store_prefix() . 'nexo_arrivages', array(
    'TITRE'            =>    __('Charlottes Russe 2017', 'nexo'),
    'DESCRIPTION'    =>    __( 'Collection spéciale de vêtement Charlotte Russe', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
    'FOURNISSEUR_REF_ID'    =>    2
) );

// Registers

$this->db->insert( store_prefix() . 'nexo_registers', array(
    'NAME'            =>    __( 'Caisse 1', 'nexo' ),
    'STATUS'            =>    'closed',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        	=>    User::id(),
) );

$this->db->insert( store_prefix() . 'nexo_registers', array(
    'NAME'            =>    __( 'Caisse 2', 'nexo' ),
    'STATUS'            =>    'closed',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        	=>    User::id(),
));

$this->db->insert( store_prefix() . 'nexo_registers', array(
    'NAME'            =>    __( 'Caisse C', 'nexo' ),
    'STATUS'            =>    'locked',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        	=>    User::id(),
));

// Fournisseurs

$this->db->insert( store_prefix() . 'nexo_fournisseurs', array(
    'NOM'            =>    __('Charlotte Russe', 'nexo'),
    'EMAIL'            =>    'vendor@tendoo.org',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
));

// Rayons création

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Hommes', 'nexo'),
    'DESCRIPTION'    =>    __('Rayon des hommes', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Femmes', 'nexo'),
    'DESCRIPTION'    =>    __('Rayon des Femmes', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Enfants', 'nexo'),
    'DESCRIPTION'    =>    __('Rayon des enfants', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Cadeaux', 'nexo'),
    'DESCRIPTION'    =>    __('Rayon des cadeaux', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

// Creation des catégories

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Femmes', 'nexo'), // 1
    'DESCRIPTION'    	=>    __('Catégorie des femmes', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Accessoires', 'nexo'), // 2
    'DESCRIPTION'    	=>    __('Catégorie des accéssoires', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Chaussures', 'nexo'), // 3
    'DESCRIPTION'    	=>    __('Catégorie des chaussures', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Robes', 'nexo'), // 4
    'DESCRIPTION'    	=>    __('Catégorie des robes', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Ensemble', 'nexo'), // 5
    'DESCRIPTION'    	=>    __('Catégorie des ensembles', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Leggings', 'nexo'), // 6
    'DESCRIPTION'    	=>    __('Catégorie des Leggings', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Sweat Shirt', 'nexo'), // 7
    'DESCRIPTION'    	=>    __('Catégorie des Sweat Shirt', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Crop Top', 'nexo'), // 8
    'DESCRIPTION'    	=>    __('Catégorie des Crop Top', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now(),
    'PARENT_REF_ID'     =>      1
) );

// Products 1

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Velvet V-Neck Body Suit', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Ensemble
    'QUANTITY'            =>        80550,
    'SKU'                =>        'BODYSUIT01',
    'QUANTITE_RESTANTE'    =>    80550,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    17.99,
    'PRIX_DE_VENTE_TTC'        =>    17.99,
	'SHADOW_PRICE'			=>	19,
    'TAUX_DE_MARGE'        =>    ((19.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/velvetvneckbodysuit.jpg',
    'CODEBAR'            =>    '0000001',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Qupid Lucite Heel Dress Booties', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        3, // Chaussure
    'QUANTITY'            =>        80550,
    'SKU'                =>        'BOOTIES01',
    'QUANTITE_RESTANTE'    =>    80550,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    30, // $
    'PRIX_DE_VENTE'        =>    42.99,
    'PRIX_DE_VENTE_TTC'        =>    42.99,
	'SHADOW_PRICE'			=>	43,
    'TAUX_DE_MARGE'        =>    ((42.99 - (30 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/quidluciteheeldressbooties.jpg',
    'CODEBAR'            =>    '0000002',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Twisted High-Low Top', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT02',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    15, // $
    'PRIX_DE_VENTE'        =>    18.99,
    'PRIX_DE_VENTE_TTC'        =>    18.99,
	'SHADOW_PRICE'			=>	19,
    'TAUX_DE_MARGE'        =>    ((18.99 - (15 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    15 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/twistedhighlowtop.jpg',
    'CODEBAR'            =>    '0000003',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Floral Notched Off-The Shoulder', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT03',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    15, // $
    'PRIX_DE_VENTE'        =>    19.99,
    'PRIX_DE_VENTE_TTC'        =>    19.99,
	'SHADOW_PRICE'			=>	19,
    'TAUX_DE_MARGE'        =>    ((19.99 - (15 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    19 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Fleurie', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/floralnotchedofftheshoulder.jpg',
    'CODEBAR'            =>    '0000004',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Sequin Cropped Bomber Jacket', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT04',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    30, // $
    'PRIX_DE_VENTE'        =>    42.99,
    'PRIX_DE_VENTE_TTC'        =>    42.99,
	'SHADOW_PRICE'			=>	19,
    'TAUX_DE_MARGE'        =>    ((42.99 - (30 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    49 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Fleurie', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/sequincroppedbomberjacket.jpg',
    'CODEBAR'            =>    '0000005',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Sequin Cropped Bomber Jacket', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        6, // Leggins
    'QUANTITY'            =>        6000,
    'SKU'                =>        'LEGGINGS01',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    14.99,
    'PRIX_DE_VENTE_TTC'        =>    14.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((14.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/pushuplowriseleggings.jpg',
    'CODEBAR'            =>    '0000006',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Varsity Stripe Jogger Pants', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        6, // Leggins
    'QUANTITY'            =>        6000,
    'SKU'                =>        'LEGGINGS02',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    19.99,
    'PRIX_DE_VENTE_TTC'        =>    19.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((19.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/varsitystripejoggerpants.jpg',
    'CODEBAR'            =>    '0000007',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Ribbed Lace Up BodySuit', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Body Suit
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT05',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    17.99,
    'PRIX_DE_VENTE_TTC'        =>    17.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((17.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/ribberlaceupbody.jpg',
    'CODEBAR'            =>    '68478945',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Marled French Terry Jogger Pants', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Body Suit
    'QUANTITY'            =>        6000,
    'SKU'                =>        'LEGGINGS03',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    18.99,
    'PRIX_DE_VENTE_TTC'        =>    18.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((18.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/marledfrenchterryjoggerpants.jpg',
    'CODEBAR'            =>    '0000009',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Floating Mock Neck Strapless Dress', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        4, // Dress
    'QUANTITY'            =>        6000,
    'SKU'                =>        'DRESS03',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    29.99,
    'PRIX_DE_VENTE_TTC'        =>    29.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((29.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/floatingmocknectstraplessdress.jpg',
    'CODEBAR'            =>    '0000010',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Shimmer Open Back BodyCon Dress', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        4, // Dress
    'QUANTITY'            =>        6000,
    'SKU'                =>        'DRESS02',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    29.99,
    'PRIX_DE_VENTE_TTC'        =>    29.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((29.99 - (10 + 2)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    10 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/shimmeropenbackbodycondress.jpg',
    'CODEBAR'            =>    '0000011',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Chrochet Trim Cold Should Sweatshirt', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        7, // Dress
    'QUANTITY'            =>        6000,
    'SKU'                =>        'SWEATSHIRT01',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    18, // $
    'PRIX_DE_VENTE'        =>    21.99,
    'PRIX_DE_VENTE_TTC'        =>    21.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((21.99 - (18 + 2)) / 18) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    18 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/crochettrimcoldshoulder.jpg',
    'CODEBAR'            =>    '0000012',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Mirrored Sequin V-Neck Romper', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Dress
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT07',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    30, // $
    'PRIX_DE_VENTE'        =>    36.99,
    'PRIX_DE_VENTE_TTC'        =>    36.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((36.99 - (30 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Etoilée', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/mirrorsequinvneck.jpg',
    'CODEBAR'            =>    '0000013',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Velvet Mock Nect Cut-Out Jumpshit', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT08',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    30, // $
    'PRIX_DE_VENTE'        =>    34.99,
    'PRIX_DE_VENTE_TTC'        =>    34.99,
	'SHADOW_PRICE'			=>	16,
    'TAUX_DE_MARGE'        =>    ((34.99 - (30 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    __('Bleu Sombre', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/velvetmocknectcutoutjumpsuit.jpg',
    'CODEBAR'            =>    '0000014',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Lace & Faux Leather Choker Necklaces - 3 Pack', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        2, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'ACCESSOIRE1',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    6.99,
    'PRIX_DE_VENTE_TTC'        =>    6.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((6.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/lacefauxleatherchokernecklaces.jpg',
    'CODEBAR'            =>    '0000015',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Hacci Cold Shoulder Raglan Tee', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        7, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'SWEATSHIRT02',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    17.99,
    'PRIX_DE_VENTE_TTC'        =>    17.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((17.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/haccicoldshoulderreglantee.jpg',
    'CODEBAR'            =>    '0000016',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Marled Strappy Caged bodysuit', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        7, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'BODYSUIT09',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    18.99,
    'PRIX_DE_VENTE_TTC'        =>    18.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((18.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/marledstrappycaedbodysuit.jpg',
    'CODEBAR'            =>    '0000017',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Embroidered Mesh Mock Neck Crop Top', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        8, // Crop Top
    'QUANTITY'            =>        6000,
    'SKU'                =>        'CROPTOP01',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    17.99,
    'PRIX_DE_VENTE_TTC'        =>    17.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((17.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/embroideredmeshmocknectcrop.jpg',
    'CODEBAR'            =>    '0000018',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Qupid Metal Trim Platform Pumps', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        8, // Ensemble
    'QUANTITY'            =>        6000,
    'SKU'                =>        'SHOES01',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    35.99,
    'PRIX_DE_VENTE_TTC'        =>    35.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((35.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/qupidmetatrimplateformpumps.jpg',
    'CODEBAR'            =>    '0000019',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Floral Floating Mock Neck Top', 'nexo'),
    'REF_RAYON'            =>        0, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        8, // Crop Top
    'QUANTITY'            =>        6000,
    'SKU'                =>        'CROPTOP02',
    'QUANTITE_RESTANTE'    =>    6000,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    5, // $
    'PRIX_DE_VENTE'        =>    23.99,
    'PRIX_DE_VENTE_TTC'        =>    23.99,
	'SHADOW_PRICE'			=>	7,
    'TAUX_DE_MARGE'        =>    ((23.99 - (5 + 2)) / 30) * 100,
    'FRAIS_ACCESSOIRE'    =>    2, // $
    'COUT_DACHAT'        =>    30 + 2, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    0, //g
    'COULEUR'            =>    '',
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/charlotterusse/floralfloatingmocknecktop.jpg',
    'CODEBAR'            =>    '0000020',
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
) );


// Clients

$this->db->query( "INSERT INTO `{$this->db->dbprefix}" . store_prefix() . "nexo_clients` (`ID`, `NOM`, `PRENOM`, `POIDS`, `TEL`, `EMAIL`, `DESCRIPTION`, `DATE_NAISSANCE`, `ADRESSE`, `NBR_COMMANDES`, `DISCOUNT_ACTIVE`) VALUES
(1, '". __('Compte Client', 'nexo')    ."', 	'', 0, 0, 'user@tendoo.org', 				'', '0000-00-00 00:00:00', '', 0, 0),
(2, '". __('John Doe', 'nexo')        ."', 	'', 0, 0, 'johndoe@tendoo.org', 				'',	'0000-00-00 00:00:00', '', 0, 0),
(3, '". __('Jane Doe', 'nexo')        ."', 	'', 0, 0, 'janedoe@tendoo.org', 				'',	'0000-00-00 00:00:00', '', 0, 0),
(4, '". __('Blair Jersyer', 'nexo')    ."', 	'', 0, 0, 'carlosjohnsonluv2004@gmail.com', 	'',	'0000-00-00 00:00:00', '', 0, 0);");

// Options
$this->load->model('Options');

$this->options        =    new Options;

$this->options->set( store_prefix() . 'nexo_currency', '$', true);

$this->options->set( store_prefix() . 'nexo_currency_iso', 'USD', true);

$this->options->set( store_prefix() . 'nexo_currency_position', 'before', true);

$this->options->set( store_prefix() . 'nexo_enable_sound', 'enable');

// Disabling discount
$this->options->set( store_prefix() . 'discount_type', 'disable', true);