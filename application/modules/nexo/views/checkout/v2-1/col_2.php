<ul class="nav nav-tabs tab-grid hidden-lg hidden-md"> <!--  -->
    <li ng-click="showPart( 'cart' );" class="{{ cartIsActive }}"><a href="#"><?php echo __( 'Panier', 'nexo' );?></a></li>
    <li ng-click="showPart( 'grid' );" class="{{ gridIsActive }}"><a href="#"><?php echo __( 'Produits', 'nexo' );?></a></a></li>
</ul>
<div class="box box-primary direct-chat direct-chat-primary" id="product-list-wrapper" style="visibility:hidden">
    <div class="box-header with-border search-field-wrapper">
        <form action="#" method="post" id="search-item-form">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-large btn-default"><i class="fa fa-search"></i></button>
                    <button type="button" class="enable_barcode_search btn btn-large btn-default"><i class="fa fa-barcode"></i></button>
                </div>

                <input autocomplete="off" type="text" name="item_sku_barcode" placeholder="<?php _e('Codebarre, UGS, nom du produit ou de la catégorie...', 'nexo');?>" class="form-control">			</div>
        </form>
    </div>
    <div class="box-header with-border cattegory-slider" style="padding:0px;">
    	<div class="container-fluid">
            <div class="row">
                <div class="col-lg-1 col-md-1 hidden-sm hidden-xs text-center slick-button cat-prev" style="padding:0;border-right:solid 1px #EEE;"><i style="font-size:20px;line-height:40px;" class="fa fa-arrow-left"></i></div>
                <div class="col-lg-10 col-md-10 col-sm-12 add_slick_inside" style="padding:0;">

                <div class="slick slick-wrapper">
                <!-- Waiting Categories -->
                </div>

                </div>
                <div class="col-lg-1 col-md-1 hidden-sm hidden-xs text-center slick-button cat-next" style="padding:0;border-left:solid 1px #EEE;"><i style="font-size:20px;line-height:40px;" class="fa fa-arrow-right"></i></div>
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
    <div class="box-body">
        <div class="direct-chat-messages item-list-container" style="padding:0px;">
            <div class="row" id="filter-list" style="padding-left:0px;padding-right:0px;margin-left:0px;margin-right:0px;padding-bottom:10px;">
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
.floatting-shortcut {
    background: #b5c7ff;
    font-weight: 600;
    padding: 0px 6px;
    position: absolute;
    top: 5px;
    border: solid 1px #545986;
    border-radius: 10px;
    box-shadow: 0px 0px 5px -1px #9797be;
    left: 5px;
}
</style>
<script type="text/javascript">
function toggleFullScreen() {
  var doc = window.document;
  var docEl = doc.documentElement;

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
    requestFullScreen.call(docEl);
  }
  else {
    cancelFullScreen.call(doc);
  }
}
function isFullScreen() {
  var doc = window.document;
  var docEl = doc.documentElement;

  var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
  var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

  if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
    return false;
  }
  else {
    return true;
  }
}
</script>