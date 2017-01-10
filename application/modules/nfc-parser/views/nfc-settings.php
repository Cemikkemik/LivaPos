<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 2);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'nfc_settings',
    'title'     =>  __( 'NFC Settings', 'nfc-parser' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  [ 'submit'  => [
        'label' =>  __( 'Save Settings' )
    ] ]
) );

$this->Gui->add_item(array(
    'type'        =>    'select',
    'name'        =>    'nfc_enable',
    'label'        =>    __( 'Enable NFC Parsing', 'nfc-parser'),
    'options'    =>    array(
        ''      =>  __( 'Default', 'nfc-parer' ),
        'yes'   =>  __( 'Yes', 'nfc-parser'),
        'no'    =>  __( 'No', 'nfc-parser' )
    )
), 'nfc_settings', 1 );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __( 'Slug', 'nfc-parser'),
    'name'        =>    'nfc_slug',
    'placeholder'    =>    __( 'URL To listen', 'nfc-parser' ),
    'description'   =>  sprintf( __( 'Please provide a basic URL from where the module show get NFC input. It may be something like this %s, so that all NFC Tag parsing generate an URL using that basic URL.' ), base_url() . "<strong>nfc_input</strong>" )
), 'nfc_settings', 1 );

$this->Gui->output();
