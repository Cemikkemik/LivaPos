<div ng-controller="awesomeCrudAddController">
    <div class="row">
        <div class="col-md-8">
            <?php 
            $columns        =   $awesome_crud->get( 'columns' );
            $config         =   $awesome_crud->get( 'config' );

            foreach( $awesome_crud->get( 'fields' ) as $namespace => $field ) {
                $validation     =   explode( '|', $field );

                // skip if hidden
                if( @$config[ $namespace ][ 'hideField' ] === true ) {
                    break;
                }             

                if( in_array( 'string', $validation ) ) {
                    ?>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon1"><?php echo @$columns[ $namespace ] ? @$columns[ $namespace ] : $namespace;?></span>
                    <input name="fields[ '<?php echo $namespace;?>']" type="text" class="form-control" placeholder="<?php echo @$columns[ $namespace ] ? @$columns[ $namespace ] : $namespace;?>" aria-describedby="sizing-addon1">
                </div>
                <p class="help-block"><?php echo @$config[ $namespace ][ 'description' ] ? @$config[ $namespace ][ 'description' ] : null;?></p>
            </div>
                    <?php
                } 

                else if( in_array( 'numeric', $validation ) ) {
                    ?>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id="sizing-addon1"><?php echo @$columns[ $namespace ] ? @$columns[ $namespace ] : $namespace;?></span>
                    <input name="fields[ '<?php echo $namespace;?>']" type="text" class="form-control" placeholder="<?php echo @$columns[ $namespace ] ? @$columns[ $namespace ] : $namespace;?>" aria-describedby="sizing-addon1">
                </div>
                <p class="help-block"><?php echo @$config[ $namespace ][ 'description' ] ? @$config[ $namespace ][ 'description' ] : null;?></p>
            </div>
                    <?php
                }
                
                
                else if( in_array( 'textarea', $validation ) ) {

                    ?>
            <div class="form-group">
                <label for="<?php echo $namespace;?>">
                    <?php echo @$columns[ $namespace ] ? @$columns[ $namespace ] : $namespace;?>
                </label>
                <textarea name="fields[ '<?php echo $namespace;?>']" class="form-control" rows="3"></textarea>
                <p class="help-block"><?php echo @$config[ $namespace ][ 'description' ] ? @$config[ $namespace ][ 'description' ] : null;?></p>
            </div>
                    <?php
                }
                
                
            }
            ?>
        </div>
    </div>
</div>