<?php
class Nexo_Vendor
{
    public function __construct()
    {
    }
    /**
     * 	Create a new vendor
     *	
     *	@param string Vendor name
     *	@param string Vendor descripion
     *	@param string Vendor pobox
     *	@param string Vendor tel
     *	@param string Vendor email
     *	@access public
     * 	@return bool
    **/
    public function create($nom, $description, $pobox, $tel, $email)
    {
    }
    
    /**
     * Get Vendor
     * 
     *	@access public
     *	@param string Vendor name
     * 	@param string filter
     *	@return array
    **/
    
    public function get($misc, $filter = 'as_name')
    {
    }
}
