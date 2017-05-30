<?php
// @since 3.0.1

$this->load->model( 'Nexo_Stores' );

$stores         =   $this->Nexo_Stores->get();

array_unshift( $stores, [
    'ID'        =>  0
]);

foreach( $stores as $store ) {

    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';

    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` ADD `UNIT_PRICE` float NOT NULL AFTER `TYPE`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` ADD `TOTAL_PRICE` float NOT NULL AFTER `UNIT_PRICE`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` ADD `DESCRIPTION` text NOT NULL AFTER `TOTAL_PRICE`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` ADD `REF_PROVIDER` int NOT NULL AFTER `DESCRIPTION`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles_stock_flow` ADD `REF_SHIPPING` int NOT NULL AFTER `REF_PROVIDER`;');
    $this->db->query('ALTER TABLE `'. $this->db->dbprefix . $store_prefix .'nexo_articles` ADD `REF_PROVIDER` int NOT NULL AFTER `REF_CATEGORIE`;');

}

// Item Stock
$this->auth->create_perm('create_item_stock',    $this->lang->line( 'create_item_stock' ),        $this->lang->line( 'create_item_stock_details' ));
$this->auth->create_perm('edit_item_stock',    $this->lang->line( 'edit_item_stock' ),        $this->lang->line( 'edit_item_stock_details' ));
$this->auth->create_perm('delete_item_stock',    $this->lang->line( 'delete_item_stock' ),        $this->lang->line( 'delete_item_stock_details' ));

foreach( [ 'shop_manager', 'master', 'shop_tester' ] as $role ) {
    $this->auth->allow_group( $role, 'create_item_stock');
    $this->auth->allow_group( $role, 'edit_item_stock');
    
    if( $role != 'shop_tester' ) {
        $this->auth->allow_group( $role, 'delete_item_stock');
    }
}
