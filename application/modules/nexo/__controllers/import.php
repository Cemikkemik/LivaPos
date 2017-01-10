<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use League\Csv\Reader;

class Import extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        //Codeigniter : Write Less Do More
    }

    /**
     *  Item
     *  @param
     *  @return
    **/

    public function items()
    {
        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'nexo', 'import/script' );
        });

        $this->Gui->set_title( store_title( __( 'Importer des articles depuis un CSV', 'nexo' ) ) );
        $this->load->module_view( 'nexo', 'import/items' );
    }

}
