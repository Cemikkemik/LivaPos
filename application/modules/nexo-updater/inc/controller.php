<?php
// Nexo Updater
include_once( MODULESPATH . 'nexo-updater/vendor/autoload.php' );

class Nexo_Updater_Controller extends Tendoo_Module 
{
     public function __construct()
     {
          parent::__construct();
     }

     /**
      * Settings
      *
     **/

     public function activate()
     {
          if( $this->input->post( 'licence-code' ) != '' ) {
               $apiRequest    =    Requests::get('https://marketplace.envato.com/api/edge/blair_jersyer/5gpszcw93ufutpqb0q8ors1v9znclaf4/verify-purchase:' . $this->input->post( 'licence-code' ) . '.json');
               $licence       =    json_decode( $apiRequest->body, true );

               if( $this->input->post( 'licence-code' ) == '51d1e5da-49fa-4e14-9c08-5c5127b5ca78' ) {
                    $this->notice->push_notice( tendoo_error( __( 'This licence has been banned. Please contact us at contact@nexopos.com.', 'nexo-updater' ) ) );
               } else if( @$licence[ 'verify-purchase' ][ 'item_id' ] == '16195010' ) {
                    set_option( 'updater_validated', $this->input->post( 'licence-code' ) );
                    return redirect([ 'dashboard', 'nexo-updater', 'activated']);
               } else {
                    $this->notice->push_notice( tendoo_error( __( 'The licence is not valid. Please try again', 'nexo-updater' ) ) );
               }
               // if( @$licence->)
          }

          $this->Gui->set_title( __( 'Activate Your licence', 'nexo-updater' ) );
          $this->load->module_view( 'nexo-updater', 'activate.gui' );
     }

     /**
      * 
      * @return void
     **/

     public function activated()
     {
          $this->Gui->set_title( __( 'Thank You', 'nexo-updater' ) );
          $this->load->module_view( 'nexo-updater', 'activated.gui' );
     }
}