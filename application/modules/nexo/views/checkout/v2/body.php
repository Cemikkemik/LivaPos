<?php
$this->Gui->col_width(1, 2);

$this->Gui->col_width(2, 2);

$this->Gui->add_meta(array(
    'type'            =>    'unwrapped',
    'col_id'        =>    1,
    'namespace'        =>    'checkout_v2_col1'
));

$this->Gui->add_meta(array(
    'type'            =>    'unwrapped',
    'col_id'        =>    2,
    'namespace'        =>    'checkout_v2_col2'
));

$data                =    array();
if (isset($order)) {
    $data            =    array( 'order' => $order );
};

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' => $this->load->module_view('nexo', 'checkout/v2/col_1', $data, true)
), 'checkout_v2_col1', 1);

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' => $this->load->module_view('nexo', 'checkout/v2/col_2', $data, true)
), 'checkout_v2_col2', 2);

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' => $this->load->module_view('nexo', 'checkout/v2/script', $data, true)
), 'checkout_v2_col2', 2);

$this->Gui->output();
