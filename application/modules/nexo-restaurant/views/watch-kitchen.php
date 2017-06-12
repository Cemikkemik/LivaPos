<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'kitchen',
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
'type'        =>    'dom',
'content'    =>     $this->load->module_view( 'nexo-restaurant', 'watch-kitchen-dom', null, true )
), 'kitchen', 1 );

$this->Gui->output();
