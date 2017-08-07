<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	    =>    'new_supply'
));

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->module_view( 'nexo', 'items.stock-supply.new-ui-dom', null, true )
), 'new_supply', 1 );

$this->Gui->output();