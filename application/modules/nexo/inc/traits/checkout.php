<?php
include_once(APPPATH . '/modules/nexo/vendor/autoload.php');

use Carbon\Carbon;
use \Curl\Curl;

trait Nexo_checkout_money
{
    public function pos_balance_post()
    {
       $this->db->insert( store_prefix() . 'nexo_checkout_money', array(
	   		'AMOUNT'			=>	$this->post( 'amount' ),
			'TYPE'				=>	$this->post( 'type' ),
			'DATE_CREATION'		=>	$this->post( 'date' ),
			'AUTHOR'			=>	$this->post( 'author' )
	   ) );
	   
	   $this->__success();
    }
	
	/**
	 * Delete Balance entry
	**/
	
	public function pos_balance_delete( $id )
	{
		$this->db->where( 'ID', $id )->delete( store_prefix() . 'nexo_checkout_money' );
		$this->__success();
	}
	
	/**
	 * Post Balance edit
	**/
	
	public function pos_balance_put( $id ) 
	{
		$this->db->where( 'ID', $id )->update( store_prefix() . 'nexo_checkout_money', array(
	   		'AMOUNT'			=>	$this->put( 'amount' ),
			'TYPE'				=>	$this->put( 'type' ),
			'DATE_MOD'			=>	$this->put( 'date' ),
			'AUTHOR'			=>	$this->put( 'author' )
	   ) );
	}
	
	/**
	 * Get balance entry
	 * @param string/int 
	 * @param string/int
	 * @return json
	**/
	
	public function pos_balance_get( $start = null, $limit = null ) 
	{
		$this->db->select( '*' );
		$this->db->from( store_prefix() . 'nexo_checkout_money' );
		if( $start != null && $limit != null ) {
			$this->db->limit( $limit, $start );
		} else if( $start != null ) {
			$this->where( 'ID', $start );
		}
		
		$this->response( $this->db->get()->result(), 200 );
	}
	
	/**
	 * Get date for time range
	**/
	
	public function pos_balance_timerange_post()
	{
		$this->response( $this->db
		->where( 'DATE_CREATION >=', $this->post( 'min' ) )
		->where( 'DATE_CREATION <=', $this->post( 'max' ) )
		->get( store_prefix() . 'nexo_checkout_money' )
		->result(), 200 );
	}
	
	/** 
	 * Check POS balance
	**/
	
	public function pos_balance_check_get()
	{
		$startOfDay	=	Carbon::parse( date_now() )->startOfDay();
		$endOfDay	=	Carbon::parse( date_now() )->endOfDay();
		
		$Curl		=	new Curl;
		
		global $Options;
		$this->load->config('rest');
		$header_key        =    $this->config->item('rest_key_name');
		
		$Curl->setHeader( $header_key, $Options[ 'rest_key' ] );
		
		$TodayBalance	=	$Curl->post( site_url( array( 'rest', 'nexo', 'pos_balance_timerange' ) ), array(
			'min'		=>	$startOfDay->toDateTimeString(),
			'max'		=>	$endOfDay->toDateTimeString(),
			$this->security->get_csrf_token_name() 	=>	$this->security->get_csrf_hash()
		) );
		
		var_dump( $TodayBalance );
	}

}
