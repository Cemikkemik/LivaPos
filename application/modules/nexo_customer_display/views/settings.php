<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 2);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'cu_display_settings',
    'title'     =>  __( 'Settings', 'nexo_customer_display' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  array(
        'submit'    =>  array(
            'label' =>  __( 'Save Settings', 'nexo_customer_display' )
        )
    )
) );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    store_prefix() . 'logo_type',
    'label'        =>    __('Select the logo type', 'nexo_customer_display'),
    'options'    =>    array(
        'text'  =>  __( 'Text', 'nexo_customer_display'),
        'logo'  =>  __( 'Logo', 'nexo_customer_display' )
    )
), 'cu_display_settings',1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Link to logo', 'nexo_customer_display'),
    'name'        =>    store_prefix() . 'logo_url',
    'placeholder'    =>    ''
), 'cu_display_settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Logo text', 'nexo_customer_display'),
    'name'        =>    store_prefix() . 'logo_text',
    'placeholder'    =>    ''
), 'cu_display_settings', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Background Image', 'nexo_customer_display'),
    'name'        =>    'cu_display_background_url',
    'placeholder'    =>    ''
), 'cu_display_settings',1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Slide LifeTime', 'nexo_customer_display'),
    'name'        =>    store_prefix() . 'cud_display_slide_lifetime',
    'placeholder'    =>    '',
    'description'   =>  __( 'Let you set after how many time the next slides should appear.', 'nexo_customer_display' )
), 'cu_display_settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'editor',
    'label'        =>    __('Welcome Message', 'nexo_customer_display'),
    'name'        =>    store_prefix() . 'cu_display_welcome_message',
    'placeholder'    =>    ''
), 'cu_display_settings', 1);

$this->Gui->output();
