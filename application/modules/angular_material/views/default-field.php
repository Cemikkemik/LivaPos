<md-input-container class="md-block" md-no-float flex="50">
    <label><?php echo $title;?></label>
    <input type="text" <?php echo ( in_array( 'required', $AnguCrud->getValidations( $key ) )  ? 'required' : '' );?> name="<?php echo $key;?>" ng-model="fields[ '<?php echo $key;?>' ]">

    <div class="hint"><?php echo $AnguCrud->getFieldDescription( $key );?></div>

    <?php if( in_array( 'required', $AnguCrud->getValidations( $key ) ) ):?>
    <div ng-messages="entriesForm.<?php echo $key;?>.$error">
        <div ng-message="required"><?php echo __( 'This field is required', 'angular_material' );?></div>
    </div>
    <?php endif;?>

</md-input-container>
<br>
