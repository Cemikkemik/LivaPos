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
    $data           	=    array(
		'order' 		=> 	$order,
		'register_id'	=>	$register_id
	);
};

$col_1_view             =   $this->events->apply_filters( 'nexo_checkout_col_1_view', [
    'module'        =>  'nexo',
    'path'          =>  'checkout/v2-1/col_1'
]);

$col_2_view             =   $this->events->apply_filters( 'nexo_checkout_col_2_view', [
    'module'        =>  'nexo',
    'path'          =>  'checkout/v2-1/col_2'
]);

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' =>        $this->load->module_view( $col_1_view[ 'module' ], $col_1_view[ 'path' ], $data, true)
), 'checkout_v2_col1', 1);

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' =>        $this->load->module_view( $col_2_view[ 'module' ], $col_2_view[ 'path' ], $data, true)
), 'checkout_v2_col2', 2);

$this->Gui->add_item(array(
    'type'    =>    'dom',
    'content' =>        $this->load->module_view('nexo', 'checkout/v2-1/script', $data, true)
), 'checkout_v2_col2', 2);

$this->Gui->add_item([
    'type'      =>  'dom',
    'content'   =>  $this->load->module_view( 'nexo', 'checkout.v2-1.style', $data, true )
], 'checkout_v2_col2', 2 );

$this->Gui->output();
