<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'alvaro_cashier',
    'title'     =>  __( 'Print Report', 'alvaro' ),
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
    'type'              =>      'dom',
    'content'           =>      $this->load->module_view( 'alvaro', 'sales_detailed_dom', null, true )
), 'alvaro_cashier', 1 );

$this->Gui->output();
