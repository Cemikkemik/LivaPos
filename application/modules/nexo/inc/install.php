<?php
// For codebar
if (! is_dir('public/upload/codebar')) {
	mkdir('public/upload/codebar');
}

// For Customer avatar @since 2.6.1
if (! is_dir('public/upload/customers')) {
	mkdir('public/upload/customers');
}

// For categories thumbs @since 2.7.1
if (! is_dir('public/upload/categories')) {
	mkdir('public/upload/categories');
}
class Nexo_Install extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('do_enable_module', array( $this, 'enable' ));
        $this->events->add_action('do_remove_module', array( $this, 'uninstall' ));
        $this->events->add_action('tendoo_settings_tables', array( $this, 'install_tables' ) );
        $this->events->add_action('tendoo_settings_final_config', array( $this, 'final_config' ), 10);
    }
    
    public function enable($namespace)
    {
        if ($namespace === 'nexo' && $this->options->get('nexo_installed') == null) {
            // Install Tables
            $this->install_tables();
            $this->final_config();
        }
    }

    /**
     * Final Config
     *
     * @return void
    **/

    public function final_config()
    {
		$this->load->model('Nexo_Checkout');
		$this->create_permissions();

		// Defaut options
		$this->options->set('nexo_installed', true, true);
		$this->options->set('nexo_display_select_client', 'enable', true);
		$this->options->set('nexo_display_payment_means', 'enable', true);
		$this->options->set('nexo_display_amount_received', 'enable', true);
		$this->options->set('nexo_display_discount', 'enable', true);
		$this->options->set('nexo_currency_position', 'before', true);
		$this->options->set('nexo_receipt_theme', 'default', true);
		$this->options->set('nexo_enable_autoprinting', 'no', true);
		$this->options->set('nexo_devis_expiration', 7, true);
		$this->options->set('nexo_shop_street', 'Cameroon, Yaoundé Ngousso Av.', true);
		$this->options->set('nexo_shop_pobox', '45 Edéa Cameroon', true);
		$this->options->set('nexo_shop_email', 'carlosjohnsonluv2004@gmail.com', true);
		$this->options->set('how_many_before_discount', 0, true);
		$this->options->set('nexo_products_labels', 5, true);
		$this->options->set('nexo_codebar_height', 100, true);
		$this->options->set('nexo_bar_width', 3, true);
		$this->options->set('nexo_soundfx', 'enable', true);
		$this->options->set('nexo_currency', '$', true);
		$this->options->set('nexo_vat_percent', 10, true);
		$this->options->set('nexo_enable_autoprint', 'yes', true);
		$this->options->set('nexo_enable_smsinvoice', 'no', true);
		$this->options->set('nexo_currency_iso', 'USD', true);
		$this->options->set( 'nexo_compact_enabled', 'yes', true );
		$this->options->set( 'nexo_enable_shadow_price', 'no', true );
		$this->options->set( 'nexo_enable_stripe', 'no', true );
    }

    /**
     * Install tables
     *
     * @return void
    **/

    public function install_tables( $scope = 'default', $prefix = '' )
    {
		$table_prefix		=	$this->db->dbprefix . $prefix;

		/**
		 * Only during installation, scope is an array
		 * Within dashboard it's a string
		**/

		if( is_array( $scope ) ) {
			// let's set this module active
			Modules::enable('grocerycrud');
			Modules::enable('nexo');
		}

		// @since 2.8 added REF_STORE
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_clients` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NOM` varchar(200) NOT NULL,
		  `PRENOM` varchar(200) NOT NULL,
		  `POIDS` int(11) NOT NULL,
		  `TEL` varchar(200) NOT NULL,
		  `EMAIL` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_NAISSANCE` datetime NOT NULL,
		  `ADRESSE` text NOT NULL,
		  `NBR_COMMANDES` int NOT NULL,
		  `OVERALL_COMMANDES` int NOT NULL,
		  `DISCOUNT_ACTIVE` int NOT NULL,
		  `TOTAL_SPEND` float NOT NULL,
		  `LAST_ORDER` varchar(200) NOT NULL,
		  `AVATAR` varchar(200) NOT NULL,
		  `STATE` varchar(200) NOT NULL,
		  `CITY` varchar(200) NOT NULL,
		  `POST_CODE` varchar(200) NOT NULL,
		  `COUNTRY` varchar(200) NOT NULL,
		  `COMPANY_NAME` varchar(200) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `REF_GROUP` int NOT NULL,
		  `AUTHOR` int NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_clients_meta` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `KEY` varchar(200) NOT NULL,
            `VALUE` text NOT NULL,
            `REF_CLIENT` int(11) NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// @since 3.1
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_clients_address` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `type` varchar(200) NOT NULL,
            `name` varchar(200) NOT NULL,
			`surname` varchar(200) NOT NULL,
			`enterprise` varchar(200) NOT NULL,
			`address_1` varchar(200) NOT NULL,
			`address_2` varchar(200) NOT NULL,
			`city` varchar(200) NOT NULL,
			`pobox` varchar(200) NOT NULL,
			`country` varchar(200) NOT NULL,
			`state` varchar(200) NOT NULL,
            `ref_client` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// Ref STORE
        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_clients_groups` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MODIFICATION` datetime NOT NULL,
		  `DISCOUNT_TYPE` varchar(220) NOT NULL,
		  `DISCOUNT_PERCENT` float(11) NOT NULL,
		  `DISCOUNT_AMOUNT` float(11) NOT NULL,
		  `DISCOUNT_ENABLE_SCHEDULE` varchar(220) NOT NULL,
		  `DISCOUNT_START` datetime NOT NULL,
		  `DISCOUNT_END` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		/**
		 * @since 2.7.5 improved
		 * 2.7.5 update brings "REF_OUTLET" to set where an order has been sold
		 * 2.8 added REF_STORE
		**/

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes` (
		`ID` int(11) NOT NULL AUTO_INCREMENT,
		`TITRE` varchar(200) NOT NULL,
		`DESCRIPTION` varchar(200) NOT NULL,
		`CODE` varchar(250) NOT NULL,
		`REF_CLIENT` int(11) NOT NULL,
		`REF_REGISTER` int(11) NOT NULL,
		`TYPE` varchar(200) NOT NULL,
		`DATE_CREATION` datetime NOT NULL,
		`DATE_MOD` datetime NOT NULL,
		`PAYMENT_TYPE` varchar(220) NOT NULL,
		`AUTHOR` varchar(200) NOT NULL,
		`SOMME_PERCU` float NOT NULL,
		`REMISE` float NOT NULL,
		`RABAIS` float NOT NULL,
		`RISTOURNE` float NOT NULL,
		`REMISE_TYPE` varchar(200) NOT NULL,
		`REMISE_PERCENT` float NOT NULL,
		`RABAIS_PERCENT` float NOT NULL,
		`RISTOURNE_PERCENT` float NOT NULL,
		`TOTAL` float NOT NULL,
		`DISCOUNT_TYPE` varchar(200) NOT NULL,
		`TVA` float NOT NULL,
		`GROUP_DISCOUNT` float,
		`REF_SHIPPING_ADDRESS` int(11) NOT NULL,
		`SHIPPING_AMOUNT` float(11) NOT NULL,
		`EXPIRATION_DATE` datetime NOT NULL,
		PRIMARY KEY (`ID`),
          UNIQUE( `CODE` )
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		$this->db->query( 'CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_shippings` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`ref_shipping` int(11) NOT NULL,
			`ref_order` int(11) NOT NULL,
			`name` varchar( 200 ) NOT NULL,
			`surname` varchar( 200 ) NOT NULL,
			`address_1` varchar( 200 ) NOT NULL,
			`address_2` varchar( 200 ) NOT NULL,
			`city` varchar( 200 ) NOT NULL,
			`country` varchar( 200 ) NOT NULL,
			`pobox` varchar( 200 ) NOT NULL,
			`state` varchar( 200 ) NOT NULL,
			`enterprise` varchar( 200 ) NOT NULL,
			`title` varchar(200) NOT NULL,
			`price` float(11) NOT NULL,
		  	PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_produits` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_PRODUCT_CODEBAR` varchar(250) NOT NULL,
		  `REF_COMMAND_CODE` varchar(250) NOT NULL,
		  `QUANTITE` int(11) NOT NULL,
		  `PRIX` float NOT NULL,
		  `PRIX_TOTAL` float NOT NULL,
		  `DISCOUNT_TYPE` varchar(200) NOT NULL,
		  `DISCOUNT_AMOUNT` float NOT NULL,
		  `DISCOUNT_PERCENT` float NOT NULL,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `INLINE` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // @ 3.0.16

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_produits_meta` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_COMMAND_PRODUCT` int(11) NOT NULL,
          `REF_COMMAND_CODE` varchar(200) NOT NULL,
		  `KEY` varchar(250) NOT NULL,
		  `VALUE` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
          `DATE_MODIFICATION` datetime NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// @since 2.9

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_paiements` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_COMMAND_CODE` varchar(250) NOT NULL,
		  `MONTANT` float NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `PAYMENT_TYPE` varchar(200) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		/**
		 * @since 2.8.2
		 * Introduce order meta
		**/

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_meta` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_ORDER_ID` int(11) NOT NULL,
		  `KEY` varchar(250) NOT NULL,
		  `VALUE` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // @since 3.0.1

        	$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_coupons` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `REF_COMMAND` int(11) NOT NULL,
            `REF_COUPON` int(11) NOT NULL,
            PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_commandes_refunds` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`REF_COMMAND` int(11) NOT NULL,
			`REF_ITEM` int(11) NOT NULL,
			`QUANTITY` int(11) NOT NULL,
			`REF_UNIT` int(11) NULL,
			`AUTHOR` int(11) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`DATE_MODIFICATION` datetime NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Articles tables
        // 			  `REF_CODE` INT NOT NULL,
        /*
              `ACTIVER_PROMOTION` BOOLEAN NOT NULL,
              `DEBUT_PROMOTION` DATETIME NOT NULL,
              `FIN_PROMOTION` DATETIME NOT NULL,
        */

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `DESIGN` varchar(200) NOT NULL,
		  `REF_RAYON` INT NOT NULL,
		  `REF_SHIPPING` INT NOT NULL,
		  `REF_CATEGORIE` INT NOT NULL,
		  `REF_PROVIDER` int NOT NULL,
		  `REF_TAXE` int NOT NULL,
		  `QUANTITY` INT NOT NULL,
		  `SKU` VARCHAR(220) NOT NULL,
		  `QUANTITE_RESTANTE` INT NOT NULL,
		  `QUANTITE_VENDU` INT NOT NULL,
		  `DEFECTUEUX` INT NOT NULL,
		  `PRIX_DACHAT` FLOAT NOT NULL,
		  `FRAIS_ACCESSOIRE` FLOAT NOT NULL,
		  `COUT_DACHAT` FLOAT NOT NULL,
		  `TAUX_DE_MARGE` FLOAT NOT NULL,
		  `PRIX_DE_VENTE` FLOAT NOT NULL,
		  `PRIX_DE_VENTE_TTC` FLOAT NOT NULL,
		  `SHADOW_PRICE` FLOAT NOT NULL,
		  `TAILLE` varchar(200) NOT NULL,
		  `POIDS` VARCHAR(200) NOT NULL,
		  `COULEUR` varchar(200) NOT NULL,
		  `HAUTEUR` VARCHAR(200) NOT NULL,
		  `LARGEUR` VARCHAR(200) NOT NULL,
		  `PRIX_PROMOTIONEL` FLOAT NOT NULL,
		  `SPECIAL_PRICE_START_DATE` datetime NOT NULL,
		  `SPECIAL_PRICE_END_DATE` datetime NOT NULL,
		  `DESCRIPTION` TEXT NOT NULL,
		  `APERCU` VARCHAR(200) NOT NULL,
		  `CODEBAR` varchar(200) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `TYPE` INT NOT NULL,
		  `STATUS` INT NOT NULL,
		  `STOCK_ENABLED` INT NOT NULL,
          `AUTO_BARCODE` INT NOT NULL,
		  `BARCODE_TYPE` VARCHAR(200) NOT NULL,
		  `USE_VARIATION` INT NOT NULL,
		  PRIMARY KEY (`ID`),
          UNIQUE( `SKU` ),
          UNIQUE( `CODEBAR` )
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// @since 2.9.1
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_meta` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_ARTICLE` int(11) NOT NULL,
		  `KEY` varchar(250) NOT NULL,
		  `VALUE` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// @since 2.9

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_variations` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`REF_ARTICLE` int(11) NOT NULL,
			`VAR_DESIGN` varchar(250) NOT NULL,
			`VAR_DESCRIPTION` varchar(250) NOT NULL,
			`VAR_PRIX_DE_VENTE` float NOT NULL,
			`VAR_QUANTITE_TOTALE` int(11) NOT NULL,
			`VAR_QUANTITE_RESTANTE` int(11) NOT NULL,
			`VAR_QUANTITE_VENDUE` int(11) NOT NULL,
			`VAR_COULEUR` varchar(250) NOT NULL,
			`VAR_TAILLE` varchar(250) NOT NULL,
			`VAR_POIDS` varchar(250) NOT NULL,
			`VAR_HAUTEUR` varchar(250) NOT NULL,
			`VAR_LARGEUR` varchar(250) NOT NULL,
			`VAR_SHADOW_PRICE` FLOAT NOT NULL,
			`VAR_SPECIAL_PRICE_START_DATE` datetime NOT NULL,
			`VAR_SPECIAL_PRICE_END_DATE` datetime NOT NULL,
			`VAR_APERCU` VARCHAR(200) NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_stock_flow` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`REF_ARTICLE_BARCODE` varchar(250) NOT NULL,
			`BEFORE_QUANTITE` int(11) NOT NULL,
			`QUANTITE` int(11) NOT NULL,
			`AFTER_QUANTITE` int(11) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`AUTHOR` int(11) NOT NULL,
			`REF_COMMAND_CODE` varchar(11) NOT NULL,
			`REF_SHIPPING` int(11) NOT NULL,
			`TYPE` varchar(200) NOT NULL,
			`UNIT_PRICE` float(11) NOT NULL,
			`TOTAL_PRICE` float(11) NOT NULL,
			`REF_PROVIDER` int(11) NOT NULL,
			`DESCRIPTION` text NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Catégories d'articles

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_categories` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NOM` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		   `DATE_MOD` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `PARENT_REF_ID` int(11) NOT NULL,
		  `THUMB` text NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Fournisseurs table

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_fournisseurs` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`NOM` varchar(200) NOT NULL,
			`BP` varchar(200) NOT NULL,
			`TEL` varchar(200) NOT NULL,
			`EMAIL` varchar(200) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`DATE_MOD` datetime NOT NULL,
			`AUTHOR` varchar(200) NOT NULL,
			`DESCRIPTION` text NOT NULL,
			`PAYABLE` float(11) NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_fournisseurs_history` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`TYPE` varchar(200) NOT NULL,
			`BEFORE_AMOUNT` float(11) NOT NULL,
			`AMOUNT` float(11) NOT NULL,
			`AFTER_AMOUNT` float(11) NOT NULL,
			`REF_PROVIDER` int(11) NOT NULL,
			`REF_INVOICE` int(11) NOT NULL,
			`REF_SUPPLY` int(11) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`DATE_MOD` datetime NOT NULL,
			`AUTHOR` int(11) NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Log Modification

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_historique` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `TITRE` varchar(200) NOT NULL,
		  `DETAILS` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
			`DATE_MOD` datetime NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        // Arrivage

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_arrivages` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`TITRE` varchar(200) NOT NULL,
			`DESCRIPTION` text NOT NULL,
			`VALUE` float NOT NULL,
			`ITEMS` int(11) NOT NULL,
			`REF_PROVIDERS` varchar(200) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`DATE_MOD` datetime NOT NULL,
			`AUTHOR` int(11) NOT NULL,
			`FOURNISSEUR_REF_ID` int(11) NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

        $this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_rayons` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `TITRE` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		   `DATE_MOD` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		/***
		 * Coupons
		 * @since 2.7.1
		**/

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_coupons` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `CODE` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `DISCOUNT_TYPE` varchar(200) NOT NULL,
		  `AMOUNT` float NOT NULL,
		  `EXPIRY_DATE` datetime NOT NULL,
		  `USAGE_COUNT` int NOT NULL,
		  `INDIVIDUAL_USE` int NOT NULL,
		  `PRODUCTS_IDS` text NOT NULL,
		  `EXCLUDE_PRODUCTS_IDS` text NOT NULL,
		  `USAGE_LIMIT` int NOT NULL,
		  `USAGE_LIMIT_PER_USER` int NOT NULL,
		  `LIMIT_USAGE_TO_X_ITEMS` int NOT NULL,
		  `FREE_SHIPPING` int NOT NULL,
		  `PRODUCT_CATEGORIES` text NOT NULL,
		  `EXCLUDE_PRODUCT_CATEGORIES` text NOT NULL,
		  `EXCLUDE_SALE_ITEMS` int NOT NULL,
		  `MINIMUM_AMOUNT` float NOT NULL,
		  `MAXIMUM_AMOUNT` float NOT NULL,
		  `USED_BY` text NOT NULL,
            `REWARDED_CASHIER` int(11) NOT NULL,
		  `EMAIL_RESTRICTIONS` text NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		// @since 2.7.5

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_registers` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `NAME` varchar(200) NOT NULL,
		  `DESCRIPTION` text NOT NULL,
		  `IMAGE_URL` text,
		  `AUTHOR` varchar(250) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `STATUS` varchar(200) NOT NULL,
		  `USED_BY` int(11) NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		/**
		 * TYPE concern activity type : opening, closing
		 * STATUS current outlet status : open, closed, unavailable
		**/

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_registers_activities` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `AUTHOR` int(11) NOT NULL,
		  `TYPE` varchar(200) NOT NULL,
		  `BALANCE` float NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  `REF_REGISTER` int(11),
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		/**
		 * @since 3.3
		 * Introduce taxes
		**/

		$this->db->query('CREATE TABLE IF NOT EXISTS `'. $table_prefix . 'nexo_taxes` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`NAME` varchar(200) NOT NULL,
			`DESCRIPTION` text NOT NULL,
			`RATE` float(11) NOT NULL,
			`AUTHOR` int(11) NOT NULL,
			`DATE_CREATION` datetime NOT NULL,
			`DATE_MOD` datetime NOT NULL,
			PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_notices` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `TYPE` varchar(200) NOT NULL,
		  `TITLE` varchar(200) NOT NULL,
		  `MESSAGE` text NOT NULL,
		  `ICON` varchar(200) NOT NULL,
		  `LINK` varchar(200) NOT NULL,
		  `REF_USER` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `DATE_MOD` datetime NOT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		if( is_array( $scope ) ) {

			/**
			 * Introduce Stores
			 * Installed Once
			**/

			$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_stores` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `AUTHOR` int(11) NOT NULL,
			  `STATUS` varchar(200) NOT NULL,
			  `NAME` varchar(200) NOT NULL,
			  `IMAGE` varchar(200) NOT NULL,
			  `DESCRIPTION` text NOT NULL,
			  `DATE_CREATION` datetime NOT NULL,
			  `DATE_MOD` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

			$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_stores_activities` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `AUTHOR` int(11) NOT NULL,
			  `TYPE` varchar(200) NOT NULL,
			  `REF_STORE` int(11) NOT NULL,
			  `DATE_CREATION` datetime NOT NULL,
			  `DATE_MOD` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

		}

		$this->events->do_action_ref_array( 'nexo_after_install_tables', array( $table_prefix, $scope ) );
    }

    /**
     * unistall Nexo
     *
     * @return void
    **/

    public function uninstall($namespace, $scope = 'default', $prefix = '')
    {
		$table_prefix		=	$this->db->dbprefix . $prefix;

        	// retrait des tables Nexo
		if ($namespace === 'nexo') {

			$this->load->model( 'Nexo_Stores' );

			$stores         =   $this->Nexo_Stores->get();

			array_unshift( $stores, [
			'ID'        =>  0
			]);

			foreach( $stores as $store ) {

				$store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_produits`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_meta`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_paiements`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_coupons`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_shippings`;');
				// @since 3.0.16
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_commandes_produits_meta`;');

				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_articles`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_articles_variations`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_articles_stock_flow`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_articles_meta`;');

				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_categories`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_fournisseurs`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_historique`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_arrivages`;');

				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_rayons`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_clients`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_clients_groups`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_clients_meta`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_clients_address`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_paiements`;');

				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_coupons`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_checkout_money`;');

				// @since 2.7.5
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_registers`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix. $store_prefix . 'nexo_registers_activities`;');

				$this->options->delete( $prefix . $store_prefix . 'nexo_installed');
				$this->options->delete( $prefix . $store_prefix . 'nexo_saved_barcode');
				$this->options->delete( $prefix . $store_prefix . 'order_code');
				$this->events->do_action_ref_array( 'nexo_after_delete_tables', array( $table_prefix . $store_prefix, $scope ) );
			}

			if( $scope == 'default' ) {
				// @since 2.8
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_stores`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_stores_activities`;');

				$this->load->model('Nexo_Checkout');
				$this->Nexo_Checkout->delete_permissions();
			}
        	}
    }

	/**
	 * Create permissions
	 * @return void
	**/

	public function create_permissions()
	{
		$this->aauth        =    $this->users->auth;
		// Create Cashier
		Group::create(
			'store.cashier',
			__( 'Caissier', 'nexo' ),
			true,
			__( 'Role ayant des permissions limitées à la vente', 'nexo' )
		);

		Group::create(
			'store.manager',
			__( 'Gérant de la boutique', 'nexo' ),
			true,
			__( 'Role ayant des permissions de gestion de la boutique.', 'nexo' )
		);

		Group::create(
			'sub-store.manager',
			__( 'Gérant de sous-boutique', 'nexo' ),
			true,
			__( 'Role ayant des permissions de gestion d\'une sous-boutique.', 'nexo' )
		);

		Group::create(
			'store.demo',
			__( 'Role test', 'nexo' ),
			true,
			__( 'Role ayant des permissions pour tester les fonctionnalités de NexoPOS.', 'nexo' )
		);

		$permissions 									=	[];
		$permissions[ 'nexo.create.orders' ] 			=	__( 'Créer des commandes', 'nexo' );
		$permissions[ 'nexo.view.orders' ] 				=	__( 'Voir la liste des commandes', 'nexo' );
		$permissions[ 'nexo.edit.orders' ] 				=	__( 'Modifier des commandes', 'nexo' );
		$permissions[ 'nexo.delete.orders' ] 			=	__( 'Supprimer des commandes', 'nexo' );

		$permissions[ 'nexo.create.items' ] 			=	__( 'Créer des articles', 'nexo' );
		$permissions[ 'nexo.view.items' ] 				=	__( 'Voir la liste des produits', 'nexo' );
		$permissions[ 'nexo.edit.items' ] 				=	__( 'Modifier des articles', 'nexo' );
		$permissions[ 'nexo.delete.items' ] 			=	__( 'Supprimer des articles', 'nexo' );

		$permissions[ 'nexo.create.categories' ] 		=	__( 'Créer des catégories', 'nexo' );
		$permissions[ 'nexo.view.categories' ] 			=	__( 'Voir la liste des catégories', 'nexo' );
		$permissions[ 'nexo.edit.categories' ] 			=	__( 'Modifier des catégories', 'nexo' );
		$permissions[ 'nexo.delete.categories' ] 		=	__( 'Supprimer des catégories', 'nexo' );

		$permissions[ 'nexo.create.departments' ] 		=	__( 'Créer des départements', 'nexo' );
		$permissions[ 'nexo.view.departments' ] 		=	__( 'Voir la liste des départements', 'nexo' );
		$permissions[ 'nexo.edit.departments' ] 		=	__( 'Modifier des départements', 'nexo' );
		$permissions[ 'nexo.delete.departments' ] 		=	__( 'Supprimer des départements', 'nexo' );

		$permissions[ 'nexo.create.providers' ] 		=	__( 'Créer des fournisseurs', 'nexo' );
		$permissions[ 'nexo.view.providers' ] 			=	__( 'Voir la liste des fournisseurs', 'nexo' );
		$permissions[ 'nexo.edit.providers' ] 			=	__( 'Modifier des fournisseurs', 'nexo' );
		$permissions[ 'nexo.delete.providers' ] 		=	__( 'Supprimer des fournisseurs', 'nexo' );

		$permissions[ 'nexo.create.supplies' ] 			=	__( 'Créer des approvisionnements', 'nexo' );
		$permissions[ 'nexo.view.supplies' ] 			=	__( 'Voir la liste des approvisionnements', 'nexo' );
		$permissions[ 'nexo.edit.supplies' ] 			=	__( 'Modifier des approvisionnements', 'nexo' );
		$permissions[ 'nexo.delete.supplies' ] 			=	__( 'Supprimer des approvisionnements', 'nexo' );

		$permissions[ 'nexo.create.customers-groups' ] 	=	__( 'Créer Groupes de clients', 'nexo' );
		$permissions[ 'nexo.view.customers-groups' ] 	=	__( 'Voir la liste des groupes de clients', 'nexo' );
		$permissions[ 'nexo.edit.customers-groups' ] 	=	__( 'Modifier Groupes de clients', 'nexo' );
		$permissions[ 'nexo.delete.customers-groups' ] 	=	__( 'Supprimer Groupes de clients', 'nexo' );
		
		$permissions[ 'nexo.create.customers' ] 		=	__( 'Créer des clients', 'nexo' );
		$permissions[ 'nexo.view.customers' ] 			=	__( 'Voir la liste des clients', 'nexo' );
		$permissions[ 'nexo.edit.customers' ] 			=	__( 'Modifier les clients', 'nexo' );
		$permissions[ 'nexo.delete.customers' ] 		=	__( 'Supprimer les clients', 'nexo' );

		$permissions[ 'nexo.create.invoices' ] 			=	__( 'Créer Factures', 'nexo' );
		$permissions[ 'nexo.view.invoices' ] 			=	__( 'Voir la liste des factures', 'nexo' );
		$permissions[ 'nexo.edit.invoices' ] 			=	__( 'Modifier Factures', 'nexo' );
		$permissions[ 'nexo.delete.invoices' ] 			=	__( 'Supprimer Factures', 'nexo' );

		$permissions[ 'nexo.create.registers' ] 		=	__( 'Créer une caisse enregistreuse', 'nexo' );
		$permissions[ 'nexo.view.registers' ] 			=	__( 'Voir la liste des caisses enregistreuse', 'nexo' );
		$permissions[ 'nexo.edit.registers' ] 			=	__( 'Modifier une caisse enregistreuse', 'nexo' );
		$permissions[ 'nexo.delete.registers' ] 		=	__( 'Supprimer une caisse enregistreuse', 'nexo' );
		$permissions[ 'nexo.use.registers' ] 			=	__( 'Utiliser une caisse enregistreuse', 'nexo' );

		$permissions[ 'nexo.create.backups' ] 			=	__( 'Créer des sauvegardes', 'nexo' );
		$permissions[ 'nexo.view.backups' ] 			=	__( 'Voir la liste des sauvegardes', 'nexo' );
		$permissions[ 'nexo.edit.backups' ] 			=	__( 'Modifier des sauvegardes', 'nexo' );
		$permissions[ 'nexo.delete.backups' ] 			= 	__( 'Supprimer des sauvegardes', 'nexo' );
		
		$permissions[ 'nexo.create.stock-adjustment' ]	=	__( 'Créer des ajustements du stock', 'nexo' );
		$permissions[ 'nexo.view.stock-adjustment' ]		=	__( 'Voir la liste des ajustements', 'nexo' );
		$permissions[ 'nexo.edit.stock-adjustment' ]		=	__( 'Modifier des ajustments', 'nexo' );
		$permissions[ 'nexo.delete.stock-adjustment' ]	= 	__( 'Supprimer des ajustements', 'nexo' );

		$permissions[ 'nexo.create.stores' ] 			=	__( 'Créer des boutiques', 'nexo' );
		$permissions[ 'nexo.view.stores' ] 			=	__( 'Voir la liste des boutiques', 'nexo' );
		$permissions[ 'nexo.edit.stores' ] 			=	__( 'Modifier des boutiques', 'nexo' );
		$permissions[ 'nexo.delete.stores' ] 			=	__( 'Supprimer des boutiques', 'nexo' );
		$permissions[ 'nexo.enter.stores' ] 			=	__( 'Utiliser une boutique', 'nexo' );

		$permissions[ 'nexo.create.coupons' ] 			=	__( 'Créer Coupons', 'nexo' );
		$permissions[ 'nexo.view.coupons' ] 			=	__( 'Voir la liste des coupons', 'nexo' );
		$permissions[ 'nexo.edit.coupons' ] 			=	__( 'Modifier Coupons', 'nexo' );
		$permissions[ 'nexo.delete.coupons' ] 			=	__( 'Supprimer Coupons', 'nexo' );		
		
		$permissions[ 'nexo.view.refund' ] 			=	__( 'Consulter un rembourssement', 'nexo' );
		$permissions[ 'nexo.create.refund' ] 			=	__( 'Créer un rembourssement', 'nexo' );
		$permissions[ 'nexo.edit.refund' ] 			=	__( 'Modifier un rembourssement', 'nexo' );
		$permissions[ 'nexo.delete.refund' ] 			=	__( 'Supprimer un rembourssement', 'nexo' );

		$permissions[ 'nexo.read.detailed-report' ] 		=	__( 'Lire ventes détaillés', 'nexo' );
		$permissions[ 'nexo.read.best-sales' ] 			=	__( 'Lire meilleures ventes', 'nexo' );
		$permissions[ 'nexo.read.daily-sales' ] 		=	__( 'Lire ventes journalières', 'nexo' );
		$permissions[ 'nexo.read.incomes-losses' ] 		=	__( 'Lire bénéfices et pertes', 'nexo' );
		$permissions[ 'nexo.read.expenses-listings' ] 	=	__( 'Lire liste des dépenses', 'nexo' );
		$permissions[ 'nexo.read.cash-flow' ] 			=	__( 'Lire flux de la trésorerie', 'nexo' );
		$permissions[ 'nexo.read.annual-sales' ] 		=	__( 'Lire income and losses', 'nexo' );
		$permissions[ 'nexo.read.cashier-performances' ] 	=	__( 'Lire performances des caissiers', 'nexo' );
		$permissions[ 'nexo.read.customer-statistics' ] 	=	__( 'Lire statistics des clients', 'nexo' );
		$permissions[ 'nexo.read.inventory-tracking' ] 	=	__( 'Lire suivi du stock', 'nexo' );
		$permissions[ 'nexo.manage.settings' ] 			=	__( 'Réglages des options', 'nexo' );
		$permissions[ 'nexo.manage.stores-settings' ] 			=	__( 'Réglages des boutiques', 'nexo' );

		foreach( $permissions as $namespace => $perm ) {
			$this->aauth->create_perm( 
				$namespace,
				$perm
			);
		}

		// all permissions with CRUD actions

		foreach([ 
			'coupons',
			'stores',
			'orders',
			'items',
			'categories',
			'departments',
			'providers',
			'supplies',
			'customers-groups',
			'customers',
			'invoices',
			'registers',
			'backups',
			'refund',
			'stock-adjustment'
		] as $component ) {
			foreach([ 'create.', 'edit.', 'delete.', 'view.' ] as $action ) {
				$this->aauth->allow_group( 'store.manager', 'nexo.' . $action . $component );
				$this->aauth->allow_group( 'master', 'nexo.' . $action . $component );
				$this->aauth->allow_group( 'admin', 'nexo.' . $action . $component );
			}
		}

		// All reports

		foreach([
			'nexo.read.detailed-report',
			'nexo.read.best-sales',
			'nexo.read.daily-sales',
			'nexo.read.incomes-losses',
			'nexo.read.expenses-listings',
			'nexo.read.cash-flow',
			'nexo.read.annual-sales',
			'nexo.read.cashier-performances',
			'nexo.read.customer-statistics',
			'nexo.read.inventory-tracking',
			'nexo.manage.settings',
			'nexo.manage.stores-settings',
		] as $reportPermission ) {
			$this->aauth->allow_group( 'store.manager', $reportPermission );
			$this->aauth->allow_group( 'master', $reportPermission );
			$this->aauth->allow_group( 'admin', $reportPermission );
		}
	}
}
new Nexo_Install;
