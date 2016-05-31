<div class="box box-primary direct-chat direct-chat-primary" id="product-list-wrapper" style="visibility:hidden">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php _e( 'Liste des produits', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <!--<div class="form-group">
                <select class="form-control filter-by-categories" multiple="multiple" data-placeholder="<?php _e( 'Filter by categories', 'nexo' );?>" style="width: 100%;">
                </select>
            </div>-->
            <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-cogs"></i> <?php _e( 'Réglages', 'nexo' );?></button>
        </div>
    </div>
    <div class="box-footer" id="search-product-code-bar">
        <form action="#" method="post">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-large btn-primary"><?php _e( 'Rechercher' );?></button>
                </span> 
                <input type="text" name="message" placeholder="<?php _e( 'Codebarre ou UGS...' );?>" class="form-control">
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