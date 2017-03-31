<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 2);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'nexo-restaurant-settings',
    'type'      =>  'unwrapped',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'  =>  [
            'label' =>  __( 'Save Settings', 'nexo-restaurant' )
        ]
    ]
) );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Reservation Pattern', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'reservation_pattern',
    'description'    =>    __( 'Use this to set a pattern of times (in minutes), separated with a comma, which can be used to set reservation duration time. Example : 30, 60, 120, 240.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->output();
