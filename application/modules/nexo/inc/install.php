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
			`QUANTITE` int(11) NOT NULL,
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
            'shop_cashier',
            get_instance()->lang->line( 'nexo_cashier' ),
            true,
            get_instance()->lang->line( 'nexo_cashier_details' )
        );

        // Create Shop Manager
        Group::create(
            'shop_manager',
            get_instance()->lang->line( 'nexo_shop_manager' ),
            true,
            get_instance()->lang->line( 'nexo_shop_manager_details' )
        );

        // Create Shop Tester
        Group::create(
            'shop_tester',
            get_instance()->lang->line( 'nexo_tester' ),
            true,
            get_instance()->lang->line( 'nexo_tester_details' )
        );

        // Shop Orders
        $this->aauth->create_perm('create_shop_orders',    __('Gestion des commandes', 'nexo'),            __('Peut créer des commandes', 'nexo'));
        $this->aauth->create_perm('edit_shop_orders',    __('Modification des commandes', 'nexo'),            __('Peut modifier des commandes', 'nexo'));
        $this->aauth->create_perm('delete_shop_orders',    __('Suppression des commandes', 'nexo'),            __('Peut supprimer des commandes', 'nexo'));

        // Shop Items
        $this->aauth->create_perm('create_shop_items',        __('Créer des articles', 'nexo'),            __('Peut créer des produits', 'nexo'));
        $this->aauth->create_perm('edit_shop_items',        __('Modifier des articles', 'nexo'),            __('Peut modifier des produits', 'nexo'));
        $this->aauth->create_perm('delete_shop_items',    __('Supprimer des articles', 'nexo'),        __('Peut supprimer des produits', 'nexo'));

        // Shop Categories
        $this->aauth->create_perm('create_shop_categories',  __('Créer des catégories', 'nexo'),        __('Crée les catégories', 'nexo'));
        $this->aauth->create_perm('edit_shop_categories',  __('Modifier des catégories', 'nexo'),        __('Modifie les catégories', 'nexo'));
        $this->aauth->create_perm('delete_shop_categories',  __('Supprimer des catégories', 'nexo'),        __('Supprime les catégories', 'nexo'));

        // Shop radius
        $this->aauth->create_perm('create_shop_radius',    __('Créer des rayons', 'nexo'),                __('Crée les rayons', 'nexo'));
        $this->aauth->create_perm('edit_shop_radius',    __('Modifier des rayons', 'nexo'),                __('Modifie les rayons', 'nexo'));
        $this->aauth->create_perm('delete_shop_radius',    __('Supprimer des rayons', 'nexo'),                __('Supprime les rayons', 'nexo'));

        // Shop Shipping
        $this->aauth->create_perm('create_shop_shippings',    __('Créer des collections', 'nexo'),        __('Crée les collections', 'nexo'));
        $this->aauth->create_perm('edit_shop_shippings',    __('Modifier des collections', 'nexo'),        __('Modifie les collections', 'nexo'));
        $this->aauth->create_perm('delete_shop_shippings',    __('Supprimer des collections', 'nexo'),        __('Supprime les collections', 'nexo'));

        // Shop Provider
        $this->aauth->create_perm('create_shop_providers',    __('Créer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        $this->aauth->create_perm('edit_shop_providers',    __('Modifier des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));
        $this->aauth->create_perm('delete_shop_providers',    __('Supprimer des fournisseurs', 'nexo'),        __('Gère les fournisseurs (Livreurs)', 'nexo'));

        // Shop Customers
        $this->aauth->create_perm('create_shop_customers',    __('Créer des clients', 'nexo'),        __('Création des clients', 'nexo'));
        $this->aauth->create_perm('edit_shop_customers',    __('Modifier des clients', 'nexo'),        __('Modification des clients', 'nexo'));
        $this->aauth->create_perm('delete_shop_customers',    __('Supprimer des clients', 'nexo'),        __('Suppression des clients', 'nexo'));

        // Shop Customers Group
        $this->aauth->create_perm('create_shop_customers_groups',    __('Créer des groupes de clients', 'nexo'),        __('Création des groupes de clients', 'nexo'));
        $this->aauth->create_perm('edit_shop_customers_groups',    __('Modifier des groupes de clients', 'nexo'),        __('Modification des groupes de clients', 'nexo'));
        $this->aauth->create_perm('delete_shop_customers_groups',    __('Supprimer des groupes de clients', 'nexo'),        __('Suppression des groupes de clients', 'nexo'));

        // Shop Purchase Invoices
        $this->aauth->create_perm('create_shop_purchases_invoices',    __('Créer des factures d\'achats', 'nexo'),        __('Création des factures d\'achats', 'nexo'));
        $this->aauth->create_perm('edit_shop_purchases_invoices',    __('Modifier des factures d\'achats', 'nexo'),        __('Modification des factures d\'achats', 'nexo'));
        $this->aauth->create_perm('delete_shop_purchases_invoices',    __('Supprimer des factures d\'achats', 'nexo'),        __('Suppression des factures d\'achats', 'nexo'));
        
		// Shop Order Types
        $this->aauth->create_perm('create_shop_backup',    __('Créer des sauvegardes', 'nexo'),        __('Création des sauvegardes', 'nexo'));
        $this->aauth->create_perm('edit_shop_backup',    __('Modifier des sauvegardes', 'nexo'),        __('Modification des sauvegardes', 'nexo'));
        $this->aauth->create_perm('delete_shop_backup',    __('Supprimer des sauvegardes', 'nexo'),        __('Suppression des sauvegardes', 'nexo'));

        // Shop Track User
        $this->aauth->create_perm('read_shop_user_tracker',    __('Lit le flux d\'activité des utilisateurs', 'nexo'),        __('Lit le flux d\'activité des utilisateurs', 'nexo'));
        $this->aauth->create_perm('delete_shop_user_tracker',    __('Efface le flux d\'actvite des utilisateurs', 'nexo'),        __('Efface le flux d\'actvite des utilisateurs', 'nexo'));

        // Shop Read Reports
        $this->aauth->create_perm('read_shop_reports', __('Lecture des rapports & statistiques', 'nexo'),            __('Autorise la lecture des rapports', 'nexo'));

		// Shop Registers
        $this->aauth->create_perm('create_shop_registers',    $this->lang->line( 'create_registers' ),        $this->lang->line( 'create_registers_details' ));
        $this->aauth->create_perm('edit_shop_registers',    $this->lang->line( 'edit_registers' ),        $this->lang->line( 'edit_registers_details' ));
        $this->aauth->create_perm('delete_shop_registers',    $this->lang->line( 'delete_registers' ),       $this->lang->line( 'delete_registers_details' ));
		$this->aauth->create_perm('view_shop_registers',    $this->lang->line( 'view_registers' ),       $this->lang->line( 'view_registers_details' ));

		// @since 2.8 Stores
		$this->aauth->create_perm('create_shop',    $this->lang->line( 'create_shop' ),        $this->lang->line( 'create_shop_details' ));
        $this->aauth->create_perm('edit_shop',    $this->lang->line( 'edit_shop' ),        $this->lang->line( 'edit_shop_details' ));
        $this->aauth->create_perm('delete_shop',    $this->lang->line( 'delete_shop' ),       $this->lang->line( 'delete_shop_details' ));
		$this->aauth->create_perm('enter_shop',    $this->lang->line( 'view_shop' ),       $this->lang->line( 'view_shop_details' ));

        // Coupons
        $this->aauth->create_perm('create_coupons',    __( 'Création des coupon', 'nexo' ),        $this->lang->line( 'create_coupons_details' ));
        $this->aauth->create_perm('edit_coupons',    $this->lang->line( 'edit_coupons' ),        $this->lang->line( 'edit_coupons_details' ));
        $this->aauth->create_perm('delete_coupons',    $this->lang->line( 'delete_coupons' ),        $this->lang->line( 'delete_coupons_details' ));

        // Item Stock
        $this->aauth->create_perm('create_item_stock',    $this->lang->line( 'create_item_stock' ),        $this->lang->line( 'create_item_stock_details' ));
        $this->aauth->create_perm('edit_item_stock',    $this->lang->line( 'edit_item_stock' ),        $this->lang->line( 'edit_item_stock_details' ));
        $this->aauth->create_perm('delete_item_stock',    $this->lang->line( 'delete_item_stock' ),        $this->lang->line( 'delete_item_stock_details' ));

		// Taxes
        $this->aauth->create_perm('create_taxes',    __( 'Créer des taxes', 'nexo' ),       __( 'Donne la capcité de créer des taxes.', 'nexo' ));
        $this->aauth->create_perm('edit_taxes',    __( 'Modifier des taxes', 'nexo' ),        __( 'Donne la capacité de modifier des taxes.', 'nexo' ));
        $this->aauth->create_perm('delete_taxes',    __( 'Supprimer des taxes', 'nexo' ),        __( 'Donne la capacité de supprimer des taxes.', 'nexo' ));

        /**
         * Permission for Cashier
        **/

        // Orders
        $this->aauth->allow_group('shop_cashier', 'create_shop_orders');
        $this->aauth->allow_group('shop_cashier', 'edit_shop_orders');
        $this->aauth->allow_group('shop_cashier', 'delete_shop_orders');

        // Customers
        $this->aauth->allow_group('shop_cashier', 'create_shop_customers');
        $this->aauth->allow_group('shop_cashier', 'delete_shop_customers');
        $this->aauth->allow_group('shop_cashier', 'edit_shop_customers');

        // Customers Groups
        $this->aauth->allow_group('shop_cashier', 'create_shop_customers_groups');
        $this->aauth->allow_group('shop_cashier', 'delete_shop_customers_groups');
        $this->aauth->allow_group('shop_cashier', 'edit_shop_customers_groups');

        // Profile
        $this->aauth->allow_group('shop_cashier', 'edit_profile');

		// Registers
		$this->aauth->allow_group('shop_cashier', 'view_shop_registers');

		// Shop
		$this->aauth->allow_group('shop_cashier', 'enter_shop');

		// @since 3.0.1 coupons
		$this->aauth->allow_group( 'shop_cashier', 'create_coupons');
		$this->aauth->allow_group( 'shop_cashier', 'edit_coupons');
		$this->aauth->allow_group( 'shop_cashier', 'delete_coupons');

		/**
		* Permission for Shop Manager
		**/

        	// Orders
		$this->aauth->allow_group('shop_manager', 'create_shop_orders');
		$this->aauth->allow_group('shop_manager', 'edit_shop_orders');
		$this->aauth->allow_group('shop_manager', 'delete_shop_orders');

		// Customers
		$this->aauth->allow_group('shop_manager', 'create_shop_customers');
		$this->aauth->allow_group('shop_manager', 'delete_shop_customers');
		$this->aauth->allow_group('shop_manager', 'edit_shop_customers');

		// Customers Groups
		$this->aauth->allow_group('shop_manager', 'create_shop_customers_groups');
		$this->aauth->allow_group('shop_manager', 'delete_shop_customers_groups');
		$this->aauth->allow_group('shop_manager', 'edit_shop_customers_groups');

		// Shop items
		$this->aauth->allow_group('shop_manager', 'create_shop_items');
		$this->aauth->allow_group('shop_manager', 'edit_shop_items');
		$this->aauth->allow_group('shop_manager', 'delete_shop_items');

		// Shop categories
		$this->aauth->allow_group('shop_manager', 'create_shop_categories');
		$this->aauth->allow_group('shop_manager', 'edit_shop_categories');
		$this->aauth->allow_group('shop_manager', 'delete_shop_categories');

		// Shop Radius
		$this->aauth->allow_group('shop_manager', 'create_shop_radius');
		$this->aauth->allow_group('shop_manager', 'edit_shop_radius');
		$this->aauth->allow_group('shop_manager', 'delete_shop_radius');

		// Shop Shipping
		$this->aauth->allow_group('shop_manager', 'create_shop_shippings');
		$this->aauth->allow_group('shop_manager', 'edit_shop_shippings');
		$this->aauth->allow_group('shop_manager', 'delete_shop_shippings');

		// Shop Provider
		$this->aauth->allow_group('shop_manager', 'create_shop_providers');
		$this->aauth->allow_group('shop_manager', 'edit_shop_providers');
		$this->aauth->allow_group('shop_manager', 'delete_shop_providers');

		// Shop Options
		$this->aauth->allow_group('shop_manager', 'create_options');
		$this->aauth->allow_group('shop_manager', 'edit_options');
		$this->aauth->allow_group('shop_manager', 'delete_options');

		// Shop Purchase Invoices
		$this->aauth->allow_group('shop_manager', 'create_shop_purchases_invoices');
		$this->aauth->allow_group('shop_manager', 'edit_shop_purchases_invoices');
		$this->aauth->allow_group('shop_manager', 'delete_shop_purchases_invoices');

		// Shop Backup
		$this->aauth->allow_group('shop_manager', 'create_shop_backup');
		$this->aauth->allow_group('shop_manager', 'edit_shop_backup');
		$this->aauth->allow_group('shop_manager', 'delete_shop_backup');

		// Shop Track User Activity
		$this->aauth->allow_group('shop_manager', 'read_shop_user_tracker');
		$this->aauth->allow_group('shop_manager', 'delete_shop_user_tracker');

		// Stock Entry
		$this->aauth->allow_group('shop_manager', 'create_item_stock');
		$this->aauth->allow_group('shop_manager', 'edit_item_stock');
		$this->aauth->allow_group('shop_manager', 'delete_item_stock');

		// Taxes
		$this->aauth->allow_group('shop_manager', 'create_taxes');
		$this->aauth->allow_group('shop_manager', 'edit_taxes');
		$this->aauth->allow_group('shop_manager', 'delete_taxes');

		// Read Reports
		$this->aauth->allow_group('shop_manager', 'read_shop_reports');
		// Profile
		$this->aauth->allow_group('shop_manager', 'edit_profile');

		// @since 2.7.5
		// Creating registers
		$this->aauth->allow_group('shop_manager', 'create_shop_registers');
		$this->aauth->allow_group('shop_manager', 'edit_shop_registers');
		$this->aauth->allow_group('shop_manager', 'delete_shop_registers');
		$this->aauth->allow_group('shop_manager', 'view_shop_registers');

		// @since 2.8
		$this->aauth->allow_group('shop_manager', 'enter_shop');
		$this->aauth->allow_group('shop_manager', 'create_shop');
		$this->aauth->allow_group('shop_manager', 'delete_shop');
		$this->aauth->allow_group('shop_manager', 'edit_shop');

		$this->aauth->allow_group( 'shop_manager', 'create_coupons');
		$this->aauth->allow_group( 'shop_manager', 'edit_coupons');
		$this->aauth->allow_group( 'shop_manager', 'delete_coupons');


		/**
		* Permission for Master
		**/

		// Orders
		$this->aauth->allow_group('master', 'create_shop_orders');
		$this->aauth->allow_group('master', 'edit_shop_orders');
		$this->aauth->allow_group('master', 'delete_shop_orders');

		// Customers
		$this->aauth->allow_group('master', 'create_shop_customers');
		$this->aauth->allow_group('master', 'delete_shop_customers');
		$this->aauth->allow_group('master', 'edit_shop_customers');

		// Customers Groups
		$this->aauth->allow_group('master', 'create_shop_customers_groups');
		$this->aauth->allow_group('master', 'delete_shop_customers_groups');
		$this->aauth->allow_group('master', 'edit_shop_customers_groups');

		// Shop items
		$this->aauth->allow_group('master', 'create_shop_items');
		$this->aauth->allow_group('master', 'edit_shop_items');
		$this->aauth->allow_group('master', 'delete_shop_items');

		// Shop categories
		$this->aauth->allow_group('master', 'create_shop_categories');
		$this->aauth->allow_group('master', 'edit_shop_categories');
		$this->aauth->allow_group('master', 'delete_shop_categories');

		// Shop Radius
		$this->aauth->allow_group('master', 'create_shop_radius');
		$this->aauth->allow_group('master', 'edit_shop_radius');
		$this->aauth->allow_group('master', 'delete_shop_radius');

		// Shop Shipping
		$this->aauth->allow_group('master', 'create_shop_shippings');
		$this->aauth->allow_group('master', 'edit_shop_shippings');
		$this->aauth->allow_group('master', 'delete_shop_shippings');

		// Shop Provider
		$this->aauth->allow_group('master', 'create_shop_providers');
		$this->aauth->allow_group('master', 'edit_shop_providers');
		$this->aauth->allow_group('master', 'delete_shop_providers');

		// Shop Purchase Invoices
		$this->aauth->allow_group('master', 'create_shop_purchases_invoices');
		$this->aauth->allow_group('master', 'edit_shop_purchases_invoices');
		$this->aauth->allow_group('master', 'delete_shop_purchases_invoices');

		// Shop Backup
		$this->aauth->allow_group('master', 'create_shop_backup');
		$this->aauth->allow_group('master', 'edit_shop_backup');
		$this->aauth->allow_group('master', 'delete_shop_backup');

		// Shop Track User Activity
		$this->aauth->allow_group('master', 'read_shop_user_tracker');
		$this->aauth->allow_group('master', 'delete_shop_user_tracker');

		// Read Reports
		$this->aauth->allow_group('master', 'read_shop_reports');

		// @since 2.7.5
		// Creating registers
		$this->aauth->allow_group('master', 'create_shop_registers');
		$this->aauth->allow_group('master', 'edit_shop_registers');
		$this->aauth->allow_group('master', 'delete_shop_registers');
		$this->aauth->allow_group('master', 'view_shop_registers');

		// @since 2.8
		$this->aauth->allow_group('master', 'enter_shop');
		$this->aauth->allow_group('master', 'create_shop');
		$this->aauth->allow_group('master', 'delete_shop');
		$this->aauth->allow_group('master', 'edit_shop');

		//@since 3.0.20
		// Stock Entry
		$this->aauth->allow_group('master', 'create_item_stock');
		$this->aauth->allow_group('master', 'edit_item_stock');
		$this->aauth->allow_group('master', 'delete_item_stock');

		// @since 3.3
		$this->aauth->allow_group('master', 'create_taxes');
		$this->aauth->allow_group('master', 'edit_taxes');
		$this->aauth->allow_group('master', 'delete_taxes');

		$this->aauth->allow_group( 'master', 'create_coupons');
		$this->aauth->allow_group( 'master', 'edit_coupons');
		$this->aauth->allow_group( 'master', 'delete_coupons');

		/**
		* Permission for Shop Test
		**/

		// Orders
		$this->aauth->allow_group('shop_tester', 'create_shop_orders');
		$this->aauth->allow_group('shop_tester', 'edit_shop_orders');

		// Customers
		$this->aauth->allow_group('shop_tester', 'create_shop_customers');
		$this->aauth->allow_group('shop_tester', 'edit_shop_customers');

		// Customers Groups
		$this->aauth->allow_group('shop_tester', 'create_shop_customers_groups');
		$this->aauth->allow_group('shop_tester', 'edit_shop_customers_groups');

		// Shop items
		$this->aauth->allow_group('shop_tester', 'create_shop_items');
		$this->aauth->allow_group('shop_tester', 'edit_shop_items');

		// Shop categories
		$this->aauth->allow_group('shop_tester', 'create_shop_categories');
		$this->aauth->allow_group('shop_tester', 'edit_shop_categories');

		// Shop Radius
		$this->aauth->allow_group('shop_tester', 'create_shop_radius');
		$this->aauth->allow_group('shop_tester', 'edit_shop_radius');

		// Shop Shipping
		$this->aauth->allow_group('shop_tester', 'create_shop_shippings');
		$this->aauth->allow_group('shop_tester', 'edit_shop_shippings');

		// Shop Provider
		$this->aauth->allow_group('shop_tester', 'create_shop_providers');
		$this->aauth->allow_group('shop_tester', 'edit_shop_providers');

		// Shop Purchase Invoices
		$this->aauth->allow_group('shop_tester', 'create_shop_purchases_invoices');
		$this->aauth->allow_group('shop_tester', 'edit_shop_purchases_invoices');

		// Shop Backup
		$this->aauth->allow_group('shop_tester', 'create_shop_backup');
		$this->aauth->allow_group('shop_tester', 'edit_shop_backup');

		// Shop Track User Activity
		$this->aauth->allow_group('shop_tester', 'read_shop_user_tracker');

		// Read Reports
		$this->aauth->allow_group('shop_tester', 'read_shop_reports');

		$this->aauth->allow_group('shop_tester', 'create_taxes');
		$this->aauth->allow_group('shop_tester', 'edit_taxes');

		// @since 2.7.5
		// Creating registers
		$this->aauth->allow_group('shop_tester', 'create_shop_registers');
		$this->aauth->allow_group('shop_tester', 'edit_shop_registers');
		$this->aauth->allow_group('shop_tester', 'view_shop_registers');

		//@since 3.0.20
		// Stock Entry
		$this->aauth->allow_group('shop_tester', 'create_item_stock');
		$this->aauth->allow_group('shop_tester', 'edit_item_stock');

		// @since 2.8
		$this->aauth->allow_group('shop_tester', 'enter_shop');
		$this->aauth->allow_group('shop_tester', 'create_shop');
		$this->aauth->allow_group('shop_tester', 'edit_shop');

		// Profile
		// $this->aauth->allow_group('shop_tester', 'edit_profile');
		$this->aauth->allow_group( 'shop_tester', 'create_coupons');
		$this->aauth->allow_group( 'shop_tester', 'edit_coupons');
	}
}
new Nexo_Install;
