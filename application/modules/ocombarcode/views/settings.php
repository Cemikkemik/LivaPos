<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->Gui->col_width(1, 4);

$this->Gui->add_meta( array(
    'col_id'    =>  1,
    'namespace' =>  'ocombarcode',
    'title'     =>  __( 'Réglages',  'ocombarcode' ),
    'type'      =>  'box',
    'gui_saver' =>  true,
    'footer'    =>  [
        'submit'    =>  [
            'label' =>  __( 'Enregistrer', 'ocombarcode' )
        ]
    ]
) );

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Suppression de caractères avant', 'ocombarcode'),
    'name'        =>    store_prefix() . 'delete_char_before',
    'placeholder'    =>    ''
), 'ocombarcode', 1);

$this->Gui->add_item(array(
    'type'        =>    'text',
    'label'        =>    __('Suppression de caractères après', 'ocombarcode'),
    'name'        =>    store_prefix() . 'delete_char_after',
    'placeholder'    =>    ''
), 'ocombarcode', 1);

$this->Gui->output();
