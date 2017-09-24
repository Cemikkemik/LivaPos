<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
     'type'			=>    'unwrapped',
     'col_id'		=>    1,
     'namespace'	=>    'nexo_premium_provider_pay'
));

$this->Gui->add_item( array(
     'type'          =>    'dom',
     'content'       =>    $this->load->module_view( 'nexo_premium', 'providers.pay-dom', null, true )
), 'nexo_premium_provider_pay', 1 );

$this->Gui->output();