<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
'type'		=>    'unwrapped',
'col_id'	=>    1,
'namespace'	=>    'supply_gui'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->module_view( 'nexo', 'items.stock-supply.dom', null, true )
), 'supply_gui', 1 );

$this->Gui->output();
