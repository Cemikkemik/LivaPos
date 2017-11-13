<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'         =>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'gastro_waiter_screen'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'gastro', 'waiters-screen.dom', null, true )
), 'gastro_waiter_screen', 1 );

$this->Gui->output();