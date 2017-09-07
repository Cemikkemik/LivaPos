<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width( 1, 1 );

$this->load->model( 'Nexo_Stores' );

$Stores     =   $this->Nexo_Stores->get();

$Users      =   $this->auth->list_users([ 'shop_cashier', 'shop_manager', 'shop_tester' ]);

$col_id     =   1;

foreach( $Users as $User ) {

    if( $col_id     == 5 ) {
        $col_id     =   1;
    }

    $this->Gui->add_meta( array(
        'col_id'    =>  $col_id,
        'namespace' =>  'nsam_users_control' . $User->user_id,
        'user_id'   =>  $User->user_id,
        'type'      =>  'box',
        'title'     =>  __( $User->user_name, 'nsam' ) . ' (' . $User->group_name . ')',
        'footer'    =>  [
            'submit' =>  [
                'label'    =>  __( 'Save Settings', 'nsam' )
            ]
        ],
        'gui_saver' =>  true
    ) );

    foreach( $Stores as $store ) {
        $this->Gui->add_item([
            'type'      =>  'select',
            'name'      =>  'store_access_' . $User->user_id . '_' . $store[ 'ID' ],
            'label'     =>  sprintf( __( 'Access to %s', 'nsam' ), $store[ 'NAME' ] ),
            'options'   =>  [
                'none'      =>  __( 'Select an options', 'nsam' ),
                'no'    =>  __( 'No', 'nsam' ),
                'yes'   =>  __( 'Yes', 'nsam' )
            ]
        ], 'nsam_users_control' . $User->user_id, $col_id );
    }

    if( count( $Stores ) == 0 ) {
        $this->Gui->add_item( array(
            'type'        =>    'dom',
            'content'    =>    '<p><a href="' . site_url([ 'dashboard', 'nexo', 'stores', 'lists', 'add' ] ) . '">' . __( 'Create a store', 'nsam' ) . '</a></p>'
        ), 'nsam_users_control' . $User->user_id, $col_id );
    }

    $col_id++;
}

if( count( $Users ) == 0 ) {
    $this->Gui->add_meta( array(
        'col_id'    =>  1,
        'namespace' =>  'nsam_users_control',
        'type'      =>  'box',
        'title'     =>  __( 'No cashier available', 'nsam' ),
        'footer'    =>  [
            'submit' =>  [
                'label'    =>  __( 'Save Settings', 'nsam' )
            ]
        ],
        'gui_saver' =>  true
    ) );

    $this->Gui->add_item( array(
        'type'        =>    'dom',
        'content'    =>    '<p><a href="' . site_url([ 'dashboard', 'users', 'create' ] ) . '">' . __( 'Create a cashier', 'nsam' ) . '</a></p>'
    ), 'nsam_users_control', 1 );
}

$this->Gui->output();
