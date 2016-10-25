<?php
// Auto Load
require_once(dirname(__FILE__) . '/vendor/autoload.php');

if (get_instance()->setup->is_installed()) {
    // include_once(dirname(__FILE__) . '/inc/helpers.php'); deprecated
    include_once(dirname(__FILE__) . '/inc/controller.php');
    include_once(dirname(__FILE__) . '/inc/tours.php');
    // include_once(dirname(__FILE__) . '/inc/cron.php'); deprecated
}

require dirname(__FILE__) . '/inc/install.php';

class Nexo extends CI_Model
{
    public function __construct()
    {
		global $PageNow;

		// Default PageNow value
		$PageNow	=	'nexo/index';

        parent::__construct();

        $this->events->add_action('load_dashboard_home', array( $this, 'init' ));
        $this->events->add_action('dashboard_header', array( $this, 'header' ));
        $this->events->add_filter('default_js_libraries', function ($libraries) {

            foreach ($libraries as $key => $lib) {
                if (in_array($lib, array( '../plugins/jQueryUI/jquery-ui-1.10.3.min' ))) { // '../plugins/jQuery/jQuery-2.1.4.min',
                    unset($libraries[ $key ]);
                }
            }

            $libraries		  =    array_values($libraries);
            $bower_path     =    '../modules/nexo/bower_components/';

            // Add Numeral @since 2.6.3
            $libraries[]    =    $bower_path . 'numeral/min/numeral.min';
            $libraries[]    =    $bower_path . 'Chart.js/Chart.min';
            $libraries[]    =    $bower_path . 'jquery_lazyload/jquery.lazyload';
            $libraries[]    =    $bower_path . 'bootstrap-toggle/js/bootstrap2-toggle.min';
            $libraries[]    =    '../modules/nexo/js/nexo-api';
            $libraries[]    =    '../plugins/knob/jquery.knob';

            return $libraries;

        });

        $this->enqueue->css( 'css/nexo-arrow', module_url( 'nexo' ) );

        $this->events->add_action('load_dashboard', array( $this, 'dashboard' ));
        $this->events->add_action('dashboard_footer', array( $this, 'footer' ));
        $this->events->add_action('after_app_init', array( $this, 'after_app_init' ));
        $this->events->add_filter('nexo_daily_details_link', array( $this, 'remove_link' ), 10, 2);
        $this->events->add_action('load_frontend', array( $this, 'load_frontend' ));
		// POS note button
		$this->events->add_filter( 'pos_search_input_after', array( $this, 'pos_note_button' ) );

		// Redirection filter
		$this->events->add_filter( 'login_redirection', function( $redirection ) {
			if( User::in_group( 'shop_cashier' ) || User::in_group( 'shop_tester' ) ) {
				return site_url( array( 'dashboard', 'nexo', 'stores', 'all' ) );
			}
			return $redirection;
		});

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
    }

    /**
     * Front End
     *
     * @return void
    **/

    public function load_frontend()
    {
        global $Options;
        if (@$Options[ 'nexo_disable_frontend' ] != 'disable') {
            // Prevent Frontend display
            redirect(array( 'dashboard' ));
        }
    }

    /**
     * After APP init
     *
     * @return void
    **/

    public function after_app_init()
    {
        $this->lang->load_lines(dirname(__FILE__) . '/language/nexo_lang.php');

        $this->load->config('nexo');
    }

    /**
     * Display text on footer
     *
     * @return void
    **/

    public function footer()
    {

    }

    /**
     * Check Whether Grocery Module is active
     *
     * @return void
    **/

    public function dashboard()
    {
		$this->load->helper('nexopos');

		define('NEXO_CODEBAR_PATH', get_store_upload_path() . '/codebar/');

		/**
		 * Init Store Feature
		**/

		global $store_id, $store_uri, $CurrentStore, $Options;

		if( @$Options[ 'nexo_store' ] == 'enabled' && $this->config->item( 'nexo_multi_store_enabled' ) ) {

			$this->load->model( 'Nexo_Stores' );

			$store_uri	=	'stores/' . $this->uri->segment( 3, 0 ) . '/';
			$store_id	=	$this->uri->segment( 3, 0 );

			if( ! $CurrentStore	=	$this->Nexo_Stores->get( $store_id ) ) {
				$store_id = null;
			}
		}

        $escapeAds    =    $this->events->apply_filters('nexo_escape_nexoadds', Modules::is_active('nexo_ads') );

		if ( ( ! Modules::is_active('grocerycrud') || $escapeAds == false ) && ! page_is( 'migrate' ) ) {
            Modules::disable('nexo');
            redirect(array( 'dashboard', 'modules?highlight=Nexo&notice=error-occured' ));
        }
    }

    /**
     * Add custom styles and scripts
     *
     * @return void
    **/

    public function header()
    {
        global $Options;
        /**
         * <script type="text/javascript" src="<?php echo js_url( 'nexo' ) . 'jsapi.js';?>"></script>
        **/
        ?>
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="<?php echo css_url('nexo') . 'jquery-ui.css';
        ?>">
        <script src="<?php echo js_url('nexo') . 'jquery-ui.min.js';
        ?>"></script>
        <script src="<?php echo module_url('nexo') . 'js/html5-audio-library.js';
        ?>"></script>
        <script src="<?php echo module_url('nexo') . 'js/HTML.min.js';
        ?>"></script>
        <link rel="stylesheet" href="<?php echo module_url('nexo') . '/bower_components/bootstrap-toggle/css/bootstrap2-toggle.min.css';
        ?>">
		<script src="<?php echo module_url('nexo') . 'bower_components/angular/angular.min.js';?>"></script>


        <!-- Include PIE CHARTS -->
        <link rel="stylesheet" href="<?php echo css_url('nexo') . '/piecharts/piecharts.css';
        ?>">
        <script type="text/javascript" src="<?php echo js_url('nexo') . 'piecharts/piecharts.js';
        ?>"></script>
        <script type="text/javascript">

		      "use strict";

			/**
			 * Popup Print dialog
			 * @param string data
			 * @return bool
			**/

			NexoAPI.Popup			=	function(data) {
				var mywindow = window.open('', 'my div', 'height=400,width=600');
				mywindow.document.write('<html><head><title><?php echo addslashes(Html::get_title());
        ?></title>');
				mywindow.document.write('<link rel="stylesheet" href="<?php echo module_url('nexo') . 'bower_components/bootstrap/dist/css/bootstrap.min.css';
        ?>" type="text/css" />');
				mywindow.document.write('</head><body >');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

				mywindow.document.close(); // necessary for IE >= 10
				mywindow.focus(); // necessary for IE >= 10

				setTimeout( function(){
					mywindow.print();
					// mywindow.close();
				}, 500 );

				return true;
			};

			/**
			 * Bind Print item
			 *
			**/

			NexoAPI.BindPrint		=	function() {
				$( '[print-item]' ).bind( 'click', function(){
					NexoAPI.PrintElement( $(this).attr( 'print-item' ) );
				});
			}

			/**
			 * Currency Position
			**/

			NexoAPI.CurrencyPosition	=	function( amount ) {
				return '<?php echo addslashes($this->Nexo_Misc->display_currency('before'));
        ?> ' + amount + ' <?php echo addslashes($this->Nexo_Misc->display_currency('after'));
        ?>';
			}

			/**
			 * Currency Position + Money Format
			**/

			NexoAPI.DisplayMoney		=	function( amount ) {
				return NexoAPI.CurrencyPosition( NexoAPI.Format( parseFloat( amount ) ) );
			}



		var NexoSound		=	'<?php echo asset_url('/modules/nexo/sound/sound-');
        ?>';

		$( document ).ready(function(e) {
			// @since 2.6.1

			NexoAPI.Bootbox	=	function(){
				<?php if (in_array('bootbox', $this->config->item('nexo_sound_fx'))):?>
				NexoAPI.Sound(2);
				return bootbox;
				<?php endif;
        ?>
			}

			NexoAPI.Notify	=	function(){
				NexoAPI.Sound(1);
				return tendoo.notify;
			}

			NexoAPI.Sound	=	function( sound_index ){
				var SoundEnabled				=	'<?php echo @$Options[ store_prefix() . 'nexo_soundfx' ];
        ?>';
				if( ( SoundEnabled.length != 0 || SoundEnabled == 'enable' ) && SoundEnabled != 'disable' ) {
					var music = new buzz.sound( NexoSound + sound_index , {
						formats: [ "mp3" ]
					});
					music.play();
				}
			}

            NexoAPI.BindPrint();

		$(".knob").knob({
			  /*change : function (value) {
			   //console.log("change : " + value);
			   },
			   release : function (value) {
			   console.log("release : " + value);
			   },
			   cancel : function () {
			   console.log("cancel : " + this.value);
			   },*/
			  draw: function () {

				// "tron" case
				if (this.$.data('skin') == 'tron') {

				  var a = this.angle(this.cv)  // Angle
					  , sa = this.startAngle          // Previous start angle
					  , sat = this.startAngle         // Start angle
					  , ea                            // Previous end angle
					  , eat = sat + a                 // End angle
					  , r = true;

				  this.g.lineWidth = this.lineWidth;

				  this.o.cursor
				  && (sat = eat - 0.3)
				  && (eat = eat + 0.3);

				  if (this.o.displayPrevious) {
					ea = this.startAngle + this.angle(this.value);
					this.o.cursor
					&& (sa = ea - 0.3)
					&& (ea = ea + 0.3);
					this.g.beginPath();
					this.g.strokeStyle = this.previousColor;
					this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
					this.g.stroke();
				  }

				  this.g.beginPath();
				  this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
				  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
				  this.g.stroke();

				  this.g.lineWidth = 2;
				  this.g.beginPath();
				  this.g.strokeStyle = this.o.fgColor;
				  this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
				  this.g.stroke();

				  return false;
				}
			  }
			});
        });
		</script>
        <?php

    }

    /**
     * Register Widgets
     *
     * @return void
    **/

    public function init()
    {
        /*$this->dashboard_widgets->add('ventes_annuelles', array(
            'title'    => __('Nombres de commandes journalières', 'nexo'),
            'type'    => 'box-primary',
            // 'background-color'	=>	'',
            'position'    => 1,
            'content'    =>    $this->load->view('../modules/nexo/inc/widgets/sales.php', array(), true)
        ));*/

        /*$this->dashboard_widgets->add('chiffre_daffaire_net', array(
            'title'    => __('Chiffre d\'affaire journalier', 'nexo'),
            'type'    => 'box-primary',
            // 'background-color'	=>	'',
            'position'    => 1,
            'content'    =>    $this->load->view('../modules/nexo/inc/widgets/chiffre-daffaire-net.php', array(), true)
        ));*/

        /** $this->dashboard_widgets->add('nexo_guides', array(
            'title'                    => __('Guides du débutant', 'nexo'),
            'type'                    => 'box-primary',
            'hide_body_wrapper'        =>    true,
            // 'background-color'	=>	'',
            'position'                => 3,
            'content'                =>    $this->load->view('../modules/nexo/inc/widgets/guides.php', array(), true)
        ));
        **/

		/**
		 * When MultiStore is enabled, we disable default widget on main site,
		 * and use custom multistore widget instead
		**/

		if( multistore_enabled() && ! is_multistore() ) {

			$this->events->add_filter( 'gui_before_cols', function( $filter ){
				return $filter . get_instance()->load->module_view( 'nexo', 'dashboard/main-store-card', array(), true );
			});

		} else {

			$this->dashboard_widgets->add( store_prefix() . 'nexo_profile', array(
				'title'                    =>    __('Profil', 'nexo'),
				'type'                    =>    'unwrapped',
				'hide_body_wrapper'        =>    true,
				'position'                =>    1,
				'content'                =>    $this->load->view('../modules/nexo/inc/widgets/profile', array(), true)
			));

			if( User::in_group( 'master' ) || User::in_group( 'shop_manager' ) ) {

				$this->dashboard_widgets->add( store_prefix() . 'nexo_sales_new', array(
					'title'                    =>    __('Meilleurs articles', 'nexo'),
					'type'                    =>    'unwrapped',
					'hide_body_wrapper'        =>    true,
					'position'                =>    1,
					'content'                =>    $this->load->view('../modules/nexo/inc/widgets/sales-new', array(), true)
				));

				$this->dashboard_widgets->add( store_prefix() . 'nexo_sales_income', array(
					'title'                    =>    __('Chiffre d\'affaire', 'nexo'),
					'type'                    =>    'unwrapped',
					'hide_body_wrapper'        =>    true,
					'position'                =>    2,
					'content'                =>    $this->load->view('../modules/nexo/inc/widgets/income', array(), true)
				));

				$this->dashboard_widgets->add( store_prefix() . 'sale_type_new', array(
					'title'                    =>    __('Types de commades', 'nexo'),
					'type'                    =>    'unwrapped',
					'hide_body_wrapper'        =>    true,
					'position'                =>    3,
					'content'                =>    $this->load->view('../modules/nexo/inc/widgets/sale_type_new', array(), true)
				));

			}
		}

        /**
        $this->dashboard_widgets->add('nexo_tutorials', array(
            'title'                    => __('Tutoriels NexoPOS', 'nexo'),
            'type'                    => 'box-primary',
            'hide_body_wrapper'        =>    true,
            // 'background-color'	=>	'',
            'position'                => 3,
            'content'                =>    $this->load->view('../modules/nexo/inc/widgets/tutorials.php', array(), true)
        ));

        $this->dashboard_widgets->add('nexo_news', array(
            'title'                    => __('Actualités NexoPOS', 'nexo'),
            'type'                    => 'box-primary',
            'hide_body_wrapper'        =>    true,
            // 'background-color'	=>	'',
            'position'                => 2,
            'content'                =>    $this->load->view('../modules/nexo/inc/widgets/news.php', array(), true)
        ));
        **/
    }

    /**
     * Add link to premium version
    **/

    public function remove_link($link)
    {
        return 'http://codecanyon.net/item/nexopos-web-application-for-retail/16195010';
    }

	/**
	 * POS Note Button
	**/

	public function pos_note_button( $data )
	{
		ob_start();
		?>
<button class="btn btn-default" type="button" alt="<?php _e( 'Note', 'nexo' );?>" data-set-note><?php echo sprintf( __( '%s Note', 'nexo' ), '<i class="fa fa-pencil"></i>' );?></button>
<?php
		$data	.=	ob_get_clean();
		return $data;
	}
}
new Nexo;
