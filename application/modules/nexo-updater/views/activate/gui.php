<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nexo-updater'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'nexo-updater', 'activate.dom', null, true )
), 'nexo-updater', 1 );

$this->Gui->output();