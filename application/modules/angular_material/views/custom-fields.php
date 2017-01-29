<?php if( $AnguCrud->fieldsType[ $key ] == 'datetime' ):?>

    <md-input-container flex="50">
    <label><?php echo $title;?></label>
    <md-datepicker ng-model="fields[ '<?php echo $key;?>' ]"></md-datepicker>

    <div class="hint"><?php echo $AnguCrud->getFieldDescription( $key );?></div>

    </md-input-container>
    <br>

<?php elseif( $AnguCrud->fieldsType[ $key ] == 'email' ):?>

    <md-input-container class="md-block" md-no-float flex="50">
        <label><?php echo $title;?></label>
        <input type="email" <?php echo ( in_array( 'required', $AnguCrud->getValidations( $key ) )  ? 'required' : '' );?> name="<?php echo $key;?>" ng-model="fields[ '<?php echo $key;?>' ]">

        <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

        <?php if( in_array( 'required', $AnguCrud->getValidations( $key ) ) ):?>
        <div ng-messages="entriesForm.<?php echo $key;?>.$error">
            <div ng-message="required"><?php echo __( 'This field is required', 'angular_material' );?></div>
            <div ng-message="email"><?php echo __( 'This is not an email', 'angular_material' );?></div>
        </div>
        <?php endif;?>

    </md-input-container>
    <br>

<?php elseif( $AnguCrud->fieldsType[ $key ] == 'number' ):?>

    <md-input-container class="md-block" md-no-float flex="50">
        <label><?php echo $title;?></label>
        <input string-to-number type="number" <?php echo ( in_array( 'required', $AnguCrud->getValidations( $key ) )  ? 'required' : '' );?> name="<?php echo $key;?>" ng-model="fields[ '<?php echo $key;?>' ]">

        <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

        <?php if( in_array( 'required', $AnguCrud->getValidations( $key ) ) ):?>
        <div ng-messages="entriesForm.<?php echo $key;?>.$error">
            <div ng-message="required"><?php echo __( 'This field is required', 'angular_material' );?></div>
            <div ng-message="email"><?php echo __( 'This is not a number', 'angular_material' );?></div>
        </div>
        <?php endif;?>

    </md-input-container>
    <br>

<?php elseif( $AnguCrud->fieldsType[ $key ] == 'select_relation' ):?>

    <?php $matching     =   $AnguCrud->getMatching(); ?>

    <md-input-container class="md-block" flex="50">
        <label><?php echo $title;?></label>
        <md-select ng-model="fields[ '<?php echo $key;?>' ]"
                   md-on-close="resetSearchvalues()"
                   data-md-container-class="selectdemoSelectHeader">
          <md-select-header class="demo-select-header">
            <input ng-model="<?php echo $key;?>"
                   ng-init="<?php echo $key;?> = ''"
                   type="search"
                   placeholder="<?php echo $AnguCrud->searchSelectLabel;?>"
                   class="demo-header-searchbox md-text select-field">
          </md-select-header>
          <md-optgroup label="<?php echo $title;?>">
              <md-option ng-value=""><i><?php echo __( 'Empty', 'angular_material' );?></i></md-option>
            <md-option ng-repeat="option in relationsObject[ '<?php echo $matching[ $key ];?>' ] | filter:<?php echo $key;?>" ng-value="option.key">{{ option.value }}</md-option>
          </md-optgroup>
        </md-select>

        <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

      </md-input-container>
      <br>

  <?php elseif( $AnguCrud->fieldsType[ $key ] == 'select_relation_multiple' ):?>

      <?php $matching     =   $AnguCrud->getMatching();?>

      <md-input-container class="md-block" flex="50">
          <label><?php echo $title;?></label>
          <md-select ng-model="fields[ '<?php echo $key;?>' ]"
                     md-on-close="resetSearchvalues()"
                     data-md-container-class="selectdemoSelectHeader"
                     multiple>
            <md-select-header class="demo-select-header">
              <input ng-model="<?php echo $key;?>"
                     ng-init="<?php echo $key;?> = ''"
                     type="search"
                     placeholder="<?php echo $AnguCrud->searchSelectLabel;?>"
                     class="demo-header-searchbox md-text select-field">
            </md-select-header>
            <md-optgroup label="<?php echo $title;?>">
                <md-option ng-value=""><i><?php echo __( 'Empty', 'angular_material' );?></i></md-option>
              <md-option ng-repeat="option in relationsObject[ '<?php echo $matching[ $key ];?>' ] | filter:<?php echo $key;?>" ng-value="option.key">{{ option.value }}</md-option>
            </md-optgroup>
          </md-select>

          <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

        </md-input-container>
        <br>

  <?php elseif( $AnguCrud->fieldsType[ $key ] == 'select_options' ):?>

      <md-input-container class="md-block" flex="50">
          <label><?php echo $title;?></label>
          <md-select ng-model="fields[ '<?php echo $key;?>' ]"
                     md-on-close="resetSearchvalues()"
                     data-md-container-class="selectdemoSelectHeader">
            <md-select-header class="demo-select-header">
              <input ng-model="<?php echo $key;?>"
                     ng-init="<?php echo $key;?> = ''"
                     type="search"
                     placeholder="<?php echo $AnguCrud->searchSelectLabel;?>"
                     class="demo-header-searchbox md-text select-field">
            </md-select-header>
            <md-optgroup label="<?php echo $title;?>">
                <md-option ng-value=""><i><?php echo __( 'Empty', 'angular_material' );?></i></md-option>
              <md-option ng-repeat="option in selectOptions[ '<?php echo $key;?>' ] | filter:<?php echo $key;?>" ng-value="option.key">{{ option.value }}</md-option>
            </md-optgroup>
          </md-select>

          <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

        </md-input-container>
        <br>

    <?php elseif( $AnguCrud->fieldsType[ $key ] == 'textarea' ):?>

        <md-input-container class="md-block" flex="50">
          <label><?php echo $title;?></label>
          <textarea ng-model="fields[ '<?php echo $key;?>' ]" rows="5" md-select-on-focus></textarea><!-- md-maxlength="150" -->

          <div class="hint" ><?php echo $AnguCrud->getFieldDescription( $key );?></div>

          <?php if( in_array( 'required', $AnguCrud->getValidations( $key ) ) ):?>
          <div ng-messages="entriesForm.<?php echo $key;?>.$error">
              <div ng-message="required"><?php echo __( 'This field is required', 'angular_material' );?></div>
          </div>
          <?php endif;?>

        </md-input-container>

        <br>
<?php endif;?>
