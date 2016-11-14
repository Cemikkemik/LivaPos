<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Gui Cols Width
$this->Gui->col_width(1, 4);

// Gui Meta
$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'slider',
    'title'     =>  __( 'Sliders', 'nexo_customer_display' ),
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
    'type'        =>    'dom',
    'content'    =>    $crud_content->output
), 'slider', 1 );

$this->Gui->output();
