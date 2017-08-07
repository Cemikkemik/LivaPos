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

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'disable_meal_feature',
    'options'     =>  [
        0           =>  __( 'Please select an option', 'nexo-restaurant' ),
        'yes'    =>     __( 'Yes', 'nexo-restaurant' ),
        'no'    =>  __( 'No', 'nexo-restaurant' )
    ],    
    'label' =>   __( 'Disable Meal Feature', 'nexo-restaurant' ),
    'description' =>   __( 'You can disable the meal feature which allow to send grouped item into meal to the kitchen.', 'nexo-restaurant' )
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

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    $this->load->module_view( 'nexo-restaurant', 'synthesis.settings-wrapper', null, true )
), 'alvaro_log', 1 );

$this->Gui->output();
