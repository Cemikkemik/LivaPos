<?php
$this->Gui->col_width( 1, 2 );

$this->Gui->add_meta( array(
    'namespace'         =>      'content_management',
    'title'             =>      __( 'Import From Store', 'nexo' ),
    'type'              =>      'box',
    'col_id'            =>      1,
    'gui_saver'         =>      false,
    'use_namespace'     =>      false,
    'footer'            =>      array(
        'submit'        =>      array(
            'label'     =>      __( 'Copy Content', 'nexo' )
        )
    )
) );

$stores_data    =   array();

foreach( $stores as $store ) {
    $stores_data[ $store[ 'ID' ] ]      =   $store[ 'NAME' ];
}

$this->Gui->add_item(array(
    'type'          =>    'select',
    'name'          =>    'store_id',
    'label'         =>    __( 'Select Store', 'nsam' ),
    'options'       =>    $stores_data
), 'content_management', 1 );

$this->Gui->add_item(array(
    'type'          =>    'multiple',
    'name'          =>    'content',
    'label'         =>    __( 'Content to Copy', 'nsam' ),
    'options'       =>    array(
        'customers'     =>    __( 'Customers', 'nsam' ),
        'customers_g'   =>    __( 'Customers Groups', 'nsam' ),
        'articles'      =>    __( 'Products', 'nsam' ),
        'categories'    =>    __( 'Categories', 'nsam' ),
        'suppliers'     =>    __( 'Suppliers', 'nsam' ),
        'radius'        =>    __( 'Radius', 'nsam' ),
        'deliveries'    =>    __( 'Deliveries', 'nsam' )
    )
), 'content_management', 1 );

$this->Gui->add_item( array(
'type'        =>    'dom',
'content'    =>    $this->load->module_view( 'nsam', 'copy_content_script', null, true )
), 'content_management', 1 );

$this->Gui->output();
