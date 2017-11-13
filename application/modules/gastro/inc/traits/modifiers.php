<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait nexo_restaurant_modifiers
{
    public function modifiers_by_group_get( $id = null )
    {
        $this->db->select( 
            store_prefix() . 'nexo_restaurant_modifiers.NAME as name,' .
            store_prefix() . 'nexo_restaurant_modifiers.DESCRIPTION as description,' . 
            store_prefix() . 'nexo_restaurant_modifiers.AUTHOR as author,' .      
            store_prefix() . 'nexo_restaurant_modifiers.REF_CATEGORY as cateogry,' .    
            store_prefix() . 'nexo_restaurant_modifiers.DEFAULT as default,' .
            store_prefix() . 'nexo_restaurant_modifiers.PRICE as price,' .
            store_prefix() . 'nexo_restaurant_modifiers.IMAGE as image,' .
            store_prefix() . 'nexo_restaurant_modifiers_categories.FORCED as group_forced,' .
            store_prefix() . 'nexo_restaurant_modifiers_categories.NAME as group_name,' .
            store_prefix() . 'nexo_restaurant_modifiers_categories.MULTISELECT as group_multiselect'  
        )
        ->from( store_prefix() . 'nexo_restaurant_modifiers' )
        ->join( 
            store_prefix() . 'nexo_restaurant_modifiers_categories', 
            store_prefix() . 'nexo_restaurant_modifiers_categories.ID = ' . store_prefix() . 'nexo_restaurant_modifiers.REF_CATEGORY' 
        );

        if( $id != null ) {
            $this->db->where( store_prefix() . 'nexo_restaurant_modifiers.REF_CATEGORY', $id );
        }

        $query  =   $this->db->get()->result();

        return $this->response( $query, 200 );
    }
}
