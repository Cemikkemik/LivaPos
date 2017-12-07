<?php
global $Routes;

$Routes->get( '/nexo/settings/stripe', 'Gateway_Controller@stripe_settings' );
$Routes->get( '/nexo/settings/payments', 'Gateway_Controller@settings' );