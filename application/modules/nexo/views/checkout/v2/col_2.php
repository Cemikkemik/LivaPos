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


/*  bootstrap tab */

div.bootstrap-tab {
	border-left: 1px #EEE solid;
	border-right: 1px #EEE solid;
  background: #FFF;
}
div.bootstrap-tab-container{
  z-index: 10;
  background-color: #ffffff;
  padding: 0 !important;
  background-clip: padding-box;
  opacity: 0.97;
  filter: alpha(opacity=97);
}
div.bootstrap-tab-menu{
  padding-right: 0;
  padding-left: 0;
  padding-bottom: 0;
  height: 242px;
}
div.bootstrap-tab-menu div.list-group{
  margin-bottom: 0;
}
div.bootstrap-tab-menu div.list-group>a{
  margin-bottom: -1px;
}
div.bootstrap-tab-menu div.list-group>a:nth-child(1){
  margin-top: -1px;
}
div.bootstrap-tab-menu div.list-group>a .glyphicon,

div.bootstrap-tab-menu div.list-group>a.active,
div.bootstrap-tab-menu div.list-group>a.active .glyphicon,
div.bootstrap-tab-menu div.list-group>a.active .fa{
  background-color:  #EEE; /** #9792e4;**/
  background-image: #EEE; /** #9792e4; **/
  color: #333;
  border: solid 1px #DDD;
}
div.bootstrap-tab-menu div.list-group>a.active:after{
  content: '';
  position: absolute;
  left: 100%;
  top: 50%;
  margin-top: -13px;
  border-left: 0;
  border-bottom: 13px solid transparent;
  border-top: 13px solid transparent;
  border-left: 10px solid #EEE; /** #9792e4; **/
}

div.bootstrap-tab-content{
  /** background-color: #ffffff; **/
  /* border: 1px solid #eeeeee; */
  padding-left: 0px;
  padding-top: 10px;
}

div.bootstrap-tab div.bootstrap-tab-content:not(.active){
  display: none;
}
.pay-box-container .list-group-item:last-child, .pay-box-container .list-group-item:first-child {
    border-radius: 0px !important;
    border-radius: 0px !important;
}
</style>
