<div class="box box-primary direct-chat direct-chat-primary" id="product-list-wrapper" style="margin-bottom:0;visibility:hidden">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php _e( 'Liste des produits', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right" style="width:80%">
            <div class="form-group">
                <select class="form-control filter-by-categories" multiple="multiple" data-placeholder="<?php _e( 'Filter by categories', 'nexo' );?>" style="width: 100%;">
                </select>
            </div>
        </div>
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
