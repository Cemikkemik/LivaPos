<?php
//$this->set_css($this->default_theme_path.'/bootstrap/css/flexigrid.css');
$this->set_js_lib($this->default_theme_path.'/bootstrap/js/jquery.form.js');
$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.form.min.js');
$this->set_js_config($this->default_theme_path.'/bootstrap/js/flexigrid-add.js');

$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.noty.js');
$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/config/jquery.noty.config.js');

// If we have at least one Group
echo form_open($insert_url, 'method="post" id="crudForm"  enctype="multipart/form-data"');

if( $groups ) {
		?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
	$index	=	0;
	foreach( $groups as $namespace	=> $group ) {
		?>
        <li class="<?php echo $index == 0 ? 'active': '';?>"> <a href="#<?php echo $namespace;?>" data-toggle="tab" aria-expanded="true">
            <?php if ( @$group[ 'icon' ] != '' ) : ?>
            <i class="fa <?php echo $group[ 'icon' ];?>"></i>
            <?php endif;?>
            <?php echo $group[ 'title' ];?> </a> </li>
        <?php
        $index++;
    }
    ?>
    </ul>
    <div class="tab-content">
        <?php
		$index	=	0;
		foreach( $groups as $namespace	=> $group ) {
					?>
        <div class="tab-pane <?php echo $index == 0 ? 'active': '';?>" id="<?php echo $namespace;?>">
            <?php
			$counter = 0;
			foreach ($fields as $field) {
				// var_dump( $field );
				if( in_array( $field->field_name, $group[ 'fields' ] ) ) {
					$even_odd = $counter % 2 == 0 ? 'odd' : 'even';
					$counter++;
					if ($input_fields[ $field->field_name ]->crud_type != 'relation_invisible') {
						if (
							in_array($input_fields[ $field->field_name ]->type, array( 'double', 'varchar', 'int', 'float' )) &&
							in_array($input_fields[ $field->field_name ]->crud_type, array( false, 'integer' ), true)) {
							?>
            <div class="form-group <?php echo $even_odd?>" id="<?php echo $field->field_name;
							?>_field_box">
                <div class="input-group"> <span class="input-group-addon" id="basic-addon1"> <?php echo $input_fields[$field->field_name]->display_as;
							?> <?php echo ($input_fields[$field->field_name]->required)? " <span class='required label label-danger'>*</span> " : "";
							?> </span> <?php echo $input_fields[$field->field_name]->input;
							?> 
                    <!--<input type="text" class="form-control" placeholder="<?php echo $field->display_as;
							?>" name="<?php echo $field->field_name;
							?>" aria-describedby="basic-addon1"> --> 
                </div>
                <?php if( ! empty( $field->description ) ):?>
                <br />
                <?php echo $field->description;?>
                <?php endif;?>
            </div>
            <?php 
						} else {
			?>
            <div class="form-group <?php echo $even_odd?>" id="<?php echo $field->field_name;
							?>_field_box">
                <label for="exampleInputEmail1"><?php echo $input_fields[$field->field_name]->display_as;
							?><?php echo ($input_fields[$field->field_name]->required)? " <span class='required label label-danger'>*</span> " : "";
							?></label>
                <br />
                <?php echo $input_fields[$field->field_name]->input;?> 
			</div>
            	<?php echo $field->description;?>
            <?php	
								}
							}
						}
					}?>
        </div>
        <!-- /.tab-pane --> 
        <?php
					$index++;
				}
				?>
    </div>
    <!-- /.tab-content --> 
</div>
<?php
	} else {
?>
<div id='main-table-box'>
    <div class='form-div'>
        <?php
		$counter = 0;
		foreach ($fields as $field) {
			$even_odd = $counter % 2 == 0 ? 'odd' : 'even';
			$counter++;
			if ($input_fields[ $field->field_name ]->crud_type != 'relation_invisible') {
				if (
					in_array($input_fields[ $field->field_name ]->type, array( 'double', 'varchar', 'int', 'float' )) &&
					in_array($input_fields[ $field->field_name ]->crud_type, array( false, 'integer' ), true)) {
					?>
        <div class="form-group <?php echo $even_odd?>" id="<?php echo $field->field_name;
					?>_field_box">
            <div class="input-group"> <span class="input-group-addon" id="basic-addon1"> <?php echo $input_fields[$field->field_name]->display_as;
					?> <?php echo ($input_fields[$field->field_name]->required)? " <span class='required label label-danger'>*</span> " : "";
					?> </span> <?php echo $input_fields[$field->field_name]->input;
					?> 
                <!--<input type="text" class="form-control" placeholder="<?php echo $field->display_as;
					?>" name="<?php echo $field->field_name;
					?>" aria-describedby="basic-addon1"> --> 
            </div>
            <?php echo $field->description;?>
        </div>
        <?php 
				} else {
					?>
        <div class="form-group <?php echo $even_odd?>" id="<?php echo $field->field_name;
					?>_field_box">
            <label for="exampleInputEmail1"><?php echo $input_fields[$field->field_name]->display_as;
					?><?php echo ($input_fields[$field->field_name]->required)? " <span class='required label label-danger'>*</span> " : "";
					?></label>
            <br />
            <?php echo $input_fields[$field->field_name]->input;
					?> </div>
                    <?php echo $field->description;?>
        <?php

				}
			}
		}?>
    </div>
</div>
<?php
	}
	?>
<!-- Start of hidden inputs -->
<?php
    foreach ($hidden_fields as $hidden_field) {
        echo $hidden_field->input;
    }
    ?>
<!-- End of hidden inputs -->
<?php if ($is_ajax) {
    ?>
<input type="hidden" name="is_ajax" value="true" />
<?php 
    }
    ?>
<div id='report-error' class='report-div error'></div>
<div id='report-success' class='report-div success'></div>
<div class="buttons-box">
    <div class="btn-group" role="group">
        <input id="form-button-save" type='submit' value='<?php echo $this->l('form_save'); ?>'  class="btn btn-success"/>
    </div>
    <?php     if (!$this->unset_back_to_list) {
    ?>
    <div class="btn-group" role="group">
        <input class="btn btn-primary" type='button' value='<?php echo $this->l('form_save_and_go_back');
    ?>' id="save-and-go-back-button"  class="btn btn-large"/>
    </div>
    <div class="btn-group" role="group">
        <input class="btn btn-danger"  type='button' value='<?php echo $this->l('form_cancel');
    ?>' class="btn btn-large" id="cancel-button" />
    </div>
    <?php 
} ?>
    <div class='form-button-box' style="display:none;">
        <div class='small-loading' id='FormLoading'><?php echo $this->l('form_insert_loading'); ?></div>
    </div>
    <div class='clear'></div>
</div>
<?php echo form_close(); ?> 
<script>
	var validation_url = '<?php echo $validation_url?>';
	var list_url = '<?php echo $list_url?>';

	var message_alert_add_form = "<?php echo $this->l('alert_add_form')?>";
	var message_insert_error = "<?php echo $this->l('insert_error')?>";
</script>