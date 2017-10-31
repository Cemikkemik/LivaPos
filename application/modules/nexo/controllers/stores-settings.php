<?php
class NexoStoreSettingsController extends CI_Model
{    
    public function settings($page = 'home')
    {
        if( User::cannot( 'nexo.manage.store-settings') ) {
            return nexo_access_denied();
        }
        
		global $PageNow;
		
		$PageNow		=	'nexo/stores-settings';
        
        $this->Gui->set_title( store_title( __( 'Réglages des boutiques', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'stores/main', array() );
    }
}