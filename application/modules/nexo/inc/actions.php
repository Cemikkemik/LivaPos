<?php
class Nexo_Actions extends Tendoo_Module
{
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
     * After APP init
     *
     * @return void
    **/

    public function after_app_init()
    {
        global $Options;
        
        $this->lang->load_lines( dirname(__FILE__) . '/../language/nexo_lang.php');
        
        $this->load->config( 'nexo' );

        // If coupon is disabled, we remove it as payment
        if( @$Options[ store_prefix() . 'disable_coupon' ] == 'yes' ) {
            $payments   = $this->config->item( 'nexo_payments_types' );
            unset( $payments[ 'coupon' ] );
            $this->config->set_item( 'nexo_payments_types', $payments );
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
        $this->load->module_view( 'nexo', 'dashboard-footer' );
        $this->load->module_view( 'nexo', 'dashboard.notification-script' );
    }

    /**
     * Load Dashboard
    **/

    public function load_dashboard()
    {
        // var_dump( $this->uri->segment(5) );die;
        if( $this->uri->uri_string() != 'dashboard/nexo/about' && $this->uri->segment(5) != 'about' && $this->uri->uri_string() != 'dashboard/modules' ) {
            // Let's get serious
            if( ! Modules::get( 'nexo-updater' ) ) {
                $this->notice->push_notice( 
                '<div class="container">
                    <div class="jumbotron">
                        <h1>' . __( 'Activez Votre Copie', 'nexo' ) . '</h1>
                        <p>' . __( 'Le module <strong>NexoPOS Updater</strong> n\'est pas installé. Pour profiter des mises à jour automatique et d\'une assistance, veuillez <strong><a href="https://nexopos.com/how-to-install-a-module-in-tendoo-cms/" target="_blank">installer le module</a></strong> téléchargeable ici', 'nexo' ) . '</p>
                        <p><a class="btn btn-primary btn-lg" href="https://nexopos.com/product/nexo-updater" target="_blank" role="button">' . __( 'Télécharger le module', 'nexo' ) . '</a></p>
                    </div>
                </div>' );
            }

            else if( ! Modules::is_active( 'nexo-updater' ) ) {
                $this->notice->push_notice( 
                '<div class="container">
                    <div class="jumbotron">
                        <h1>' . __( 'Activez Votre Copie', 'nexo' ) . '</h1>
                        <p>' . __( 'Le module <strong>NexoPOS Updater</strong> n\'est pas activé. Pour profiter des mises à jour automatiques et d\'une assistance, veuillez activer le module.', 'nexo' ) . '</p>
                        <p><a class="btn btn-primary btn-lg" href="' . site_url([ 'dashboard', 'modules' ]) . '" role="button">' . __( 'Activer', 'nexo' ) . '</a></p>
                    </div>
                </div>' );
            }
        }
        
        $segments    = $this->uri->segment_array();
        
        if( @$segments[ 2 ] == 'stores' && @$segments[ 4 ] == null ) {

            $this->enqueue->js_namespace( 'dashboard_footer' );
            $this->enqueue->js( 'tendoo.widget.dragging' );

        }

        $this->enqueue->js_namespace( 'dashboard_header' );
        $bower_path     =    '../modules/nexo/bower_components/';
        // @since 3.1
        // $libraries[]    =    $bower_path . 'babel/browser.min';
        // $libraries[]    =    $bower_path . 'babel/browser-polyfill.min';

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
        $this->enqueue->js( '../modules/nexo/bower_components/angular-hotkeys/build/hotkeys.min' );
        // @since 3.1
        $this->enqueue->js( '../modules/nexo/bower_components/angular-sanitize/angular-sanitize.min' );
        $this->enqueue->js( '../modules/nexo/bower_components/tv4/tv4' );
        $this->enqueue->js( '../modules/nexo/bower_components/objectpath/lib/ObjectPath' );
        $this->enqueue->js( '../modules/nexo/bower_components/angular-schema-form/dist/schema-form.min' );
        $this->enqueue->js( '../modules/nexo/bower_components/angular-schema-form/dist/bootstrap-decorator.min' );
        $this->enqueue->js( '../modules/nexo/bower_components/np-autocomplete/src/np-autocomplete' );
        

        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( 'css/nexo-arrow', module_url( 'nexo' ) );
        $this->enqueue->css( '../modules/nexo/css/jquery-ui' );
        $this->enqueue->css( '../bower_components/angular-bootstrap-datetimepicker/src/css/datetimepicker' );
        $this->enqueue->css( '../modules/nexo/bower_components/bootstrap-toggle/css/bootstrap2-toggle.min' );
        $this->enqueue->css( '../modules/nexo/css/piecharts/piecharts' );
        $this->enqueue->css( '../modules/nexo/bower_components/angular-hotkeys/build/hotkeys.min' );

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

        // @since 3.0.19
        $this->events->do_action( 'nexo_loaded' );

        // @since 3.1.3
        if( @$Options[ store_prefix() . 'nexo_enable_stock_warning' ] == 'yes' ) {
            $cache  =   new CI_Cache( array( 'adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' ) );
            if( $itemsOutOfStock    =   $cache->get( store_prefix() . 'items_out_of_stock' ) ) {
                foreach( $itemsOutOfStock as $item ) {
                    nexo_notices([
                        'message'   =>  sprintf( __( 'Le stock du produit <strong>%s</strong> est faible. Cliquez-ici pour accéder au produit.', 'nexo' ), @$item[ 'design' ] ),
                        'user_id'   =>  User::id(),
                        'icon'      =>  'fa fa-warning',
                        'type'      =>  'text-info',
                        'link'      =>  site_url( array( 'dashboard', store_prefix(), 'nexo', 'produits', 'lists', 'edit', $item[ 'id' ] ) ),
                    ]);
                }
                $cache->delete( store_prefix() . 'items_out_of_stock' );
            }
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
     * Dashboard Header
     * @echo string
    **/

    public function dashboard_header(){
        echo '<meta name="mobile-web-app-capable" content="yes">';
    }

}