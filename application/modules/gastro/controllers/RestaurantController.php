<?php
class RestaurantController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Settings
     *  @param void
     *  @return void
    **/

    public function settings()
    {
        $this->Gui->set_title( store_title( __( 'Restaurant Settings', 'gastro' ) ) );
        $this->load->module_view( 'gastro', 'settings' );
    }

    /**
     * Revoke
     */
    public function revoke()
    {
        global $Options;
        if( ! empty( $_GET[ 'app_code' ] ) ) {
            if( $_GET[ 'app_code' ] == @$Options[ store_prefix() . 'nexopos_app_code' ] ) {
                $this->options->delete( store_prefix() . 'nexopos_app_code' );
                return redirect([ 'dashboard', store_slug(), 'gastro', 'settings?notice=app_code_deleted' ]); 
            }
        }
        return redirect([ 'dashboard', store_slug(), 'gastro', 'settings?notice=unknow_app' ]); 
    }

    /**
     * NexoPOS restaurant Callback
     * 
     * @return void
    **/

    public function callback()
    {
        if( ! empty( @$_GET[ 'app_code' ] ) ) {
            // save app code
            $this->options->set( store_prefix() . 'nexopos_app_code', $_GET[ 'app_code' ], true );

            return redirect([ 'dashboard', store_slug(), 'gastro', 'settings?notice=app_connected' ]); 
        }
        return redirect([ 'dashboard', 'error', '404' ]);
    }

    /**
     *  table Selection
     *  @param void
     *  @return void
    **/

    public function templates( $template )
    {
        return $this->load->module_view( 'gastro', 'templates.' . $template );
    }
}