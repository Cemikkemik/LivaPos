<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'detailed',
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->load->module_view( 'nexo_premium', 'sales_detailed_dom', array(
        'start_date'    =>  $start_date,
        'end_date'      =>  $end_date
    ), true )
), 'detailed', 1 );

$this->Gui->output();
