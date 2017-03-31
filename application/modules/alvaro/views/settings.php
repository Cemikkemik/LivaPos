<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'alvaro',
    'title'     =>  __( 'Alvaro Settings', 'alvaro' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'    =>  [
            'label' =>  __( 'Save Settings', 'alvaro' )
        ]
    ]
) );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Opening Time', 'alvaro'),
    'name'        =>    store_prefix() . 'opening_time',
    'placeholder'    =>    '',
    'description'   =>  __( 'Define when the calendar should start.', 'alvaro')
), 'alvaro', 1 );

$this->Gui->add_item(array(
    'type'          =>    'text',
    'label'         =>    __('Closing Time', 'alvaro'),
    'name'          =>    store_prefix() . 'closing_time',
    'placeholder'   =>    '',
    'description'   =>  __( 'Define when the calendar should end.', 'alvaro')
), 'alvaro', 1 );

$this->Gui->add_item(array(
    'type'          =>    'text',
    'label'         =>    __('Interval between day times', ''),
    'name'          =>    store_prefix() . 'time_interval',
    'description'   =>    __( 'Let you define the time interval between minutes.', 'alvaro' ),
    'placeholder'   =>    ''
), 'alvaro', 1 );

$this->Gui->output();
