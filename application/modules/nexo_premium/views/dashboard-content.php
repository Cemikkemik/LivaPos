<br />
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box"> <span class="info-box-icon bg-aqua"><i class="fa fa-line-chart"></i></span>
                <div class="info-box-content"> <span class="info-box-text"><?php _e( 'Ventes réalisées', 'nexo_premium' );?></span> <span class="info-box-number"><?php echo $Cache->get( 'sales_number' );?><small></small></span> </div>
                <!-- /.info-box-content --> 
            </div>
            <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box"> <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>
                <div class="info-box-content"> <span class="info-box-text"><?php _e( 'Chiffre d\'affaire globale', 'nexo_premium' );?></span>
                    <span class="info-box-number">
                    <?php echo $this->Nexo_Misc->display_currency( 'before' );?>
                    <?php echo $Cache->get( 'net_sales' );?>
                    <?php echo $this->Nexo_Misc->display_currency( 'after' );?>
                    </span>
                </div>
                <!-- /.info-box-content --> 
            </div>
            <!-- /.info-box --> 
        </div>
        <!-- /.col --> 
        
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                <div class="info-box-content"> <span class="info-box-text"><?php _e( 'Clients', 'nexo_premium' );?></span> <span class="info-box-number"><?php echo $Cache->get( 'customers_number' );?></span> </div>
                <!-- /.info-box-content --> 
            </div>
            <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-smile-o"></i></span>
                <div class="info-box-content"> <span class="info-box-text"><?php _e( 'Créances', 'nexo_premium' );?></span> 
                    <span class="info-box-number">
                    <?php echo $this->Nexo_Misc->display_currency( 'before' );?>
                    <?php echo $Cache->get( 'creances' );?>
                    <?php echo $this->Nexo_Misc->display_currency( 'after' );?>
                    </span> 
                </div>
                <!-- /.info-box-content --> 
            </div>
            <!-- /.info-box --> 
        </div>
        <!-- /.col --> 
    </div>
</div>