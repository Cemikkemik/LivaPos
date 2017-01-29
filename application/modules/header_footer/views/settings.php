<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'header_footer',
    'title'     =>  __( 'Header And Footer', 'header_footer' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'  =>  [
            'label' =>  __( 'Save Settings' )
        ]
    ]
) );

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'label'        =>    __('Header Code', 'header_footer'),
    'name'        =>    'header_code',
    'placeholder'    =>    __( 'Header Code', 'header_footer')
), 'header_footer', 1 );

$this->Gui->add_item(array(
    'type'        =>    'textarea',
    'label'        =>    __('Footer Script', 'header_footer'),
    'name'        =>    'footer_script',
    'placeholder'    =>    __( 'Footer Script', 'header_footer')
), 'header_footer', 1 );

$this->Gui->output();
