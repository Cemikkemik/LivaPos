<?php
$this->Gui->col_width( 1, 2 );

$this->Gui->col_width( 2, 2 );

// Create meta
$this->Gui->add_meta( array(
	'title'	=>	__( 'Réglages SMS', 'nexo_sms' ),
	'namespace'	=>	'nexo_sms',
	'gui_saver'	=>	true,
	'type'		=>	'box',
	'col_id'	=>	1,
	'footer'        =>        array(
        'submit'    =>        array(
            'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
        )
    )
) );

$this->Gui->add_item( array(
	'type'		=>	'select',
	'name'		=>	'nexo_sms_service',
	'options'	=>	$this->config->item( 'nexo_sms_providers' ),
	'label'		=>	__( 'Veuillez choisir votre fournisseurs SMS', 'nexo_sms' )
), 'nexo_sms', 1 );

// If Twilio is enabled
if( in_array( 'twilio', array_keys( $this->config->item( 'nexo_sms_providers' ) ) ) ) {

	// Meta for Twilio
	$this->Gui->add_meta( array(
		'title'			=>	__( 'Réglages Twilio', 'nexo_sms' ),
		'namespace'		=>	'nexo_twilio',
		'gui_saver'		=>	true,
		'type'			=>	'box',
		'col_id'		=> 2,
		'footer'        =>        array(
			'submit'    =>        array(
				'label'    =>        __('Sauvegarder les réglages', 'nexo_sms')
			)
		)
	) );
	
	$this->Gui->add_item( array(
		'type'			=>	'text',
		'name'			=>	'nexo_twilio_account_sid',
		'label'			=>	__( 'SID du compte', 'nexo_sms' ),
		'description'	=>	sprintf( __( 'Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms' ), 'http://twilio.com/console' )
	), 'nexo_twilio', 2 );
	
	$this->Gui->add_item( array(
		'type'			=>	'text',
		'name'			=>	'nexo_twilio_account_token',
		'label'			=>	__( 'Jeton d\'accès', 'nexo_sms' ),
		'description'	=>	sprintf( __( 'Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms' ), 'http://twilio.com/console' )
	), 'nexo_twilio', 2 );
	
	$this->Gui->add_item( array(
		'type'			=>	'text',
		'name'			=>	'nexo_twilio_from_number',
		'label'			=>	__( 'Numéro d\'envoi', 'nexo_sms' ),
		'description'	=>	sprintf( __( 'Récupérer les informations relatives aux clés sur votre <a href="%s">compte Twilio</a>.', 'nexo_sms' ), 'http://twilio.com/console' )
	), 'nexo_twilio', 2 );
	
	
}

$this->Gui->output();