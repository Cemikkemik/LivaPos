<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		     =>    1,
     'namespace'	     =>    'provider_history'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'nexo', 'providers.history-dom', null, true )
), 'provider_history', 1 );

$this->Gui->output();