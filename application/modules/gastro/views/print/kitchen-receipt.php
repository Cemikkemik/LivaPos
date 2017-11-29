<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo __( 'Kitchen Receipt', 'gastro' );?></title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <address>
                <h2><?php echo store_option( 'site_name' );?></h2>
                <p><em><?php echo sprintf( __( '<strong>Kitchen </strong>: %s', 'gastro' ), @$kitchen[ 'NAME' ] ? @$kitchen[ 'NAME' ] : __( 'Main Kitchen', 'nexo-restauran' ) );?></em></p>            
                <?php if( isset( $table ) ):?>
                    <p><em><?php echo sprintf( __( '<strong>Table </strong>: %s', 'gastro' ), @$table[0][ 'NAME' ] ? @$table[0][ 'NAME' ] : '' );?></em></p>            
                <?php endif;?>
                <?php if( $order ):?>
                    <?php
                    switch( $order[ 'RESTAURANT_ORDER_TYPE' ] ) {
                        case 'dinein' :  $type  =   __( 'Dine in', 'gastro' ); break;
                        case 'takeaway' :  $type  =   __( 'Take Away', 'gastro' ); break;
                        case 'delivery' :  $type  =   __( 'Delivery', 'gastro' ); break;
                        default: $type  =   __( 'Unknow Order Type', 'gastro' ); break;
                    }
                    ?>
                    <p><em><?php echo sprintf( __( '<strong>Order </strong>: %s', 'gastro' ), $type );?></em></p>            
                <?php endif;?>
            </address>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
            <p>
                <em><?php echo sprintf( __( '<strong>Date</strong>: %s', 'gastro' ), $order[ 'DATE_CREATION' ] );?></em>
            </p>
            <p>
                <em><?php echo sprintf( __( '<strong>Order</strong>: %s', 'gastro' ), $order[ 'CODE' ] );?></em>
            </p>
            <p>
                <em><?php echo sprintf( __( '<strong>Placed By</strong>: %s', 'gastro' ), $order[ 'AUTHOR_NAME' ] );?></em>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="text-center">
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo __( 'Product', 'gastro' );?></th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // is meals is enabled
                $meals      =   [];
                foreach( $items as $item ):
                    
                    if( store_option( 'disable_meal_feature' ) == 'yes' ) {
                ?>
                <tr>
                    <td >
                        <?php echo empty( $item[ 'DESIGN' ] ) ? $item[ 'NAME' ] : $item[ 'DESIGN' ];?><br>
                        <?php 
                        if( @$item[ 'FOOD_NOTE'] != null ) {
                            echo $item[ 'FOOD_NOTE' ] . '<br>';
                        }
                        
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
                    <td style="text-align: center"> <?php echo $item[ 'QTE_ADDED' ];?></td>
                </tr>
                <?php 
                    } else {
                        if( @$meals[ $item[ 'MEAL' ] ] == null ) {
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
            <table class="table table-bordered table-striped" style="margin-bottom:20px">
                <thead>
                    <tr>
                        <td colspan="1"><?php echo sprintf( __( 'Meal: %s', 'gastro' ), $key );?></td>
                        <td class="text-center"><?php echo __( 'Quantity', 'gastro' );?></td>
                    </tr>
                    
                </thead>
                <?php foreach( $meal as $item ):?>
                <tr>
                    <td>
                        <?php echo empty( $item[ 'DESIGN' ] ) ? $item[ 'NAME' ] : $item[ 'DESIGN' ];?><br>
                        <?php 
                        if( @$item[ 'FOOD_NOTE'] != null ) {
                            echo $item[ 'FOOD_NOTE' ] . '<br>';
                        }
                        
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
                    <td style="text-align: center" width="200"> <?php echo $item[ 'QTE_ADDED' ];?></td>
                </tr> 
                <?php endforeach;?>
            </table> 
                <?php
            }
        }
        ?>
        </td>
    </div>
<?php // include( MODULESPATH . 'nexo/inc/bootstrap3-style.php' );?>
</body>
</html>