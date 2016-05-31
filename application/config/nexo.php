<?php

/**
 * Host Nexo Configs
**/

$config[ 'discounted_item_background' ]		=	'#DFF0D8';

/**
 * Order Types
**/

$config[ 'nexo_order_types' ]	=	array(
	'nexo_order_comptant'			=>	__( 'Complète', 'nexo' ),
	'nexo_order_advance'			=>	__( 'Incomplète', 'nexo' ),
	'nexo_order_devis'				=>	__( 'Devis', 'nexo' )	
);

/**
 * Discount Type
**/

$config[ 'nexo_discount_type' ]	=	array(
	'disable'		=>	__( 'Désactivée', 'nexo' ),
	'amount'		=>	__( 'Remise fixe', 'nexo' ),
	'percent'		=>	__( 'Remise au pourcentage', 'nexo' )
);

/**
 * Nexo True or False dropdown menu
**/

$config[ 'nexo_true_false' ]	=	array(
	'false'		=>	__( 'Non', 'nexo' ),
	'true'			=>	__( 'Oui', 'nexo' )	
);