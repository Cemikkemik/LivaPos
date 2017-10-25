<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		     =>    1,
     'namespace'	     =>    'perm_manager'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'perm_manager', 'index-dom', null, true )
), 'perm_manager', 1 );

$this->Gui->output();