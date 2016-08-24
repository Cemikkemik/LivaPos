<div class="box box-primary direct-chat direct-chat-primary">
<div class="box-header with-border">
  <h3 class="box-title"><?php _e( 'Order n:', 'nexo_restaurant' );?></h3>

  <div class="box-tools pull-right">
    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
    </button>
    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
      <i class="fa fa-comments"></i></button>
    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
  </div>
</div>
<!-- /.box-header -->
<div class="box-body" style="display: block;">
  
</div>
<!-- /.box-body -->
<div class="box-footer" style="display: block;">
  <form action="#" method="post">
    <button type="submit" class="btn btn-primary btn-flat"><?php _e( 'Proceed order', 'nexo_restaurant' );?></button>
  </form>
</div>
<!-- /.box-footer-->
</div>

<script type="text/javascript">
$( document ).ready(function(e) {
	setInterval( function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'item' ) );?>', {
			success	:	function( data ){
				console.log( data );
			},
			dataType:  'json',
		});
	}, 5000 );
});
</script>
