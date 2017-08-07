<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	=>    'nexo_welcome'
));

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    $this->load->module_view( 'nexo', 'welcome.dom', null, true )
), 'nexo_welcome', 1 );


$this->Gui->output();