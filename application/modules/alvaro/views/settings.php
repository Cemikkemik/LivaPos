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

$this->Gui->add_item( array(
    'type' =>    'textarea',
    'name' =>	'calendario_sms_template',
    'label' =>   __( 'SMS Template', 'alvaro'),
    'description' =>   __( 'This template will be used while sending a reminder to the customer. 
    Don\'t forget to use :
    <ul>
        <li>{{customer_name}} to display the customer name.</li>
        <li>{{customer_phone}} to display the customer phone.</li>
        <li>{{store_name}} to display the shop name.</li>
        <li>{{store_phone}} to display the shop phone.</li>
        <li>{{order_total}} to display the order total.</li>
        <li>{{start_date}} to display when the appointment start</li>
    </ul>', 'alvaro' ),
    'placeholder' =>   ''
), 'alvaro', 1 );

$this->Gui->output();
