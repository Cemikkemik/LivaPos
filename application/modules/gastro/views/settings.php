<?php
global $Options;

defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->library( 'Curl' );
$this->load->module_model( 'gastro', 'Nexo_Restaurant_Kitchens', 'kitchens_model' );
$kitchens           =   get_instance()->kitchens_model->get();

$kitchens_options   =   [];
foreach( $kitchens as $kitchen ) {
    $kitchens_options[ $kitchen[ 'ID' ] ]   =   $kitchen[ 'NAME' ];
}

$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'gastro-settings',
    'type'      =>  'unwrapped',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'  =>  [
            'label' =>  __( 'Save Settings', 'gastro' )
        ]
    ]
) );

$this->Gui->add_meta( array(
    'col_id'    =>  2,
    'namespace' =>  'gastro-settings-2',
    'type'      =>  'unwrapped',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'  =>  [
            'label' =>  __( 'Save Settings', 'gastro' )
        ]
    ]
) );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h4>' . __( 'Kitchen Order Alert Pattern', 'gastro' ) . '</h4>' .
    '<p>' . __( 'All alert pattern must be correctly filled, otherwise it will be disabled. An order cannot be considered as Too Late before an order is considered as "Fresh". The minutes set must follow this rule : Fresh Order < Late Order < Too Later Order. All placeholder value will be used by default.', 'gastro' ) . '</p>'
), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Fresh Order (minutes)', 'gastro'),
    'name'        =>    store_prefix() . 'fresh_order_min',
    'placeholder'    =>    __( 'For example : 10', 'gastro' ),
    'description'    =>    __( 'an order is considered as fresh when it has been published during a specific amount of minutes.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Late Order (minutes)', 'gastro'),
    'name'        =>    store_prefix() . 'late_order_min',
    'placeholder'    =>    __( 'For example : 20', 'gastro' ),
    'description'    =>    __( 'An order is considered as long when it has been published after a specific amount of minutes', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Too Late Order (minutes)', 'gastro'),
    'name'        =>    store_prefix() . 'too_late_order_min',
    'placeholder'    =>    __( 'For example : 30', 'gastro' ),
    'description'    =>    __( 'An order is considered as too late when it has been published after a specific amount of minutes.', 'gastro' )
), 'gastro-settings', 1 );

$color_options          =   [
    'box-default'    =>  __( 'Default', 'nexo' ),
    'bg-info box-info'       =>  __( 'Blue', 'nexo'),
    'bg-warning box-warning'       =>  __( 'Orange', 'nexo'),
    'bg-danger box-danger'       =>  __( 'Red', 'nexo'),
    'bg-success box-success'       =>  __( 'Green', 'nexo'),
];

$this->Gui->add_item(array(
    'type'        =>    'select',
    'options'       =>  $color_options,
    'label'        =>    __('Fresh Order Theme', 'gastro'),
    'placeholder'    =>    __( 'For example : #FFF', 'gastro' ),
    'name'        =>    store_prefix() . 'fresh_order_color',
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'options'       =>  $color_options,
    'label'        =>    __('Late Order Theme', 'gastro'),
    'name'        =>    store_prefix() . 'late_order_color',
    'placeholder'    =>    __( 'For example : #F5A4A4', 'gastro' ),
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'options'       =>  $color_options,
    'label'        =>    __('Too Late Order Theme', 'gastro'),
    'name'        =>    store_prefix() . 'too_late_order_color',
    'placeholder'    =>    __( 'For example : #DD1414', 'gastro' ),
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'gastro-settings', 1 );

// $this->Gui->add_item(array(
//     'type'        =>    'text',
//     'label'        =>    __('Reservation Pattern', 'gastro'),
//     'name'        =>    store_prefix() . 'reservation_pattern',
//     'description'    =>    __( 'Use this to set a pattern of times (in minutes), separated with a comma, which can be used to set reservation duration time. Example : 30, 60, 120, 240.', 'gastro' )
// ), 'gastro-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Kitchen Refresh Interval', 'gastro'),
    'name'        =>    store_prefix() . 'refreshing_seconds',
    'placeholder'    =>    __( 'Set refresh in seconds', 'gastro' ),
    'description'   =>  __( 'After how many time (seconds) the order should be refreshed on the kitchen.')
), 'gastro-settings', 1 );

// $this->Gui->add_item( array(
//     'type' =>    'select',
//     'name' =>	store_prefix() . 'disable_meal_feature',
//     'options'     =>  [
//         0           =>  __( 'Please select an option', 'gastro' ),
//         'yes'    =>     __( 'Yes', 'gastro' ),
//         'no'    =>  __( 'No', 'gastro' )
//     ],    
//     'label' =>   __( 'Disable Meal Feature', 'gastro' ),
//     'description' =>   __( 'You can disable the meal feature which allow to send grouped item into meal to the kitchen.', 'gastro' )
// ), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h4>' . __( 'Feature List', 'gastro' ) . '</h4>'
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_kitchen_screen',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Kitchen Screen', 'gastro' ),
    'description' =>   __( 'You can disable the kitchen screen. This will disable food status.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_waiter_screen',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Waiter Screen', 'gastro' ),
    'description' =>   __( 'You can disable the waiter screen.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_pos_waiter',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Waiter Screen on POS', 'gastro' ),
    'description' =>   __( 'Disable the waiter screen feature on POS.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_kitchen_print',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Kitchen Print', 'gastro' ),
    'description' =>   __( 'All order proceeded from the POS system is send by default to the kitchen. You can disale this feature from here.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_area_rooms',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Area and Rooms', 'gastro' ),
    'description' =>   __( 'If you want to make the table management easier, you can disable the area and rooms feature. You can disale this feature from here.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'takeaway_kitchen',
    'options'     =>  $kitchens_options,    
    'label' =>   __( 'Take Away Kitchen', 'gastro' ),
    'description' =>   __( 'All take away order will be send to that kitchen.', 'gastro' )
), 'gastro-settings', 1 );

$this->Gui->add_item( array(
    'type'              =>    'text',
    'name'              =>	store_prefix() . 'restaurant_envato_licence',
    'label'             =>   __( 'Envato Licence', 'gastro' ),
    'description'       =>   __( 'Enter your envato licence here for NexoPOS Restaurant Extension. If that field is not set, the cloud print may not work.', 'gastro' ),
    'placeholder'       =>   __( 'Envato Licence', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type'          =>  'text',
    'name'          =>	store_prefix() . 'printer_gpc_proxy',
    'label'         =>  __( 'Printer Proxy', 'gastro' ),
    'description'   =>  __( 'Learn how to get the printer proxy <a href="https://nexopos.com/how-to-get-the-printer-proxy">here</a>. If that field is not set, the cloud print may not work.', 'gastro' ),
    'placeholder'   =>  __( 'Printer Proxy', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h3>' . __( 'Printing Options', 'gastro' ) . '</h3>'
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'printing_option',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'kitchen_printers'      =>  __( 'Kitchen Printers', 'gastro' ),
        'single_printer'        =>  __( 'Single Printer', 'gastro' )
    ],    
    'label' =>   __( 'Print Option (Default: Single Printer)', 'gastro' ),
    'description' =>   __( 'You can choose whether you would like to use the printers assigned to each kitchen or you can use a single printer for all placed orders.', 'gastro' )
), 'gastro-settings-2', 2 );

// Add Print List
if( ! empty( @$Options[ store_prefix() . 'nexopos_app_code' ] ) && ! empty( $Options[ store_prefix() . 'printer_gpc_proxy' ] ) ) {
    
    $curl_raw           =   $this->curl->get( tendoo_config( 'nexo', 'store_url' ) . '/api/gcp/printers?app_code=' . $Options[ store_prefix() . 'nexopos_app_code' ] );
    $printers           =   json_decode( $curl_raw, true );
    $printers_options   =   [];
    
    // turn Raw to options
    foreach( ( array ) $printers[ 'printers' ] as $printer ) {
        $printers_options[ $printer[ 'id' ] ]   =   $printer[ 'displayName' ];
    }

    if( @$printers[ 'success' ] == true ) {
        $this->Gui->add_item( array(
            'type'          =>    'dom',
            'content'       =>    '<h3>' . __( 'Printers for kitchens', 'gastro' ) . '</h3>'
        ), 'gastro-settings-2', 2 );

        foreach( $kitchens as $kitchen ) {
            $this->Gui->add_item( array(
                'type'          =>  'select',
                'name'          =>	store_prefix() . 'printer_kitchen_' . $kitchen[ 'ID' ],
                'label'         =>  sprintf( __( 'Kitchen : %s', 'gastro' ), $kitchen[ 'NAME' ] ),
                'description'   =>  sprintf( __( 'Select a printer to a specific kitchen : %s', 'gastro' ), $kitchen[ 'NAME' ] ),
                'options'       =>  $printers_options
            ), 'gastro-settings-2', 2 );
        }

        $this->Gui->add_item( array(
            'type'          =>  'select',
            'name'          =>	store_prefix() . 'printer_takeway',
            'label'         =>  __( 'Default Printer', 'gastro' ),
            'description'   =>  __( 'Select a printer for a take away order.', 'gastro' ),
            'options'       =>  $printers_options
        ), 'gastro-settings-2', 2 );

        if( count( $kitchens ) == 0 ) {
            $this->Gui->add_item( array(
                'type'          =>    'dom',
                'content'       =>    tendoo_info( __( 'You don\'t have a kitchen to setup the printer', 'gastro' )  )
            ), 'gastro-settings-2', 2 );
        }
    } else {
        $this->Gui->add_item( array(
            'type'          =>    'dom',
            'content'       =>    tendoo_info( __( 'Unable to retreive printers from your Google Account.', 'gastro' )  )
        ), 'gastro-settings-2', 2 );
    }
}

if( empty( @$Options[ store_prefix() . 'nexopos_app_code' ] ) ) {
    $this->Gui->add_item( array(
        'type'          =>    'dom',
        'content'       =>    $this->load->module_view( 'gastro', 'login-btn', null, true )
    ), 'gastro-settings-2', 2 );
}

if( ! empty( @$Options[ store_prefix() . 'nexopos_app_code' ] ) ) {
    $this->Gui->add_item( array(
        'type'          =>    'dom',
        'content'       =>    $this->load->module_view( 'gastro', 'revoke-btn', null, true )
    ), 'gastro-settings-2', 2 );
}


$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h3>' . __( 'Speech Synthesizer', 'gastro' ) . '</h3>'
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'enable_kitchen_synthesizer',
    'options'     =>  [
        ''      =>  __( 'Choose a value', 'gastro' ),
        'yes'   =>  __( 'yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Enable Kitchen Synthesizer', 'gastro' ),
    'description' =>   __( 'The kitchen view will receive a vocal notice when an order is placed. <a href="https://developer.mozilla.org/fr/docs/Web/API/Window/speechSynthesis#Browser_compatibility">Your browser need to be compatible with</a>.', 'gastro' )
), 'gastro-settings-2', 2 );

/**
 * New Disable Options
**/

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_takeaway',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Take Away', 'gastro' ),
    'description' =>   __( 'If the Take away order type is not in use, you can disable it.', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_dinein',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Dine in', 'gastro' ),
    'description' =>   __( 'If the Dine In order type is not in use, you can disable it.', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_delivery',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Delivery', 'gastro' ),
    'description' =>   __( 'If the Delivery order type is not in use, you can disable it.', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_readyorders',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Ready Order Button', 'gastro' ),
    'description' =>   __( 'This option just let you disable the ready orders button form the new operation popup.', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_pendingorders',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Pending Orders Button', 'gastro' ),
    'description' =>   __( 'This option let you disable the pending orders from the new operation popup.', 'gastro' )
), 'gastro-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_saleslist',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'gastro' ),
        'yes'    =>     __( 'Yes', 'gastro' ),
        'no'    =>  __( 'No', 'gastro' )
    ],    
    'label' =>   __( 'Disable Sale List button', 'gastro' ),
    'description' =>   __( 'Hi the sales list button on the new operation popup.', 'gastro' )
), 'gastro-settings-2', 2 );

// $this->Gui->add_item( array(
//     'type' =>    'select',
//     'name' =>	store_prefix() . 'disable_booking',
//     'options'     =>  [
//         0           =>  __( 'Please select an option', 'gastro' ),
//         'yes'    =>     __( 'Yes', 'gastro' ),
//         'no'    =>  __( 'No', 'gastro' )
//     ],    
//     'label' =>   __( 'Disable Booking', 'gastro' ),
//     'description' =>   __( 'If the Booking order type is not in use, you can disable it.', 'gastro' )
// ), 'gastro-settings-2', 1 );

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    $this->load->module_view( 'gastro', 'synthesis.settings-wrapper', null, true )
), 'alvaro_log', 1 );

$this->Gui->output();
