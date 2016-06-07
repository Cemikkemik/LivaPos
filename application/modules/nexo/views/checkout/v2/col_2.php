<div class="box box-primary direct-chat direct-chat-primary" id="product-list-wrapper" style="visibility:hidden">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php _e('Liste des produits', 'nexo');?>
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-primary item-list-settings btn-sm" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            	<i class="fa fa-cogs"></i>
                <?php _e('Filtrer les catégories', 'nexo');?>
			</button>
        </div>
    </div>
    <div class="box-footer" id="search-product-code-bar" style="border-bottom:1px solid #EEE;">
        <form action="#" method="post" id="search-item-form">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-large btn-primary"><?php _e('Rechercher', 'nexo');?></button>
                </span> 
                <input type="text" name="item_sku_barcode" placeholder="<?php _e('Codebarre ou UGS...', 'nexo');?>" class="form-control">
			</div>
        </form>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="direct-chat-messages" style="padding:0px;">
            <div class="row" id="filter-list" style="padding-left:0px;padding-right:0px;margin-left:0px;margin-right:0px;">
            </div>
        </div>
    </div>
    <div class="overlay" id="product-list-splash">
      <i class="fa fa-refresh fa-spin"></i>
    </div>
</div>
<style type="text/css">
.content-wrapper > .content {
	padding-bottom:0px;
}
</style>