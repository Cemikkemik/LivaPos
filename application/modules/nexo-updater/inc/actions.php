<?php

include_once( dirname( __FILE__ ) . '/controller.php' );

class Nexo_Updater_Actions extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
     }

     /**
      * Load Dashboard
      * 
     **/

     public function load_dashboard()
     {
        if( $this->uri->uri_string() != 'dashboard/nexo/about' && $this->uri->segment(5) != 'about' && $this->uri->uri_string() != 'dashboard/modules' ) {
            if( get_option( 'updater_validated', 'no' ) === 'no' && $this->uri->uri_string() !== 'dashboard/nexo-updater/activate' ) {
                $this->notice->push_notice( 
                '<div class="container-fluid">
                    <div class="jumbotron">
                        <h1>' . __( 'Activate Your Copy', 'nexo' ) . '</h1>
                        <p>' . __( 'To take advantage of updates and support, you must provide a license to use on CodeCanyon.', 'nexo' ) . '</p>
                        <p><a class="btn btn-primary btn-lg" href="' . site_url([ 'dashboard', 'nexo-updater', 'activate' ]) . '" role="button">' . __( 'Validate A Licence', 'nexo' ) . '</a></p>
                    </div>
                </div>' );
            }
        }
        $this->Gui->register_page_object( 'nexo-updater', new Nexo_Updater_Controller );
     }

     /**
      * enable_module
      *
    **/

    public function do_enable_module( $namespace )
    {
        if( $namespace == 'nexo-updater' ) {
            return redirect([ 'dashboard', 'nexo-updater', 'activate' ]);
        }
    }
}