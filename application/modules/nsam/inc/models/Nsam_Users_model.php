<?php
class Nsam_Users_model extends grocery_CRUD_model 
{
     private  $query_str = ''; 
	function __construct() {
		parent::__construct();
	}
 
	function get_list() {

          $this->query_str    =    'SELECT 
          ' . $this->db->dbprefix( 'aauth_groups' ) . '.`name` as `groups`, 
          ' . $this->db->dbprefix( 'aauth_users' ) . '.`name` as `name`, 
          ' . $this->db->dbprefix( 'aauth_users' ) . '.`id` as `id`, 
          ' . $this->db->dbprefix( 'aauth_groups' ) . '.`id` as `group_id`,
          ' . $this->db->dbprefix( 'aauth_users' ). '.*
          FROM ' . $this->db->dbprefix( 'aauth_users' ) . ' 
               JOIN 
               ' . $this->db->dbprefix( 'aauth_user_to_group' ) . 
               ' ON 
               ' . $this->db->dbprefix( 'aauth_users' ) . '.`id` = ' . $this->db->dbprefix( 'aauth_user_to_group' ) . '.`user_id` 
               JOIN ' . $this->db->dbprefix( 'aauth_groups' ) . 
               ' ON ' . $this->db->dbprefix( 'aauth_groups' ) . '.`id` = ' . $this->db->dbprefix( 'aauth_user_to_group' ) . '.`group_id`
          WHERE `' . $this->db->dbprefix( 'aauth_groups' ) . '`.`name` = \'shop_cashier\' 
          AND ( 
               SELECT `value` FROM ' . $this->db->dbprefix( 'options' ) . ' 
               WHERE ' . $this->db->dbprefix( 'options' ) . '.key = CONCAT( \'store_access_\', ' . $this->db->dbprefix( 'aauth_users' ) . '.`id`, "_'. get_store_id() .'" )
          ) = "yes"
          LIMIT 10';
          $query=$this->db->query( $this->query_str);

          $results_array=$query->result();

		return $results_array;		
	}
 
	// public function set_query_str($query_str) {
     //      var_dump( $query_str );die;
	// 	$this->query_str = $query_str;
	// }
}