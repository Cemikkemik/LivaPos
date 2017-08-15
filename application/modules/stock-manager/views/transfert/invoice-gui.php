<?php
$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'			=>    'unwrapped',
    'col_id'		=>    1,
    'namespace'	=>    'transfert_history'
));

$keys                =   [ 
    'site_name'                 =>      'store_name', 
    'nexo_shop_phone'           =>      'store_phone', 
    'nexo_shop_street'          =>      'store_address', 
    'nexo_shop_pobox'           =>      'store_pobox', 
    'nexo_shop_email'           =>      'store_email', 
    'nexo_shop_fax'             =>      'store_fax'
];

$template           =   [];
if( intval( $transfert[0][ 'FROM_STORE' ] ) == get_store_id() ) {
    foreach( [ 'site_name', 'nexo_shop_phone', 'nexo_shop_street', 'nexo_shop_pobox', 'nexo_shop_email', 'nexo_shop_fax' ] as $option ) {
        $template[ 'provider_' . $keys[ $option ] ]    =   store_option( $option );
    }
} else {
    $prefix         =   '';
    if( intval( $transfert[0][ 'FROM_STORE' ] ) != 0 ) {
        $prefix     =   'store_' . $transfert[0][ 'FROM_STORE' ] . '_'; 
    }

    foreach( [ 'site_name', 'nexo_shop_phone', 'nexo_shop_street', 'nexo_shop_pobox', 'nexo_shop_email', 'nexo_shop_fax' ] as $option ) {
        $template[ 'provider_' . $keys[ $option ] ]    =   store_option( $prefix . $option );
    }
}


if( intval( $transfert[0][ 'DESTINATION_STORE' ] ) == get_store_id() ) {
    foreach( [ 'site_name', 'nexo_shop_phone', 'nexo_shop_street', 'nexo_shop_pobox', 'nexo_shop_email', 'nexo_shop_fax' ] as $option ) {
        $template[ 'receiver_' . $keys[ $option ] ]    =   store_option( $option );
    }
} else {
    $prefix         =   '';
    if( intval( $transfert[0][ 'DESTINATION_STORE' ] ) != 0 ) {
        $prefix     =   'store_' . $transfert[0][ 'DESTINATION_STORE' ] . '_'; 
    }
    
    foreach( [ 'site_name', 'nexo_shop_phone', 'nexo_shop_street', 'nexo_shop_pobox', 'nexo_shop_email', 'nexo_shop_fax' ] as $option ) {
        $template[ 'receiver_' . $keys[ $option ] ]    =   store_option( $prefix . $option );
    }
}
// get received options
$template       =   [ $template ];

$this->Gui->add_item( array(
    'type'          =>    'dom',
    'content'       =>    $this->parser->parse( '../modules/stock-manager/views/transfert/invoice-dom', compact( 'transfert', 'items', 'template' ), true )
), 'transfert_history', 1 );

$this->Gui->output();