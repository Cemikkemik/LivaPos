<?php

/**
 * SMS provider
**/

$config[ 'nexo_sms_providers' ]    =    array(
    'disable'                    =>    get_instance()->lang->line('disable'),
    'twilio'                    =>    get_instance()->lang->line('nexo_twilio_service'),
    // 'plivo'						=>	 get_instance()->lang->line('nexo_plivo_service'),
    'bulksms'                    =>    get_instance()->lang->line('nexo_bulksms_service'),
);

/**
 * Default SMS templating
**/

$config[ 'default_sms_invoice_template' ]        =    get_instance()->lang->line('default_sms_invoice_template');
