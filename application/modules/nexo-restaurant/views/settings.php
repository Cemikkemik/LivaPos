<?php
global $Options;

defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->library( 'Curl' );
$this->load->module_model( 'nexo-restaurant', 'Nexo_Restaurant_Kitchens', 'kitchens_model' );
$kitchens           =   get_instance()->kitchens_model->get();

$kitchens_options   =   [];
foreach( $kitchens as $kitchen ) {
    $kitchens_options[ $kitchen[ 'ID' ] ]   =   $kitchen[ 'NAME' ];
}

$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

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

$this->Gui->add_meta( array(
    'col_id'    =>  2,
    'namespace' =>  'nexo-restaurant-settings-2',
    'type'      =>  'unwrapped',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'  =>  [
            'label' =>  __( 'Save Settings', 'nexo-restaurant' )
        ]
    ]
) );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h4>' . __( 'Kitchen Order Alert Pattern', 'nexo-restaurant' ) . '</h4>' .
    '<p>' . __( 'All alert pattern must be correctly filled, otherwise it will be disabled. An order cannot be considered as Too Late before an order is considered as "Fresh". The minutes set must follow this rule : Fresh Order < Late Order < Too Later Order. All placeholder value will be used by default.', 'nexo-restaurant' ) . '</p>'
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Fresh Order (minutes)', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'fresh_order_min',
    'placeholder'    =>    __( 'For example : 10', 'nexo-restaurant' ),
    'description'    =>    __( 'an order is considered as fresh when it has been published during a specific amount of minutes.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Late Order (minutes)', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'late_order_min',
    'placeholder'    =>    __( 'For example : 20', 'nexo-restaurant' ),
    'description'    =>    __( 'An order is considered as long when it has been published after a specific amount of minutes', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Too Late Order (minutes)', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'too_late_order_min',
    'placeholder'    =>    __( 'For example : 30', 'nexo-restaurant' ),
    'description'    =>    __( 'An order is considered as too late when it has been published after a specific amount of minutes.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

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
    'label'        =>    __('Fresh Order Theme', 'nexo-restaurant'),
    'placeholder'    =>    __( 'For example : #FFF', 'nexo-restaurant' ),
    'name'        =>    store_prefix() . 'fresh_order_color',
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'options'       =>  $color_options,
    'label'        =>    __('Late Order Theme', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'late_order_color',
    'placeholder'    =>    __( 'For example : #F5A4A4', 'nexo-restaurant' ),
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'options'       =>  $color_options,
    'label'        =>    __('Too Late Order Theme', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'too_late_order_color',
    'placeholder'    =>    __( 'For example : #DD1414', 'nexo-restaurant' ),
    'description'    =>    __( 'Select a theme for this alert pattern.')
), 'nexo-restaurant-settings', 1 );

// $this->Gui->add_item(array(
//     'type'        =>    'text',
//     'label'        =>    __('Reservation Pattern', 'nexo-restaurant'),
//     'name'        =>    store_prefix() . 'reservation_pattern',
//     'description'    =>    __( 'Use this to set a pattern of times (in minutes), separated with a comma, which can be used to set reservation duration time. Example : 30, 60, 120, 240.', 'nexo-restaurant' )
// ), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Kitchen Refresh Interval', 'nexo-restaurant'),
    'name'        =>    store_prefix() . 'refreshing_seconds',
    'placeholder'    =>    __( 'Set refresh in seconds', 'nexo-restaurant' ),
    'description'   =>  __( 'After how many time (seconds) the order should be refreshed on the kitchen.')
), 'nexo-restaurant-settings', 1 );

// $this->Gui->add_item( array(
//     'type' =>    'select',
//     'name' =>	store_prefix() . 'disable_meal_feature',
//     'options'     =>  [
//         0           =>  __( 'Please select an option', 'nexo-restaurant' ),
//         'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
//         'no'    =>  __( 'No', 'nexo-restaurant' )
//     ],    
//     'label' =>   __( 'Disable Meal Feature', 'nexo-restaurant' ),
//     'description' =>   __( 'You can disable the meal feature which allow to send grouped item into meal to the kitchen.', 'nexo-restaurant' )
// ), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h4>' . __( 'Feature List', 'nexo-restaurant' ) . '</h4>'
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_kitchen_screen',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Kitchen Screen', 'nexo-restaurant' ),
    'description' =>   __( 'You can disable the kitchen screen. This will disable food status.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_kitchen_print',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Kitchen Print', 'nexo-restaurant' ),
    'description' =>   __( 'All order proceeded from the POS system is send by default to the kitchen. You can disale this feature from here.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_area_rooms',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Area and Rooms', 'nexo-restaurant' ),
    'description' =>   __( 'If you want to make the table management easier, you can disable the area and rooms feature. You can disale this feature from here.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'takeaway_kitchen',
    'options'     =>  $kitchens_options,    
    'label' =>   __( 'Take Away Kitchen', 'nexo-restaurant' ),
    'description' =>   __( 'All take away order will be send to that kitchen.', 'nexo-restaurant' )
), 'nexo-restaurant-settings', 1 );

$this->Gui->add_item( array(
    'type'              =>    'text',
    'name'              =>	store_prefix() . 'restaurant_envato_licence',
    'label'             =>   __( 'Envato Licence', 'nexo-restaurant' ),
    'description'       =>   __( 'Enter your envato licence here for NexoPOS Restaurant Extension. If that field is not set, the cloud print may not work.', 'nexo-restaurant' ),
    'placeholder'       =>   __( 'Envato Licence', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type'          =>  'text',
    'name'          =>	store_prefix() . 'printer_gpc_proxy',
    'label'         =>  __( 'Printer Proxy', 'nexo-restaurant' ),
    'description'   =>  __( 'Learn how to get the printer proxy <a href="https://nexopos.com/how-to-get-the-printer-proxy">here</a>. If that field is not set, the cloud print may not work.', 'nexo-restaurant' ),
    'placeholder'   =>  __( 'Printer Proxy', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h3>' . __( 'Printing Options', 'nexo-restaurant' ) . '</h3>'
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'printing_option',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'kitchen_printers'      =>  __( 'Kitchen Printers', 'nexo-restaurant' ),
        'single_printer'        =>  __( 'Single Printer', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Print Option (Default: Single Printer)', 'nexo-restaurant' ),
    'description' =>   __( 'You can choose whether you would like to use the printers assigned to each kitchen or you can use a single printer for all placed orders.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

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
            'content'       =>    '<h3>' . __( 'Printers for kitchens', 'nexo-restaurant' ) . '</h3>'
        ), 'nexo-restaurant-settings-2', 2 );

        foreach( $kitchens as $kitchen ) {
            $this->Gui->add_item( array(
                'type'          =>  'select',
                'name'          =>	store_prefix() . 'printer_kitchen_' . $kitchen[ 'ID' ],
                'label'         =>  sprintf( __( 'Kitchen : %s', 'nexo-restaurant' ), $kitchen[ 'NAME' ] ),
                'description'   =>  sprintf( __( 'Select a printer to a specific kitchen : %s', 'nexo-restaurant' ), $kitchen[ 'NAME' ] ),
                'options'       =>  $printers_options
            ), 'nexo-restaurant-settings-2', 2 );
        }

        $this->Gui->add_item( array(
            'type'          =>  'select',
            'name'          =>	store_prefix() . 'printer_takeway',
            'label'         =>  __( 'Default Printer', 'nexo-restaurant' ),
            'description'   =>  __( 'Select a printer for a take away order.', 'nexo-restaurant' ),
            'options'       =>  $printers_options
        ), 'nexo-restaurant-settings-2', 2 );

        if( count( $kitchens ) == 0 ) {
            $this->Gui->add_item( array(
                'type'          =>    'dom',
                'content'       =>    tendoo_info( __( 'You don\'t have a kitchen to setup the printer', 'nexo-restaurant' )  )
            ), 'nexo-restaurant-settings-2', 2 );
        }
    } else {
        $this->Gui->add_item( array(
            'type'          =>    'dom',
            'content'       =>    tendoo_info( __( 'Unable to retreive printers from your Google Account.', 'nexo-restaurant' )  )
        ), 'nexo-restaurant-settings-2', 2 );
    }
}

if( empty( @$Options[ store_prefix() . 'nexopos_app_code' ] ) ) {
    $this->Gui->add_item( array(
        'type'          =>    'dom',
        'content'       =>    $this->load->module_view( 'nexo-restaurant', 'login-btn', null, true )
    ), 'nexo-restaurant-settings-2', 2 );
}

if( ! empty( @$Options[ store_prefix() . 'nexopos_app_code' ] ) ) {
    $this->Gui->add_item( array(
        'type'          =>    'dom',
        'content'       =>    $this->load->module_view( 'nexo-restaurant', 'revoke-btn', null, true )
    ), 'nexo-restaurant-settings-2', 2 );
}


$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    '<h3>' . __( 'Speech Synthesizer', 'nexo-restaurant' ) . '</h3>'
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'enable_kitchen_synthesizer',
    'options'     =>  [
        ''      =>  __( 'Choose a value', 'nexo-restaurant' ),
        'yes'   =>  __( 'yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Enable Kitchen Synthesizer', 'nexo-restaurant' ),
    'description' =>   __( 'The kitchen view will receive a vocal notice when an order is placed. <a href="https://developer.mozilla.org/fr/docs/Web/API/Window/speechSynthesis#Browser_compatibility">Your browser need to be compatible with</a>.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

/**
 * New Disable Options
**/

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_takeaway',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Take Away', 'nexo-restaurant' ),
    'description' =>   __( 'If the Take away order type is not in use, you can disable it.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_dinein',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Dine in', 'nexo-restaurant' ),
    'description' =>   __( 'If the Dine In order type is not in use, you can disable it.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_delivery',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Delivery', 'nexo-restaurant' ),
    'description' =>   __( 'If the Delivery order type is not in use, you can disable it.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_readyorders',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Ready Order Button', 'nexo-restaurant' ),
    'description' =>   __( 'This option just let you disable the ready orders button form the new operation popup.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_pendingorders',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Pending Orders Button', 'nexo-restaurant' ),
    'description' =>   __( 'This option let you disable the pending orders from the new operation popup.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_saleslist',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Sale List button', 'nexo-restaurant' ),
    'description' =>   __( 'Hi the sales list button on the new operation popup.', 'nexo-restaurant' )
), 'nexo-restaurant-settings-2', 2 );

// $this->Gui->add_item( array(
//     'type' =>    'select',
//     'name' =>	store_prefix() . 'disable_booking',
//     'options'     =>  [
//         0           =>  __( 'Please select an option', 'nexo-restaurant' ),
//         'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
//         'no'    =>  __( 'No', 'nexo-restaurant' )
//     ],    
//     'label' =>   __( 'Disable Booking', 'nexo-restaurant' ),
//     'description' =>   __( 'If the Booking order type is not in use, you can disable it.', 'nexo-restaurant' )
// ), 'nexo-restaurant-settings-2', 1 );

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    $this->load->module_view( 'nexo-restaurant', 'synthesis.settings-wrapper', null, true )
), 'alvaro_log', 1 );

$this->Gui->output();
