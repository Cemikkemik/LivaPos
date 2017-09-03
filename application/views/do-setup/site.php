<body class="register-page">
	<div class="register-box" style="width:40%;">
		<div class="register-logo">
			<a href="<?php echo base_url();?>"><b><?php echo __('Tendoo CMS');?></b> <?php echo get('str_core');?></a>
		</div>

		<div class="register-box-body">
			<p>
				<h4 class="text-center"><?php _e('Define site settings');?></h4>
			</p>
			<p>
				<?php echo fetch_notice_from_url();?>
			</p>
			<p>
				<?php echo $this->notice->output_notice();?>
			</p>
			<form method="post">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="form-group <?php echo form_error( 'site_name' ) ? 'has-error' : '';?>">
					<label for="site_name"><?php echo __( 'Site Name' );?></label>
					<input type="text" class="form-control" placeholder="<?php _e('Site Name');?>" name="site_name" value="<?php echo set_value('site_name');?>">
					<p class="help-block"><?php echo form_error( 'site_name' );?></p>
				</div>
				<div class="form-group">
					<label for="site_name"><?php echo __( 'Language' );?></label>
					<select type="text" class="form-control" name="lang">
					<?php
					foreach (get_instance()->config->item('supported_languages') as $key => $value) {
					?>
					<option <?php echo $key == riake('lang', $_GET) ? 'selected="selected"': '';
					?> value="<?php echo $key;
					?>"><?php echo $value;
					?></option>
					<?php
					
					}
					?>
					</select>
				</div>
				<?php echo $this->events->apply_filters('installation_fields', '');?>
				<div class="row">
					<div class="col-xs-8">
					</div>
					<!-- /.col -->
					<div class="col-xs-4">
						<button type="submit" class="btn btn-primary btn-block btn-flat"><?php _e( 'Save Settings' );?></button>
					</div>
					<!-- /.col -->
				</div>
			</form>
		</div>
		<!-- /.form-box -->
	</div>
	<!-- /.register-box -->

</body>

</html>