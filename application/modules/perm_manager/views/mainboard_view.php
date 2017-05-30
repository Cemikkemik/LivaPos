<?php 
    defined('BASEPATH') or exit('No direct script access allowed');

    $this->Gui->col_width(1,4);

    $this->Gui->add_meta(array(
        'namespace'        =>    'perm_manager',
        'col_id'           =>    1,
        'type'             =>    'unwrapped'
    ));

    $this->Gui->add_item(array(
        'type'            =>    'dom',
        'content'         =>    $this->load->module_view( 'perm_manager', 'mainboard_dom', null, true )
    ), 'perm_manager', 1);

    $this->Gui->output();
