<?php
trait Nexo_items
{
    /**
     * Get item
     *
    **/
    
    public function item_get($id = null, $filter = 'ID')
    {
        if ($id != null && $filter != 'sku-barcode') {
            $result        =    $this->db->where($filter, $id)->get('nexo_articles')->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } elseif ($filter == 'sku-barcode') {
            $result        =    $this->db
                                ->where('CODEBAR', $id)
                                ->or_where('SKU', $id)
                                ->get('nexo_articles')
                                ->result();
            $result        ?    $this->response($result, 200)  : $this->response(array(), 404);
        } else {
            $this->db->select('*,
			nexo_articles.ID as ID,
			nexo_categories.ID as CAT_ID
			')
            ->from('nexo_articles')
            ->join('nexo_categories', 'nexo_articles.REF_CATEGORIE = nexo_categories.ID');
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
            $this->__failed();
        } else if( $id == 'all' ) {
			$this->db->where('ID >', 0)->delete('nexo_articles')->result();
            
            $this->__success();
		} else {
            $this->db->where('ID', $id)->delete('nexo_articles')->result();
            
            $this->__failed();
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
        ->set('QUANTITE_VENDU', $this->put('quantite_vendue'))
        ->set('DEFECTUEUX', $this->put('defectueux'))
        ->set('PRIX_DACHAT', $this->put('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->put('frais_accessoire'))
        ->set('COUT_DACHAT', $this->put('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->put('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->put('prix_de_vente'))
		->set('PRIX_PROMOTIONEL', $this->put('prix_promotionel'))
		->set( 'DESCRIPTION', $this->put( 'description' ))
		->set( 'DATE_MOD', $this->put( 'date_mod' ))
		->set( 'POIDS', $this->put( 'poids' ))
        ->update('nexo_articles');
        
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
        ->set('DESIGN', $this->post('design'))
        ->set('REF_RAYON', $this->post('ref_rayon'))
        ->set('REF_SHIPPING', $this->post('ref_shipping'))
        ->set('REF_CATEGORIE', $this->post('ref_categorie'))
        ->set('QUANTITY', $this->post('quantity'))
        ->set('SKU', $this->post('sku'))
        ->set('QUANTITE_RESTANTE', $this->post('quantite_restante'))
        ->set('QUANTITE_VENDU', $this->post('quantite_vendue'))
        ->set('DEFECTUEUX', $this->post('defectueux'))
        ->set('PRIX_DACHAT', $this->post('prix_dachat'))
        ->set('FRAIS_ACCESSOIRE', $this->post('frais_accessoire'))
        ->set('COUT_DACHAT', $this->post('cout_dachat'))
        ->set('TAUX_DE_MARGE', $this->post('taux_de_marge'))
        ->set('PRIX_DE_VENTE', $this->post('prix_de_vente'))
		->set('PRIX_PROMOTIONEL', $this->post('prix_promotionel'))
		->set( 'DESCRIPTION', $this->post( 'description' ))
		->set( 'DATE_CREATION', $this->post( 'date_creation' ))
		->set( 'POIDS', $this->post( 'poids' ))
        ->insert('nexo_articles');
        
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
}
