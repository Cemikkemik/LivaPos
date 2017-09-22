<?php
// Collection

$this->db->insert( store_prefix() . 'nexo_arrivages', array(
    'TITRE'            =>    __('Collection 1', 'nexo'),
    'DESCRIPTION'    =>    __('Collection spéciale pour vêtements d\'hiver', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
    'FOURNISSEUR_REF_ID'    =>    1
));

$this->db->insert( store_prefix() . 'nexo_arrivages', array(
    'TITRE'            =>    __('Collection 2', 'nexo'),
    'DESCRIPTION'    =>    __('Collection spéciale pour vêtements d\'été', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
    'FOURNISSEUR_REF_ID'    =>    2
));

// Registers

$this->db->insert( store_prefix() . 'nexo_registers', array(
    'NAME'            =>    __( 'Caisse A', 'nexo' ),
    'STATUS'            =>    'closed',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        	=>    User::id(),
));

$this->db->insert( store_prefix() . 'nexo_registers', array(
    'NAME'            =>    __( 'Caisse B', 'nexo' ),
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
    'NOM'            =>    __('Fournisseurs 1', 'nexo'),
    'EMAIL'            =>    'vendor@tendoo.org',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
));

$this->db->insert( store_prefix() . 'nexo_fournisseurs', array(
    'NOM'            =>    __('Fournisseurs 2', 'nexo'),
    'EMAIL'            =>    'vendor@tendoo.org',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
));

$this->db->insert( store_prefix() . 'nexo_fournisseurs', array(
    'NOM'            =>    __('Fournisseurs 3', 'nexo'),
    'EMAIL'            =>    'vendor@tendoo.org',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
));

$this->db->insert( store_prefix() . 'nexo_fournisseurs', array(
    'NOM'            =>    __('Fournisseurs 4', 'nexo'),
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
    'NOM'            	=>    __('Vêtements', 'nexo'),
    'DESCRIPTION'    	=>    __('Catégorie vêtements', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Musique', 'nexo'),
    'DESCRIPTION'    	=>    __('Catégorie musique', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Restaurant', 'nexo'),
    'DESCRIPTION'    	=>    __('Catégorie restaurant', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
));

// Sub categories

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Hommes', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour articles d\'hommes.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	1, // Catégorie parent Vêtements
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Femmes', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour articles de femmes.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	1, // Catégorie parent Vêtements
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Enfants', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour articles pour enfants.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	1, // Catégorie parent Vêtements
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Cadeaux', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour articles en cadeaux.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	1, // Catégorie parent Vêtements
));

// Music
$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Rock', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour CD de Rock.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	2, // Catégorie parent Musique
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('RnB', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour CD de RnB.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	2, // Catégorie parent Musique
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Jazz', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour CD de Jazz.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	2, // Catégorie parent Musique
));

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            =>        __('Pop', 'nexo'),
    'DESCRIPTION'    =>        __('Catégorie pour CD de Pop.', 'nexo'),
    'AUTHOR'        =>        User::id(),
    'DATE_CREATION'    =>        date_now(),
	'PARENT_REF_ID'	=>	2, // Catégorie parent Musique
));

// Products 1

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 1', 'nexo'),
    'REF_RAYON'            =>        1, // Hommes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        4, // Hommes
    'QUANTITY'            =>        300,
    'SKU'                =>        'UGS1',
    'QUANTITE_RESTANTE'    =>    300,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    65, // $
    'PRIX_DE_VENTE'        =>    100,
    'PRIX_DE_VENTE_TTC'        =>    100,
	'SHADOW_PRICE'			=>	130,
    'TAUX_DE_MARGE'        =>    ((100 - (65 + 5)) / 65) * 100,
    'FRAIS_ACCESSOIRE'    =>    5, // $
    'COUT_DACHAT'        =>    65 + 5, // PA + FA
    'TAILLE'            =>    38, // Pouce
    'POIDS'                =>    300, //g
    'COULEUR'            =>    __('Rouge', 'nexo'),
    'HAUTEUR'            =>    25, // cm
    'LARGEUR'            =>    8, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-1.jpg',
    'CODEBAR'            =>    147852,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

// Produits 2

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 2', 'nexo'),
    'REF_RAYON'            =>        4, // cadeaux
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        4, // cadeaux
    'QUANTITY'            =>        200,
    'SKU'                =>        'UGS2',
    'QUANTITE_RESTANTE'    =>    200,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    10, // $
    'PRIX_DE_VENTE'        =>    15,
    'PRIX_DE_VENTE_TTC'        =>    15,
	'SHADOW_PRICE'			=>	30,
    'TAUX_DE_MARGE'        =>    ((15 - (10 + 3)) / 10) * 100,
    'FRAIS_ACCESSOIRE'    =>    3, // $
    'COUT_DACHAT'        =>    10 + 3, // PA + FA
    'POIDS'                =>    10, //g
    'COULEUR'            =>    __('Jaune', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-2.jpg',
    'CODEBAR'            =>    258741,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

// Produits 3

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 3', 'nexo'),
    'REF_RAYON'            =>        3, // Enfants
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Enfants
    'QUANTITY'            =>        100,
    'SKU'                =>        'UGS3',
    'QUANTITE_RESTANTE'    =>    100,
    'DEFECTUEUX'        =>    1000,
    'PRIX_DACHAT'        =>    100, // $
    'PRIX_DE_VENTE'        =>    150,
    'PRIX_DE_VENTE_TTC'        =>    150,
	'SHADOW_PRICE'			=>	180,
    'TAUX_DE_MARGE'        =>    ((150 - (100 + 20)) / 100) * 100,
    'FRAIS_ACCESSOIRE'    =>    20, // $
    'COUT_DACHAT'        =>    100 + 20, // PA + FA
    'POIDS'                =>    10, //g
    'COULEUR'            =>    __('Bleu', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-3.jpg',
    'CODEBAR'            =>    258963,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

// Produits 4

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 4', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        5, // Hommes
    'QUANTITY'            =>        150,
    'SKU'                =>        'UGS4',
    'QUANTITE_RESTANTE'    =>    150,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    120, // $
	'SHADOW_PRICE'			=>	150,
    'PRIX_DE_VENTE'        =>    190,
    'PRIX_DE_VENTE_TTC'        =>    190,
    'TAUX_DE_MARGE'        =>    ((190 - (120 + 20)) / 120) * 100,
    'FRAIS_ACCESSOIRE'    =>    20, // $
    'COUT_DACHAT'        =>    120 + 20, // PA + FA
    'POIDS'                =>    10, //g
    'COULEUR'            =>    __('Rose', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-4.jpg',
    'CODEBAR'            =>    369852,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 5', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        6, // Hommes
    'QUANTITY'            =>        252,
    'SKU'                =>        'UGS5',
    'QUANTITE_RESTANTE'    =>    252,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    120, // $
    'PRIX_DE_VENTE'        =>    190,
    'PRIX_DE_VENTE_TTC'        =>    190,
	'SHADOW_PRICE'			=>	200,
    'TAUX_DE_MARGE'        =>    ((190 - (120 + 20)) / 120) * 100,
    'FRAIS_ACCESSOIRE'    =>    20, // $
    'COUT_DACHAT'        =>    120 + 20, // PA + FA
    'POIDS'                =>    10, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-5.jpg',
    'CODEBAR'            =>    987456,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 6', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        7, // Hommes
    'QUANTITY'            =>        220,
    'SKU'                =>        'UGS6',
    'QUANTITE_RESTANTE'    =>    220,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    80, // $
    'PRIX_DE_VENTE'        =>    120,
    'PRIX_DE_VENTE_TTC'        =>    120,
	'SHADOW_PRICE'			=>	155,
    'TAUX_DE_MARGE'        =>    ((120 - (80 + 20)) / 80) * 100,
    'FRAIS_ACCESSOIRE'    =>    20, // $
    'COUT_DACHAT'        =>    80 + 20, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Noir', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-6.jpg',
    'CODEBAR'            =>    781124,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 7', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        8, // Hommes
    'QUANTITY'            =>        141,
    'SKU'                =>        'UGS7',
    'QUANTITE_RESTANTE'    =>    141,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    80, // $
    'PRIX_DE_VENTE'        =>    120,
    'PRIX_DE_VENTE_TTC'        =>    120,
	'SHADOW_PRICE'			=>	150,
    'TAUX_DE_MARGE'        =>    ((120 - (80 + 20)) / 80) * 100,
    'FRAIS_ACCESSOIRE'    =>    20, // $
    'COUT_DACHAT'        =>    80 + 20, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Cyan', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-7.jpg',
    'CODEBAR'            =>    789654,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 8', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        9, // Hommes
    'QUANTITY'            =>        120,
    'SKU'                =>        'UGS8',
    'QUANTITE_RESTANTE'    =>    120,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    120, // $
    'PRIX_DE_VENTE'        =>    300,
    'PRIX_DE_VENTE_TTC'        =>    300,
	'SHADOW_PRICE'			=>	350,
    'TAUX_DE_MARGE'        =>    ((300 - (120 + 20)) / 120) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Jaune', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-8.jpg',
    'CODEBAR'            =>    456987,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 9', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        10, // Hommes
    'QUANTITY'            =>        200,
    'SKU'                =>        'UGS9',
    'QUANTITE_RESTANTE'    =>    200,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    120, // $
    'PRIX_DE_VENTE'        =>    300,
    'PRIX_DE_VENTE_TTC'        =>    300,
	'SHADOW_PRICE'			=>	345,
    'TAUX_DE_MARGE'        =>    ((300 - (120 + 20)) / 120) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Jaune', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-9.jpg',
    'CODEBAR'            =>    874569,
	'STOCK_ENABLED'		=>	1,
	'TYPE'				=>	1,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Article 10', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        4, // Hommes
    'QUANTITY'            =>        302,
    'SKU'                =>        'UGS10',
    'QUANTITE_RESTANTE'    =>    302,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    120, // $
    'PRIX_DE_VENTE'        =>    300,
    'PRIX_DE_VENTE_TTC'        =>    300,
	'SHADOW_PRICE'			=>	330,
    'TAUX_DE_MARGE'        =>    ((300 - (120 + 20)) / 120) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Jaune', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/produit-10.jpg',
    'CODEBAR'            =>    896547
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Earl Klugh - HandPucked', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        10, // Jazz
    'QUANTITY'            =>        50,
    'SKU'                =>        'EKBF',
    'QUANTITE_RESTANTE'    =>    50,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    20, // $
    'PRIX_DE_VENTE'        =>    35.25,
    'PRIX_DE_VENTE_TTC'        =>    35.25,
	'SHADOW_PRICE'			=>	40,
    'TAUX_DE_MARGE'        =>    ((35 - (20 + 20)) / 20) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Black Pocket', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/earl.jpg',
    'CODEBAR'            =>    877774,
	'STOCK_ENABLED'		=>	2,
	'TYPE'				=>	2,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Paul McCartney - Tug Of War', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        11, // Pop
    'QUANTITY'            =>        155,
    'SKU'                =>        'PMCTOW',
    'QUANTITE_RESTANTE'    =>    150,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    20, // $
    'PRIX_DE_VENTE'        =>    35.80,
    'PRIX_DE_VENTE_TTC'        =>    35.80,
	'SHADOW_PRICE'			=>	40,
    'TAUX_DE_MARGE'        =>    ((35 - (20 + 20)) / 20) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Black Pocket', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/tugofwar.jpg',
    'CODEBAR'            =>    877775,
	'STOCK_ENABLED'		=>	2,
	'TYPE'				=>	2,
	'STATUS'			=>	1
));

$this->db->insert( store_prefix() . 'nexo_articles', array(
    'DESIGN'            =>        __('Micheal Jackson - Bad', 'nexo'),
    'REF_RAYON'            =>        2, // Femmes
    'REF_SHIPPING'        =>        1, // Sample Shipping
    'REF_CATEGORIE'        =>        11, // Pop
    'QUANTITY'            =>        300,
    'SKU'                =>        'MICHBAD',
    'QUANTITE_RESTANTE'    =>    300,
    'QUANTITE_VENDU'    =>    0,
    'DEFECTUEUX'        =>    0,
    'PRIX_DACHAT'        =>    20, // $
    'PRIX_DE_VENTE'        =>    36,
    'PRIX_DE_VENTE_TTC'        =>    36,
	'SHADOW_PRICE'			=>	40,
    'TAUX_DE_MARGE'        =>    ((35 - (20 + 20)) / 20) * 100,
    'FRAIS_ACCESSOIRE'    =>    15, // $
    'COUT_DACHAT'        =>    120 + 15, // PA + FA
    'POIDS'                =>    8, //g
    'COULEUR'            =>    __('Bad Cover', 'nexo'),
    'HAUTEUR'            =>    3, // cm
    'LARGEUR'            =>    1, // cm
    'AUTHOR'            =>    User::id(),
    'DATE_CREATION'        =>    date_now(),
    'APERCU'            =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo/images/bad.jpg',
    'CODEBAR'            =>    877779,
	'STOCK_ENABLED'		=>	2,
	'TYPE'				=>	2,
	'STATUS'			=>	1
));



// Clients

$this->db->query("INSERT INTO `{$this->db->dbprefix}" . store_prefix() . "nexo_clients` (`ID`, `NOM`, `PRENOM`, `POIDS`, `TEL`, `EMAIL`, `DESCRIPTION`, `DATE_NAISSANCE`, `ADRESSE`, `NBR_COMMANDES`, `DISCOUNT_ACTIVE`) VALUES
(1, '". __('Compte Client', 'nexo')    ."', 	'', 0, 0, 'user@tendoo.org', 				'', " . date_now() . ", '', 0, 0),
(2, '". __('John Doe', 'nexo')        ."', 	'', 0, 0, 'johndoe@tendoo.org', 				'',	" . date_now() . ", '', 0, 0),
(3, '". __('Jane Doe', 'nexo')        ."', 	'', 0, 0, 'janedoe@tendoo.org', 				'',	" . date_now() . ", '', 0, 0),
(4, '". __('Blair Jersyer', 'nexo')    ."', 	'', 0, 0, 'carlosjohnsonluv2004@gmail.com', 	'',	" . date_now() . ", '', 0, 0);");

// Options
$this->load->model('Options');

$this->options        =    new Options;

$this->options->set( store_prefix() . 'nexo_currency', '$', true);

$this->options->set( store_prefix() . 'nexo_currency_iso', 'USD', true);

$this->options->set( store_prefix() . 'nexo_currency_position', 'before', true);

$this->options->set( store_prefix() . 'nexo_enable_sound', 'enable');

// Disabling discount
$this->options->set( store_prefix() . 'discount_type', 'disable', true);
