<?php
trait Nexo_customers
{
	/**
     * Customers
    **/

    public function customer_get($id = null, $filter = 'ID')
    {
        if ($id != null) {
            $result        =    $this->db->where($filter, $id)->get( store_prefix() . 'nexo_clients')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 500);
        } else {
            $this->response($this->db->get( store_prefix() . 'nexo_clients')->result());
        }
    }

    /**
     * Customer Insert
     *
     * @params POST string name
     * @params POST string email
     * @params POST string tel
     * @params POST string prenom
    **/

    public function customer_post()
    {
        $request    =    $this->db
        ->set('NOM',    $this->post('nom'))
        ->set('EMAIL',    $this->post('email'))
        ->set('TEL',    $this->post('tel'))
        ->set('PRENOM',    $this->post('prenom'))
        ->set('REF_GROUP', $this->post('ref_group'))
        ->set('AUTHOR', $this->post('author'))
        ->set('DATE_CREATION', $this->post('date_creation'))
        ->insert( store_prefix() . 'nexo_clients');

        if ($request) {
            $this->response(array(
                'status'        =>        'success'
            ), 200);
        } else {
            $this->response(array(
                'status'        =>        'error'
            ), 404);
        }
    }
	
	/**
     * Customer Groups
     * @params int/string group par
     * @return json
    **/

    public function customers_groups_get($id = null, $filter = 'id')
    {		
        if ($id != null) {
            $this->db->where('ID', $id);
        }

        $query    =    $this->db->get( store_prefix() . 'nexo_clients_groups');
        $this->response($query->result(), 200);
    }

    /**
     * Customer Groups Post
     * @param String name
     * @param String Description
     * @param Int author
     * @return void
    **/

    public function customers_groups_post()
    {
		$data		=	array(
            'NAME'            =>    $this->post('name'),
            'DESCRIPTION'    =>    $this->post('descirption'),
            'DATE_CREATION'    =>    date_now(),
            'AUTHOR'        =>    $this->post('user_id')
        );
		
        $this->db->insert( store_prefix() . 'nexo_clients_groups', $data );

        $this->__success();
    }

    /**
     * Customer Groupe delete
     * @param Int group id
     * @return json
     *
    **/

    public function customers_groups_delete($id)
    {
        if ($this->db->where('ID', $id)->delete( store_prefix() . 'nexo_clients_groups')) {
            $this->__failed();
        } else {
            $this->__success();
        }
    }

    /**
     * Customer edit
     * @param Int group id
     * @return json
    **/

    public function customers_groups_update($group_id)
    {
		$data 		=	array(
            'NAME'                =>    $this->put('name'),
            'DESCRIPTION'        =>    $this->put('description'),
            'AUTHOR'            =>    $this->put('user_id'),
            'DATE_MODIFICATION'    =>    date_now()
        );
		
        if ($this->where('ID', $group_id)->update('nexo_clients_groups', $data )) {
            $this->__success();
        } else {
            $this->__failed();
        }
    }
}