<div class="box box-primary direct-chat direct-chat-primary" id="product-list-wrapper" style="visibility:hidden">
    <div class="box-header with-border search-field-wrapper">
        <form action="#" method="post" id="search-item-form">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-large btn-default"><?php _e('Rechercher', 'nexo');?></button>
                </span>
                <input type="text" name="item_sku_barcode" placeholder="<?php _e('Codebarre, UGS, nom du produit ou de la catégorie...', 'nexo');?>" class="form-control">
                <span class="input-group-btn">
					<?php echo $this->events->apply_filters( 'pos_search_input_after', '' );?>
                </span>
			</div>
        </form>
    </div>
    <div class="box-header with-border cattegory-slider" style="padding:0px;">
    	<div class="container-fluid">
            <div class="row">
                <div class="col-lg-1 col-md-1 hidden-sm text-center slick-button cat-prev" style="padding:0;border-right:solid 1px #EEE;"><i style="font-size:20px;line-height:40px;" class="fa fa-arrow-left"></i></div>
                <div class="col-lg-10 col-md-10 col-sm-12 add_slick_inside" style="padding:0;">
                
                <div class="slick slick-wrapper">
                <!-- Waiting Categories -->
                </div>
                
                </div>
                <div class="col-lg-1 col-md-1 hidden-sm text-center slick-button cat-next" style="padding:0;border-left:solid 1px #EEE;"><i style="font-size:20px;line-height:40px;" class="fa fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
    <style type="text/css">
	.slick-button:hover {
		background : #F2F2F2;
		cursor: pointer;
	}
	.slick-item:hover {
		box-shadow:inset 0px -3px 10px 5px #F2F2F2;
		cursor: pointer
	}
	.slick-item-active {
		background: #EEE
	}
	</style>
    <!--<div class="box-footer" id="search-product-code-bar" style="border-bottom:1px solid #EEE;">
        <form action="#" method="post" id="search-item-form">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-large btn-default"><?php _e('Rechercher', 'nexo');?></button>
                </span>
                <input type="text" name="item_sku_barcode" placeholder="<?php _e('Codebarre ou UGS...', 'nexo');?>" class="form-control">
                <span class="input-group-btn">
                    <button class="btn btn-default item-list-settings" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-cogs"></i>
                    <?php _e('Filtrer les catégories', 'nexo');?>
                </button>
                </span>
			</div>
        </form>
    </div>-->
    <!-- /.box-header -->
    <div class="box-body">
        <div class="direct-chat-messages item-list-container" style="padding:0px;">
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
