<?php
global $Routes;

$Routes->match([ 'get', 'post' ], '/nexo/licence', 'NexoUpdaterLicence@index' )->name( 'nexo.licence' );
$Routes->match([ 'get', 'post' ], '/nexo/licence/activated', 'NexoUpdaterLicence@activated' )->name( 'nexo.licence' );