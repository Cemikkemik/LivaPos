<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	=>    'transfert_ui'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->module_view( 'stock-manager', 'transfert.dom', null, true )
), 'transfert_ui', 1 );


$this->Gui->output();