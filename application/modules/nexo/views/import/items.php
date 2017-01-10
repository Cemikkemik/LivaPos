<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'import_item',
    'title'     =>  __( 'Importer des articles par CSV', 'nexo' ),
    'type'      =>  'unwrapped'
) );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->module_view( 'nexo', 'import/items-dom', array(), true )
), 'import_item', 1 );

$this->Gui->output();
