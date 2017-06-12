<?php
$this->db->insert( store_prefix() . 'nexo_arrivages', array(
    'TITRE'            =>    __('Restaurant Stuffs', 'nexo'),
    'DESCRIPTION'    =>    __( 'Restaurant', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'            =>    User::id(),
    'FOURNISSEUR_REF_ID'    =>    1
) );

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
    'NOM'            =>    __('Luigi', 'nexo'),
    'EMAIL'            =>    'luigi@tendoo.org',
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id(),
));

// Rayons création

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Cakes', 'nexo'),
    'DESCRIPTION'    =>    __('Cakes', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Pizza', 'nexo'),
    'DESCRIPTION'    =>    __('pizza', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Juices', 'nexo'),
    'DESCRIPTION'    =>    __('Juices', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

$this->db->insert( store_prefix() . 'nexo_rayons', array(
    'TITRE'            =>    __('Breads', 'nexo'),
    'DESCRIPTION'    =>    __('breads', 'nexo'),
    'DATE_CREATION'    =>    date_now(),
    'AUTHOR'        =>    User::id()
));

// Categories

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Appetizers', 'nexo'), // 1
    'DESCRIPTION'    	=>    __('Appetizers', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Cheesy Black Bean Quesadillas', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        1, // Ensemble
            'QUANTITY'              =>        300,
            'SKU'                   =>        'APZER01',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.85,
            'SHADOW_PRICE'			=>	    8.25,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/cheesy-black-bean-quesadillas.jpg',
            'CODEBAR'               =>    0000001,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Sweet & Spicy Nuts', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        1, // Ensemble
            'QUANTITY'              =>        300,
            'SKU'                   =>        'APZER02',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    6.45,
            'SHADOW_PRICE'			=>	    7.35,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/sweet-spicy-nuts.jpg',
            'CODEBAR'               =>    0000002,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Spicy Apple Dip', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        1, // Ensemble
            'QUANTITY'              =>        300,
            'SKU'                   =>        'APZER03',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.32,
            'SHADOW_PRICE'			=>	    7.97,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/spicy-apple-dip.jpg',
            'CODEBAR'               =>    0000003,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Spicy Sweet Potato Chips & Cilantro Dip', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        1, // Ensemble
            'QUANTITY'              =>        300,
            'SKU'                   =>        'APZER04',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    6.52,
            'SHADOW_PRICE'			=>	    7.55,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/spicy-sweet-potato-chips-cilantrop-dip.jpg',
            'CODEBAR'               =>    0000004,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Spicy Crab Dip', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        1, // Ensemble
            'QUANTITY'              =>        300,
            'SKU'                   =>        'APZER05',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    5.25,
            'SHADOW_PRICE'			=>	    6.45,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/spicy-crab-dip.jpg',
            'CODEBAR'               =>    0000005,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Breads', 'nexo'), // 2
    'DESCRIPTION'    	=>    __('Breads', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Classic Wholemeal', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        2, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'BREADS01',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.25,
            'SHADOW_PRICE'			=>	    8.45,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/classic-wholemeal.jpg',
            'CODEBAR'               =>    0000006,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Organic Salt-Free Wholemeal', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        2, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'BREADS02',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.25,
            'SHADOW_PRICE'			=>	    8.45,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/salt-free-wholemeal.jpg',
            'CODEBAR'               =>    0000007,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Organic Wholemeal Spelt Bread', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        2, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'BREADS03',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.22,
            'SHADOW_PRICE'			=>	    8.65,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/wholemeal-spelt-bread.jpg',
            'CODEBAR'               =>    0000008,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Organic Wholemeal', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        2, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'BREADS04',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.25,
            'SHADOW_PRICE'			=>	    8.45,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/organic-wholemeal.jpg',
            'CODEBAR'               =>    9874479,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Organic Wholemeal Bread with Reduced Carbohydrate', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        2, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'BREADS05',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.52,
            'SHADOW_PRICE'			=>	    8.25,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/reduced-carbohydrate-organic-wholemeal.jpg',
            'CODEBAR'               =>    00000010,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );
        

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Cold Subs', 'nexo'), // 3
    'DESCRIPTION'    	=>    __('cold subs', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Philadelphia CheeseSteak', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        3, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'COLDSUBS01',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    11.52,
            'SHADOW_PRICE'			=>	    12.25,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/cheesesteak_600x300.jpg',
            'CODEBAR'               =>    00000011,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Chicken Teriyaki', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        3, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'COLDSUBS02',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    12.41,
            'SHADOW_PRICE'			=>	    13.36,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/chicken_teriyaki_600x300.jpg',
            'CODEBAR'               =>    1158748,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Sub Pizza', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        3, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'COLDSUBS03',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    11.25,
            'SHADOW_PRICE'			=>	    12.40,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/pizza_600x300.jpg',
            'CODEBAR'               =>    1287489,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Grilled Artichoke', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        3, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'COLDSUBS04',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    10.55,
            'SHADOW_PRICE'			=>	    11.40,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/artichoke_600x300.jpg',
            'CODEBAR'               =>    139748,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Cold Italian', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        3, // Breads
            'QUANTITY'              =>        300,
            'SKU'                   =>        'COLDSUBS05',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    10.05,
            'SHADOW_PRICE'			=>	    11.00,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/cold_italian_600x300.jpg',
            'CODEBAR'               =>    148759,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Drinks', 'nexo'), // 4
    'DESCRIPTION'    	=>    __('Drinks', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Lemon Drop Martini', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        4, // Drinks
            'QUANTITY'              =>        300,
            'SKU'                   =>        'DRINKS01',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    8.00, // $
            'PRIX_DE_VENTE'         =>    9.55,
            'SHADOW_PRICE'			=>	    11.00,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/LemonDropMartini-insidelarge.jpg',
            'CODEBAR'               =>    154785,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('White Russian', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        4, // Drinks
            'QUANTITY'              =>        300,
            'SKU'                   =>        'DRINKS02',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    9.05,
            'SHADOW_PRICE'			=>	    10.00,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/WhiteRussian-insidelarge.jpg',
            'CODEBAR'               =>    169857,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Ginger Basil Grapefruit Spritzer', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        4, // Drinks
            'QUANTITY'              =>        300,
            'SKU'                   =>        'DRINKS03',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.05,
            'SHADOW_PRICE'			=>	  9.00,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463433597-grapefruit.jpg',
            'CODEBAR'               =>    178452,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>        __('Watermelon Cooler', 'nexo-restaurant'),
            'REF_RAYON'             =>        0, 
            'REF_SHIPPING'          =>        1, // Sample Shipping
            'REF_CATEGORIE'         =>        4, // Drinks
            'QUANTITY'              =>        300,
            'SKU'                   =>        'DRINKS04',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.25,
            'SHADOW_PRICE'			=>	  9.50,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463434313-dsc-0047.jpg',
            'CODEBAR'               =>    189658,
            'STOCK_ENABLED'		    =>	0,
            'TYPE'				    =>	2,
            'STATUS'			    =>	1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Berry Jam Lemonade Freeze', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    4, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'DRINKS05',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.55,
            'SHADOW_PRICE'			=>	  9.20,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463434663-berry-jam-lemonade-freeze-6-of-8.jpg',
            'CODEBAR'               =>    196654,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Thai Tea', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    4, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'DRINKS06',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    9.55,
            'SHADOW_PRICE'			=>	  9.88,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463433951-thai-iced-tea-recipe-5.jpg',
            'CODEBAR'               =>    198547,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Coconut Lavender Lemonade', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    4, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'DRINKS07',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.55,
            'SHADOW_PRICE'			=>	  8.88,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463433402-coconut-lavender-lemonade-t-e1403963423619.jpg',
            'CODEBAR'               =>    208475,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Raspberry Lemonade Spritzers', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    4, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'DRINKS08',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.25,
            'SHADOW_PRICE'			=>	  9.88,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/gallery-1463432867-raspberry-lemonade-spritzers-3.jpg',
            'CODEBAR'               =>    215475,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Raspberry Lemonade Spritzers', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    4, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'DRINKS09',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.25,
            'SHADOW_PRICE'			=>	  9.55,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/54ebe1d6eac4d_-_6-wd-pitcher-drinks-mango-raspberry-punch-xl.jpg',
            'CODEBAR'               =>    225478,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Hot Subs', 'nexo'), // 5
    'DESCRIPTION'    	=>    __('hot subs', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

       $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Smoked Turkey Breast', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    5, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'HOTSUBS01',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.25,
            'SHADOW_PRICE'			=>	  9.55,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/plain-turkey.png',
            'CODEBAR'               =>    236587,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Pastrami', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    5, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'HOTSUBS02',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    7.55,
            'SHADOW_PRICE'			=>	  9.85,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/med-pastrami__web_new_r4.png',
            'CODEBAR'               =>    248579,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );

        $this->db->insert( store_prefix() . 'nexo_articles', array(
            'DESIGN'                =>    __('Virgina Honey Ham', 'nexo-restaurant'),
            'REF_RAYON'             =>    0, 
            'REF_SHIPPING'          =>    1, // Sample Shipping
            'REF_CATEGORIE'         =>    5, // Drinks
            'QUANTITY'              =>    300,
            'SKU'                   =>    'HOTSUBS03',
            'QUANTITE_RESTANTE'     =>    300,
            'QUANTITE_VENDU'        =>    0,
            'DEFECTUEUX'            =>    0,
            'PRIX_DACHAT'           =>    10, // $
            'PRIX_DE_VENTE'         =>    8.55,
            'SHADOW_PRICE'			=>	  9.85,
            'AUTHOR'                =>    User::id(),
            'DATE_CREATION'         =>    date_now(),
            'APERCU'                =>    ( ( store_prefix() != '' ) ? '../' : '' ) . '../../../modules/nexo-restaurant/img/demo/med-ham_web_new_r4.png',
            'CODEBAR'               =>    258579,
            'STOCK_ENABLED'		    =>	 0,
            'TYPE'				    =>	 2,
            'STATUS'			    =>	 1
        ) );  

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Special Pizza', 'nexo'), // 6
    'DESCRIPTION'    	=>    __('special pizza', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Pizza', 'nexo'), // 7
    'DESCRIPTION'    	=>    __('pizza', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Stromboli', 'nexo'), // 8
    'DESCRIPTION'    	=>    __('stromboli', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Salads', 'nexo'), // 9
    'DESCRIPTION'    	=>    __('Salads', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->insert( store_prefix() . 'nexo_categories', array(
    'NOM'            	=>    __('Stuffed Pizza', 'nexo'), // 10
    'DESCRIPTION'    	=>    __('stuffed Pizza', 'nexo'),
    'AUTHOR'        	=>    User::id(),
    'DATE_CREATION'    	=>    date_now()
) );

$this->db->query("INSERT INTO `{$this->db->dbprefix}" . store_prefix() . "nexo_clients` (`ID`, `NOM`, `PRENOM`, `POIDS`, `TEL`, `EMAIL`, `DESCRIPTION`, `DATE_NAISSANCE`, `ADRESSE`, `NBR_COMMANDES`, `DISCOUNT_ACTIVE`) VALUES
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

$this->options->set( store_prefix() . 'nexo_compact_enabled', 'yes', true );

$this->options->set( store_prefix() . 'keyshortcuts', '5|10|15|20', true );

// Rooms
$this->db->insert_batch( store_prefix() . 'nexo_restaurant_rooms', [
    [
        'NAME'          =>  __( 'Room Stage 1', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Stage 1', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
    ],
    [
        'NAME'          =>  __( 'Room Floor', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Room Floor', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
    ]
]);

// Areas
$this->db->insert_batch( store_prefix() . 'nexo_restaurant_areas', [
    [
        'NAME'          =>  __( 'Area 1', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Some Area', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  1
    ],
    [
        'NAME'          =>  __( 'Area 2', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Some Area', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  1
    ],
    [
        'NAME'          =>  __( 'Area Floor 1', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Some Floor Area 2', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  2
    ],
    [
        'NAME'          =>  __( 'Area Floor 2', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Some Floor Area 2', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  2
    ]
]);

// Kitchens
$this->db->insert_batch( store_prefix() . 'nexo_restaurant_kitchens', [
    [
        'NAME'          =>  __( 'Kitchens A', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Kitchens A', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  1,
        'REF_CATEGORY'  =>  1 // supposed to be men
    ],
    [
        'NAME'          =>  __( 'Area 2', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Some Area 2', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_ROOM'      =>  2,
        'REF_CATEGORY'  =>  2 // supposed to be women
    ]
]);

// Tables
$this->db->insert_batch( store_prefix() . 'nexo_restaurant_tables', [
    [
        'NAME'          =>  __( 'Tables A', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Tables A', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_AREA'      =>  1,
        'MAX_SEATS'     =>  4,
        'STATUS'        =>  'available',
        'CURRENT_SEATS_USED'    =>  0
    ],
    [
        'NAME'          =>  __( 'Tables B', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Tables B', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_AREA'      =>  2,
        'MAX_SEATS'     =>  5,
        'STATUS'        =>  'available',
        'CURRENT_SEATS_USED'    =>  0
    ],
    [
        'NAME'          =>  __( 'Tables C', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Tables C', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_AREA'      =>  2,
        'MAX_SEATS'     =>  3,
        'STATUS'        =>  'available',
        'CURRENT_SEATS_USED'    =>  0
    ],
    [
        'NAME'          =>  __( 'Tables D', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Tables D', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_AREA'      =>  1,
        'MAX_SEATS'     =>  1,
        'STATUS'        =>  'available',
        'CURRENT_SEATS_USED'    =>  0
    ],
    [
        'NAME'          =>  __( 'Tables Floor A', 'nexo-restaurant' ),
        'DESCRIPTION'   =>  __( 'Tables Floor A', 'nexo-restaurant' ),
        'AUTHOR'        =>  User::id(),
        'DATE_CREATION' =>  date_now(),
        'REF_AREA'      =>  3,
        'MAX_SEATS'     =>  1,
        'STATUS'        =>  'available',
        'CURRENT_SEATS_USED'    =>  0
    ],
]);