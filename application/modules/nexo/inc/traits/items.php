<?php
trait Nexo_items
{
    /**
     * Get item
     *
    **/
    
    public function item_get($id = null, $filter = 'ID' )
    {
        if ($id != null && $filter != 'sku-barcode') {
            $result        =    $this->db->where($filter, $id)->get( store_prefix() . 'nexo_articles')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } elseif ($filter == 'sku-barcode') {
            $result        =    $this->db
                                ->where('CODEBAR', $id)
                                ->or_where('SKU', $id)
                                ->get( store_prefix() . 'nexo_articles')
                                ->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } else {
            $this->db->select('*,
			' . store_prefix() . 'nexo_articles.ID as ID,
			' . store_prefix() . 'nexo_categories.ID as CAT_ID
			')
            ->from( store_prefix() . 'nexo_articles')
            ->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_articles.REF_CATEGORIE = ' . store_prefix() . 'nexo_categories.ID');
            $this->response($this->db->get()->result());
        }
    }
    
    /**
     * Delete Item from Shop
     *
    **/
    
    public function item_delete($id = null)
    {
        if ($id == null) {
            $this->response(array(
                'status' => 'failed'
            ));
        } else {
            $this->db->where('ID', $id)->delete( store_prefix() . 'nexo_articles')->result();
            
            $this->response(array(
                'status' => 'failed'
            ));
        }
    }
    
    /**
     * PUt item
     *
    **/
    
    public function item_put()
    {
        $request    =    $this->db->where($this->put('id'))
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->update( store_prefix() . 'nexo_articles');
        
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
     * Item insert
    **/
    
    public function item_post()
    {
        $request    =    $this->db
        ->set('DESIGN', $this->put('design'))
        ->set('REF_RAYON', $this->put('ref_rayon'))
        ->set('REF_SHIPPING', $this->put('ref_shipping'))
        ->set('REF_CATEGORIE', $this->put('ref_categorie'))
        ->set('QUANTITY', $this->put('quantity'))
        ->set('SKU', $this->put('sku'))
        ->set('QUANTITE_RESTANTE', $this->put('quantite_restante'))
        ->set('QUANTITE_VENDUE', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
        ->insert( store_prefix() . 'nexo_articles');
        
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
	 * Item by collection
	 *
	 * @param int collection id
	 * @return json
	**/
	
	public function item_by_collection_get( $collection_id )
	{
		$this->response( 
		$this->db->select( '*,
		' . store_prefix() . 'nexo_categories.NOM as CAT_NAME' )
		->from( store_prefix() . 'nexo_articles' )
		->join( store_prefix() . 'nexo_categories', store_prefix() . 'nexo_articles.REF_CATEGORIE = ' . store_prefix() . 'nexo_categories.ID', 'inner' )
		->where( 'REF_SHIPPING', $collection_id )
		->get()->result() );
	}
}
