<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'register',
    'title'     =>  __( 'Register List', 'nexo_customer_display' ),
    'type'      =>  'box-primary'
) );

$this->Gui->add_item( array(
'type'        =>    'dom',
'content'    =>    $this->load->module_view( 'nexo_customer_display', 'register_list_dom', null, true )
), 'register', 1 );

$this->Gui->output();
