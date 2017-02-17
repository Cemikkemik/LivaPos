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

        $this->load->helper('nexopos');
        $this->events->add_action( 'load_dashboard_home', array( $this, 'init' ));
        $this->events->add_action( 'dashboard_footer', array( $this, 'dashboard_footer' ) );
        $this->events->add_filter( 'ui_notices', array( $this, 'ui_notices' ) );
        $this->events->add_filter( 'default_js_libraries', function ($libraries) {

            foreach ($libraries as $key => $lib) {
                if (in_array($lib, array( '../plugins/jQueryUI/jquery-ui-1.10.3.min' ))) { // '../plugins/jQuery/jQuery-2.1.4.min',
                    unset($libraries[ $key ]);
                }
            }

            return $libraries;

        });

        $segments    = $this->uri->segment_array();
        if( @$segments[ 2 ] == 'stores' && @$segments[ 4 ] == null ) {

            $this->enqueue->js_namespace( 'dashboard_footer' );
            $this->enqueue->js( 'tendoo.widget.dragging' );

        }

        $this->enqueue->js_namespace( 'dashboard_header' );
        $bower_path     =    '../modules/nexo/bower_components/';
        $libraries[]    =    $bower_path . 'numeral/min/numeral.min';
        $libraries[]    =    $bower_path . 'Chart.js/Chart.min';
        $libraries[]    =    $bower_path . 'jquery_lazyload/jquery.lazyload';
        $libraries[]    =    $bower_path . 'bootstrap-toggle/js/bootstrap2-toggle.min';
        $libraries[]    =    '../modules/nexo/js/nexo-api';
        $libraries[]    =    '../plugins/knob/jquery.knob';

        foreach( $libraries as $lib ) {
            $this->enqueue->js( $lib );
        }

        $this->enqueue->js( '../modules/nexo/js/jquery-ui.min' );
        $this->enqueue->js( '../modules/nexo/js/html5-audio-library' );
        $this->enqueue->js( '../modules/nexo/js/HTML.min' );
        $this->enqueue->js( '../modules/nexo/js/piecharts/piecharts' );
        $this->enqueue->js( '../modules/nexo/js/jquery-ui.min' );

        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( '../modules/nexo/bower_components/moment/min/moment.min' );
        $this->enqueue->js( '../modules/nexo/bower_components/angular-numeraljs/dist/angular-numeraljs.min' );
        $this->enqueue->js( '../bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker' );
        $this->enqueue->js( '../bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.templates' );

        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( 'css/nexo-arrow', module_url( 'nexo' ) );
        $this->enqueue->css( '../modules/nexo/css/jquery-ui' );
        $this->enqueue->css( '../bower_components/angular-bootstrap-datetimepicker/src/css/datetimepicker' );
        $this->enqueue->css( '../modules/nexo/bower_components/bootstrap-toggle/css/bootstrap2-toggle.min' );
        $this->enqueue->css( '../modules/nexo/css/piecharts/piecharts' );

        $this->events->add_action('load_dashboard', array( $this, 'dashboard' ));
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

        $this->events->add_filter( 'dashboard_dependencies', function( $deps ){
            $deps[]     =   'ngNumeraljs';
            $deps[]     =   'ui.bootstrap.datetimepicker';
            return $deps;
        });

        // @since 2.9.6
        $this->events->add_filter( 'signin_logo', function( $string ){
            global $Options;

            if( @$Options[ store_prefix() . 'nexo_logo_type' ] == 'text' ) {
                return @$Options[ store_prefix() . 'nexo_logo_text' ];
            } else if( @$Options[ store_prefix() . 'nexo_logo_type' ] == 'image_url' ) {
                return '<img style="' . ( ! in_array( @$Options[ store_prefix() . 'nexo_logo_width' ], array( null, '' ) ) ? 'width:' . $Options[ store_prefix() . 'nexo_logo_width' ] . 'px;' : '' ) . ( ! in_array( @$Options[ store_prefix() . 'nexo_logo_height' ], array( null, '' ) ) ? 'height:' . $Options[ store_prefix() . 'nexo_logo_height' ] . 'px;' : '' ) . '" src="' . @$Options[ store_prefix() . 'nexo_logo_url' ] . '" alt="' . @$Options[ store_prefix() . 'nexo_logo_text' ] . '"/>';
            }
            return $string;
        });

        // @since 2.9.11
        $this->events->add_filter( 'dashboard_footer_right', function( $text ) {
            global $Options;
            if( ! is_multistore() ) {
                return xss_clean( @$Options[ 'nexo_footer_text' ] );
            }
            return $text;
        });

        // @since 2.9.10
        $this->events->add_filter( 'dashboard_logo_long', function( $text ) {
            global $Options;
            if( ! is_multistore() ) {
                if( ! in_array( @$Options[ 'nexo_logo_type' ], array( 'default', null ) ) ){
                    return @$Options[ 'nexo_logo_text' ];
                }
            }
            return $text;
        });

        //
        $this->events->add_filter( 'dashboard_logo_small', function( $text ) {
            global $Options;
            if( ! is_multistore() ) {
                if( ! in_array( @$Options[ 'nexo_logo_type' ], array( 'default', null ) ) ){
                    return '<img src="' . @$Options[ 'nexo_logo_url' ] . '" alt="logo" style="width:50px;"/>';
                }
            }
            return $text;
        });

        //
        $this->events->add_filter( 'dashboard_footer_text', function( $text ){
            global $Options;
            if( ! is_multistore() ) {
                if( ! in_array( @$Options[ 'nexo_logo_type' ], array( 'default', null ) ) ){
                    return @$Options[ 'nexo_logo_text' ];
                }
            }
            return $text;
        });

        $this->events->add_action( 'dashboard_header', function(){
            echo '<meta name="mobile-web-app-capable" content="yes">';
        }, 1 );

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
     *  Ui notice
     *
     *  @param  array notice array
     *  @return array
    **/

    public function ui_notices( $notices )
    {
        global $Options;
        if( @$Options[ store_prefix() . 'nexo_enable_stock_warning' ] == 'yes' ) {
            $cache  =   new CI_Cache( array( 'adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' ) );
            if( $itemsOutOfStock    =   $cache->get( store_prefix() . 'items_out_of_stock' ) ) {
                foreach( $itemsOutOfStock as $item ) {
                    $notices[]  =   array(
                        'namespace'     =>  'items_out_of_stock',
                        'type'          =>  'info',
                        'message'       =>  sprintf( __( 'Le stock du produit <strong>%s</strong> est faible. Cliquez-ici pour accéder au produit.', 'nexo' ), @$item[ 'design' ] ),
                        'icon'          =>  'fa fa-warning',
                        'href'          =>  site_url( array( 'dashboard', store_prefix(), 'nexo', 'produits', 'lists', 'edit', $item[ 'id' ] ) ),
                    );
                }
                $cache->delete( store_prefix() . 'items_out_of_stock' );
            }
        }
        return $notices;
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
        global $Options;
        $this->lang->load_lines(dirname(__FILE__) . '/language/nexo_lang.php');
        $this->load->config('nexo');

        // If coupon is disabled, we remove it as payment
        if( @$Options[ store_prefix() . 'disable_coupon' ] == 'yes' ) {
            $payments   = $this->config->item( 'nexo_payments_types' );
            unset( $payments[ 'coupon' ] );
            $this->config->set_item( 'nexo_payments_types', $payments );
        }
    }

    /**
     * Check Whether Grocery Module is active
     *
     * @return void
    **/

    public function dashboard()
    {
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
    }

    /**
     * Add custom styles and scripts
     *
     * @return void
    **/

    public function dashboard_footer()
    {
        global $Options;
        /**
         * <script type="text/javascript" src="<?php echo js_url( 'nexo' ) . 'jsapi.js';?>"></script>
        **/
        ?>
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

            NexoAPI.Toast    =   function(){
                NexoAPI.Sound(1);
                var showtoast = new ToastBuilder({
                    defaultText: 'Toast, yo!',
                    displayTime: 2000,
                    target: 'body'
                })
				return showtoast;
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

				// $this->dashboard_widgets->add( store_prefix() . 'nexo_sales_new', array(
				// 	'title'                    =>    __('Meilleurs articles', 'nexo'),
				// 	'type'                    =>    'unwrapped',
				// 	'hide_body_wrapper'        =>    true,
				// 	'position'                =>    1,
				// 	'content'                =>    $this->load->view('../modules/nexo/inc/widgets/sales-new', array(), true)
				// ));

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
