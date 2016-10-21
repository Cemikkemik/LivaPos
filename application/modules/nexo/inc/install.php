<?php
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
        $this->Nexo_Checkout->create_permissions();

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
		  `TOTAL` float NOT NULL,
		  `DISCOUNT_TYPE` varchar(200) NOT NULL,
		  `TVA` float NOT NULL,
		  `GROUP_DISCOUNT` float,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

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
		  PRIMARY KEY (`ID`)
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
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table_prefix.'nexo_articles_defectueux` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `REF_ARTICLE_BARCODE` varchar(250) NOT NULL,
		  `QUANTITE` int(11) NOT NULL,
		  `DATE_CREATION` datetime NOT NULL,
		  `AUTHOR` int(11) NOT NULL,
		  `REF_COMMAND_CODE` varchar(250) NOT NULL,
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
            // $this->db->query( 'DROP TABLE IF EXISTS `'.$table_prefix.'bon_davoir`;' );
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_commandes`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_commandes_produits`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_commandes_meta`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_commandes_paiements`;');
			
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_articles`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_articles_variations`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_articles_defectueux`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_articles_meta`;');

            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_categories`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_fournisseurs`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_historique`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_arrivages`;');

            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_rayons`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_clients`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_clients_groups`;');
            $this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_paiements`;');
			
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_coupons`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_checkout_money`;');
			
			// @since 2.7.5
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_registers`;');
			$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_registers_activities`;');

            $this->options->delete( $prefix . 'nexo_installed');
            $this->options->delete( $prefix . 'nexo_saved_barcode');
            $this->options->delete( $prefix . 'order_code');

			if( $scope == 'default' ) {
				// @since 2.8
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_stores`;');
				$this->db->query('DROP TABLE IF EXISTS `'.$table_prefix.'nexo_stores_activities`;');			
			
				$this->load->model('Nexo_Checkout');
				$this->Nexo_Checkout->delete_permissions();
			}
			
			$this->events->do_action_ref_array( 'nexo_after_delete_tables', array( $table_prefix, $scope ) );
        }
    }
}
new Nexo_Install;
