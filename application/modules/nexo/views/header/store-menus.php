<?php
get_instance()->load->model( 'Nexo_Stores' );

$stores		=	get_instance()->Nexo_Stores->get( 'opened', 'STATUS' );

global $Options;

if( @$Options[ 'nexo_store' ] == 'enabled' ) {
?>
<li class="messages-menu">
    <?php
    if( User::in_group( 'shop_cashier' ) || User::in_group( 'shop_tester' ) ) {
        $store_url	=	site_url( array( 'dashboard', 'nexo', 'stores', 'all' ) );
    } else {
        $store_url	=	site_url( array( 'dashboard', 'nexo', 'stores', 'lists' ) );
    }
    ?>
    <a href="<?php echo $store_url;?>" title="<?php _e( 'Gérer les boutiques', 'nexo' );?>">
        <i class="fa fa-home"></i>
    </a>
</li>
<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
        <i class="fa fa-cubes"></i>
        <?php _e( 'Boutiques', 'nexo' );?>
    </a>
    <ul class="dropdown-menu">
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <?php if( $stores ):?>
                    <?php $stores   =   $this->events->apply_filters( 'stores_list_menu', $stores );?>
                    <?php foreach( $stores as $store ): ?>
                        <?php if( $store[ 'STATUS' ] == 'opened' ):?>
                            <li><!-- start message -->
                                <a href="<?php echo $store[ 'ID' ] != false ? site_url( array( 'dashboard', 'stores', $store[ 'ID' ] ) ) : "#";?>">
                                <div class="pull-left">
                                    <?php if (  $store[ 'IMAGE' ] != '' ): ?>
                                        <img src="<?php echo upload_url() . '/stores/' . $store[ 'IMAGE' ];?>" class="img-circle" alt="User Image">
                                    <?php else: ?>
                                            <img src="<?php echo module_url( 'nexo' ) . '/images/default.png';?>" class="img-circle" alt="User Image">
                                    <?php endif; ?>
                                </div>
                                <h4>
                                    <?php echo xss_clean( $store[ 'NAME' ] );?>
                                    <!-- <small><i class="fa fa-clock-o"></i> 5 mins</small>-->
                                </h4>
                                <p><?php echo $store[ 'DESCRIPTION' ] != '' ? xss_clean( $store[ 'DESCRIPTION' ] ) : __( 'Aucune description disponible', 'nexo' );?></p>
                                </a>
                            </li>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php else:?>
                <li>
                    <a href="<?php echo site_url( array( 'dashboard', 'nexo', 'stores', 'lists', 'add' ) );?>"><?php _e( '+1 Créer une boutique', 'nexo' );?></a>
                </li>
            <?php endif;?>
            </ul>
        </li>
    </ul>
</li>
<?php
}
