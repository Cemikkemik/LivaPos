<?php

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
	'ID'        =>  0
	]);
	
	$this->load->dbforge();
	$this->load->library( 'schema' );
		
	foreach( $stores as $store ) {
		
		$store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
		
		// edit item price and taxes
		$this->db->query( 'ALTER TABLE `' . $this->db->dbprefix . $store_prefix . 'nexo_articles` 
		ADD `REF_UNIT_1` INT NOT NULL AFTER `REF_CATEGORIE`,
		ADD `REF_UNIT_2` INT NOT NULL AFTER `REF_UNIT_1`,
		ADD `REF_UNIT_3` INT NOT NULL AFTER `REF_UNIT_2`,
		ADD `SUPPLY_TYPE` varchar(200) NOT NULL AFTER `REF_UNIT_3`,
		ADD `SALE_TYPE` varchar(200) NOT NULL AFTER `SUPPLY_UNIT`' );
		
		$this->schema->create_table( $store_prefix . 'nexo_units', function( $schema ) {
			$schema->auto_increment_integer( 'ID' );
			$schema->primary_key( 'ID' );
			$schema->string( 'NAME' );
			$schema->text( 'DESCRIPTION' );
			$schema->integer( 'AUTHOR' );
			$schema->datetime( 'DATE_CREATION' );
			$schema->datetime( 'DATE_MOD' );
			$schema->integer( 'QUANTITY' );
		});

		$this->schema->add_column( $store_prefix . 'nexo_commandes_produits', 'REF_UNIT', 'integer', [], 'QUANTITE' );
		$this->schema->add_column( $store_prefix . 'nexo_commandes_produits', 'UNIT_QUANTITY', 'integer', [], 'REF_UNIT' );
	}