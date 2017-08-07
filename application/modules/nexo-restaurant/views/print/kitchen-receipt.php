<div class="container">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <address>
                <h2><?php echo @$Options[ store_prefix() . 'site_name' ];?></h2>
                <p><?php echo __( 'Kitchen Receipt', 'nexo-restaurant' );?></p>                
            </address>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
            <p>
                <em><?php echo sprintf( __( '<strong>Date</strong>: %', 'nexo-restaurant' ), $order[ 'DATE_CREATION' ] );?></em>
            </p>
            <p>
                <em><?php echo sprintf( __( '<strong>Order</strong>: %s', 'nexo-restaurant' ), $order[ 'CODE' ] );?></em>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="text-center">
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo __( 'Product', 'nexo-restaurant' );?></th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // is meals is enabled
                $meals      =   [];
                foreach( $items as $item ):
                    if( @$Options[ store_prefix() . 'disable_meal_feature' ] == 'yes' ) {
                ?>
                <tr>
                    <td class="col-md-9">
                        <?php echo empty( $item[ 'DESIGN' ] ) ? $item[ 'NAME' ] : $item[ 'DESIGN' ];?><br>
                        <?php 
                        
                        if( $modifiers  =   json_decode( $item[ 'MODIFIERS' ], true ) ) {
                            foreach( $modifiers as $modifier ) {
                                if( $modifier[ 'default' ] == '1' ) {
                                    ?>
                                    <em> + <?php echo $modifier[ 'name' ];?></em><br>
                                    <?php
                                }                                
                            }
                        }

                        ?>                
                    </td>
                    <td class="col-md-1" style="text-align: center"> <?php echo $item[ 'QTE_ADDED' ];?></td>
                </tr>
                <?php 
                    } else {
                        if( @$meals[ $item[ 'MEAL' ] ] != null ) {
                            $meals[ $item[ 'MEAL' ] ]       =   [];
                        }

                        // push
                        $meals[ $item[ 'MEAL' ] ][]         =   $item;
                    }
                endforeach;
                ?>
            </tbody>
        </table>
        <?php
        if( $meals ) {
            foreach( $meals as $key => $meal ) {
                ?>
            <table class="table-bordered table-striped">
                <thead>
                    <tr>
                        <td colspan=""><?php __( 'Meal: %s', 'nexo-restaurant' );?></td>
                    </tr>
                </thead>
                <?php foreach( $meal as $item ):?>
                <tr>
                    <td class="col-md-9">
                        <em>[<?php echo $key;?>] &mdash; <?php echo empty( $meal[ 'DESIGN' ] ) ? $meal[ 'NAME' ] : $meal[ 'DESIGN' ];?></em><br>
                        <?php 
                        if( $modifiers  =   json_decode( $meal[ 'MODIFIERS' ], true ) ) {
                            foreach( $modifiers as $modifier ) {
                                if( $modifier[ 'default' ] == '1' ) {
                                    ?>
                                    <em> + <?php echo $modifier[ 'name' ];?></em><br>
                                    <?php
                                }                                
                            }
                        }
                        ?>                
                    </td>
                    <td class="col-md-1" style="text-align: center"> <?php echo $meal[ 'QTE_ADDED' ];?></td>
                </tr>   
                <?php endforeach;?>
            </table> 
                <?php
            }
        }
        ?>
        </td>
    </div>
<?php include_once( dirname( __FILE__ ) . '/bootstrap4.min.php' );?>
 