<?php

use Carbon\Carbon;

Trait Np_Orders
{
	/**
	 * Search Order
	**/

	public function orders_get( $code = null, $action = 'search' )
	{
		$this->db
		->select( '*,
		' . store_prefix() . 'nexo_commandes.ID as ID,
		' . store_prefix() . 'nexo_commandes.DATE_CREATION as DATE_CREATION,
		' . store_prefix() . 'nexo_clients.NOM,
		' . store_prefix() . 'nexo_clients.PRENOM' )
		->from( store_prefix() . 'nexo_commandes_produits' )
		->join( store_prefix() . 'nexo_commandes',
			store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner'
		)
		->join( store_prefix() . 'nexo_articles',
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner'
		)
		->join( store_prefix() . 'nexo_articles_meta',
			store_prefix() . 'nexo_articles_meta.REF_ARTICLE = ' . store_prefix() . 'nexo_articles.ID', 'inner'
		)
		->join( store_prefix() . 'nexo_clients',
			store_prefix() . 'nexo_clients.ID = ' . store_prefix() . 'nexo_commandes.REF_CLIENT', 'inner'
		)
		->group_by( store_prefix() . 'nexo_commandes.ID' );

		if( $code != null ) {
			if( $action == 'search' ) {
				$this->db->like( store_prefix() . 'nexo_commandes.ID', $code );
			} else {
				$this->db->where( store_prefix() . 'nexo_commandes.CODE', $code );
			}
		}

		$this->db->limit( 10 );

		$query	=	$this->db->get();

		$data	=	$query->result_array();

		foreach( $data as $key => $_data ) {
			$_subquery	=	$this->db->select( '*' )
			->from( store_prefix() . 'nexo_commandes_meta' )
			->where( 'REF_ORDER_ID', $_data[ 'ID' ] )
			->get();

			$sub_data		=	$_subquery->result_array();

			if( $sub_data ) {
				foreach( $sub_data as $_sub_data ) {
					$data[ $key ][ $_sub_data[ 'KEY' ] ]		=	$_sub_data[ 'VALUE' ];
				}
			} else {
				$data[ $key ][ 'USED_SECONDS' ]	=	0;
				$data[ $key ][ 'START_TIME' ]	=	0;
				$data[ $key ][ 'END_TIME' ]		=	0;
				$data[ $key ][ 'TIMER_ON' ]		=	0;
			}
		}

		$this->response( $data, 200 );
	}

	/**
	 * Order Details
	**/

	public function orders_items_get( $code )
	{
		$this->db
		->select(
			store_prefix() . 'nexo_articles.DESIGN,
		' . store_prefix() . 'nexo_articles.ID as ITEM_ID,
		' . store_prefix() . 'nexo_commandes_produits.QUANTITE,
		' . store_prefix() . 'nexo_commandes_produits.PRIX,
		' . store_prefix() . 'nexo_commandes_produits.PRIX_TOTAL

		' )
		->from( store_prefix() . 'nexo_commandes_produits' )
		->join( store_prefix() . 'nexo_commandes',
			store_prefix() . 'nexo_commandes.CODE = ' . store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', 'inner'
		)
		->join( store_prefix() . 'nexo_articles',
			store_prefix() . 'nexo_articles.CODEBAR = ' . store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR', 'inner'
		)

		/*->join( store_prefix() . 'nexo_articles_meta',
			store_prefix() . 'nexo_articles_meta.REF_ARTICLE = ' . store_prefix() . 'nexo_articles.ID', 'inner'
		)*/
		// ->group_by( store_prefix() . 'nexo_commandes_produits.REF_PRODUCT_CODEBAR' )

		->where( store_prefix() . 'nexo_commandes_produits.REF_COMMAND_CODE', $code );

		$query			=	$this->db->get();
		$result_data	=	$query->result_array();

		foreach( $result_data as $key => $_result ) {
			$_subquery	=	$this->db->select( '*' )
			->from( store_prefix() . 'nexo_articles_meta' )
			->where( 'REF_ARTICLE', $_result[ 'ITEM_ID' ] )
			->get();

			$data		=	$_subquery->result_array();

			foreach( $data as $_data ) {
				$result_data[ $key ][ $_data[ 'KEY' ] ]		=	$_data[ 'VALUE' ];
			}
		}

		$this->response( $result_data, 200 );
	}

	/**
	 * Start Timer
	 * @param string order CODE
	 * @return json
	**/

	public function start_timer_post()
	{
		/**
		 * If Timer is enabled. The procees can't goes
		**/

		$query						=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
		->where( 'KEY', 'TIMER_ON' )
		->get( store_prefix() . 'nexo_commandes_meta' );
		$result						=	$query->result_array();

		if( $result ) {

			if( $result[0][ 'VALUE' ] != '0' ) {
				$this->__forbidden();
			}

			$query	=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
			->where( 'KEY', 'USED_SECONDS' )
			->get( store_prefix() . 'nexo_commandes_meta' );

			$result						=	$query->result_array();

			if( $result ) {
				if( intval( $result[0][ 'VALUE' ] ) >= intval( $this->post( 'overall_time' ) ) ) {
					$this->__failed();
				}
			}

		}

		$query						=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
		->where( 'KEY', 'USED_SECONDS' )
		->get( store_prefix() . 'nexo_commandes_meta' );
		$result						=	$query->result_array();

		$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )->delete( store_prefix() . 'nexo_commandes_meta' );

		$query	=	$this->db
		->insert_batch( store_prefix() . 'nexo_commandes_meta', array(
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'TIMER_ON',
				'VALUE'				=>	1
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'START_TIME',
				'VALUE'				=>	$this->post( 'date' )
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'END_TIME',
				'VALUE'				=>	0
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'USED_SECONDS',
				'VALUE'				=>	@$result[0][ 'VALUE' ] == null ? 0 : $result[0][ 'VALUE' ]
			)
		) );

		$this->__success();
	}

	/**
	 * End Timer
	 * @param string order CODE
	 * @return json
	**/

	public function end_timer_post()
	{
		/**
		 * If Timer is enabled. The procees can't goes
		**/

		$query						=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
		->where( 'KEY', 'TIMER_ON' )
		->get( store_prefix() . 'nexo_commandes_meta' );
		$result						=	$query->result_array();

		$difference					=	0;

		if( $result ) {

			if( $result[0][ 'VALUE' ] != '1' ) {
				$this->__forbidden();
			}

			$query	=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
			->where( 'KEY', 'START_TIME' )
			->get( store_prefix() . 'nexo_commandes_meta' );

			$result			=	$query->result_array();
			$old_time		=	Carbon::parse( $result[0][ 'VALUE' ] );
			$new_time		=	Carbon::parse( $this->post( 'date' ) );

			// var_dump( $new_time->toDateTimeString() );
			// var_dump( $old_time->toDateTimeString() );

			$difference		=	$old_time->diffInSeconds( $new_time );
		}

		$query						=	$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )
		->where( 'KEY', 'USED_SECONDS' )
		->get( store_prefix() . 'nexo_commandes_meta' );
		$result						=	$query->result_array();

		$this->db->where( 'REF_ORDER_ID', $this->post( 'order_id' ) )->delete( store_prefix() . 'nexo_commandes_meta' );

		if( intval( $difference ) + intval( @$result[0][ 'VALUE' ] == null ? 0 : $result[0][ 'VALUE' ] ) > $this->post( 'overall_time' ) ) {
			$used_seconds			=	$this->post( 'overall_time' );
		} else {
			$used_seconds			=	intval( $difference ) + intval( @$result[0][ 'VALUE' ] == null ? 0 : $result[0][ 'VALUE' ] );
		}

		$query	=	$this->db
		->insert_batch( store_prefix() . 'nexo_commandes_meta', array(
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'TIMER_ON',
				'VALUE'				=>	0
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'START_TIME',
				'VALUE'				=>	0
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'END_TIME',
				'VALUE'				=>	$this->post( 'date' )
			),
			array(
				'REF_ORDER_ID'		=>	$this->post( 'order_id' ),
				'KEY'				=>	'USED_SECONDS',
				'VALUE'				=>	$used_seconds
			)
		) );

		$this->__success();
	}
}
