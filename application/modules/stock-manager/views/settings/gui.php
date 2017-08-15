<?php
$this->Gui->col_width(1, 2);
$this->Gui->col_width(2, 2);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	    =>    'tranfert_settings',
    'gui_saver'        =>        true,
    'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Save the settings', 'stock-manager' )
        )
    ),
    'use_namespace'    =>        false,
));

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    2,
    'namespace'	    =>    'tranfert_settings2'
));

$this->Gui->add_item( array(
    'type' =>    'textarea',
    'name' =>	store_prefix() . 'transfert_column_1',
    'label' =>   __( 'Left Column Settings', 'stock-manager' ),
    'description' =>   __( 'Customize the left column section for the transfert invoice.', 'stock-manager' ),
), 'tranfert_settings',  1 );

$this->Gui->add_item( array(
    'type' =>    'textarea',
    'name' =>	store_prefix() . 'transfert_column_2',
    'label' =>   __( 'Right Column Settings', 'stock-manager' ),
    'description' =>   __( 'Customize the right column section for the transfert invoice.', 'stock-manager' ),
), 'tranfert_settings',  1 );

$this->Gui->add_item( array(
    'type' =>    'select',
    'name' =>	store_prefix() . 'deduct_from_store',
    'label' =>      __( 'Deduct Stock', 'stock-manager' ),
    'description' =>   __( 'All item send won\'t be available when the stock is being send. Default Yes', 'stock-manager' ),
    'options' =>   [
        'yes'   =>  __( 'Yes', 'stock-manager' ),
        'no'    =>  __( 'No', 'stock-manager' )
    ]
), 'tranfert_settings', 1 );

$this->Gui->add_item( array(
'type'          =>    'dom',
'content'       =>    $this->load->module_view( 'stock-manager', 'settings.dom', null, true )
), 'tranfert_settings2', 2 );

$this->Gui->output();