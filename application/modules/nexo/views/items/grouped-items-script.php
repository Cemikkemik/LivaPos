<script>
var grouped_items   =   {
    api_url                 :   '<?php echo site_url([ 'api', 'nexopos', 'physicals-and-digitals', store_get_param( '?' )]);?>',
    post_item_url           :   '<?php echo site_url([ 'api', 'nexopos', 'post-grouped', store_get_param( '?' )]);?>',
    text                    :   {
        warning             :   '<?php echo _s( 'Attention', 'nexo' );?>',
        missing_item_name   :   '<?php echo _s( 'Veuillez fourni un nom à ce produit', 'nexo' );?>',
        missing_items       :   '<?php echo _s( 'Veuillez ajouter des produits', 'nexo' );?>',
        formHasError        :   '<?php echo _s( 'Veuillez remplir la catégorie, le prix de vente, l\'unité de gestion de stock et le statut', 'nexo' );?>'
    },
    categories              :   <?php echo json_encode(
        $this->db->get( store_prefix() . 'nexo_categories' )->result()
    );?>,
    barcodes                 :   <?php echo json_encode( 
        $this->config->item( 'nexo_barcode_supported' )
    );?>,
    taxes                   :   <?php echo json_encode(
        $this->db->get( store_prefix() . 'nexo_taxes' )->result()
    );?>
}
$( '.category-dropdown' ).selectpicker({
  style: 'btn-default',
  size: 4
});
</script>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script src="<?php echo module_url( 'nexo' ) . '/js/group-items.js';?>"></script>