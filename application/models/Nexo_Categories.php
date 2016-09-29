<?php
class Nexo_Categories extends CI_Model
{
    /**
     * Get categories
     *
     * @param int
     * @return array/bool
    **/
    
    public function get($id = null, $filter = 'as_id')
    {
        if ($id != null) {
            if ($filter == 'as_id') {
                $this->db->where('ID', $id);
            } elseif ($filter == 'as_nom') {
                $this->db->where('NOM', $id);
            }
        }
        
        $query    =    $this->db->get( store_prefix() . 'nexo_categories');
        return $query->result_array();
    }
}
