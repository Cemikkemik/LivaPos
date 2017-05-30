<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'commission_report',
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
'type'        =>    'dom',
'content'    =>    $this->load->module_view( 'alvaro', 'commission_report_dom', null, true )
), 'commission_report', 1 );

$this->Gui->output();
