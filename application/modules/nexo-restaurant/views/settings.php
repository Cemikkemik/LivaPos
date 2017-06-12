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

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Kitchen Refresh Interval', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'refreshing_seconds',
    'placeholder'    =>    __( 'Set refresh in seconds', 'nexo-restaurant' ),
    'description'   =>  __( 'After how many time (seconds) the order should be refreshed on the kitchen.')
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'          =>      'select',
    'optionns'      =>      [
        'foo'       =>      'bar'
    ],
    'label'         =>      __('Kitchen for Take away', 'nexo-restaurant'),
    'name'          =>      store_prefix() . 'kitchen_for_take_away',
    'placeholder'   =>      __( 'Set refresh in seconds', 'nexo-restaurant' ),
    'description'   =>      __( 'You can set where all.')
), 'nexo-restaurant-settings', 1 );

$this->Gui->output();
