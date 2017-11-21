<div  ng-controller="invoiceCTRL">
	<div style="background:#FFF;padding:15px;box-shadow:0 0 1px #333" class="invoice-container">
		<!-- title row -->
		<div class="row">
			<div class="col-xs-12">

			</div>
			<!-- /.col -->
		</div>
		<!-- info row -->
		<div class="row invoice-info">
			<div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
				<?php if( store_option( 'url_to_logo' ) != null ):?>
					<div class="text-center">
						<img src="<?php echo store_option( 'url_to_logo' );?>" 
						style="display:inline-block;<?php echo store_option( 'logo_height' ) != null ? 'height:' . store_option( 'logo_height' ) . 'px' : '';?>
						;<?php echo store_option( 'logo_width' ) != null ? 'width:' . store_option( 'logo_width' ) . 'px' : '';?>"/>
					</div>
				<?php else:?>
					<h2 class="text-center"><?php echo store_option( 'site_name' );?></h2>
				<?php endif;?>
			</div>
			<div class="col-sm-4 col-xs-4">
				<strong><?php echo __( 'Addresse de facturation', 'nexo' );?></strong><br>
				<address>
					<strong><?php echo __( 'Customer :', 'nexo' );?></strong> {{ data.order[0].customer_name }}
					<br> <strong><?php echo __( 'Téléphone', 'nexo' );?>:</strong> {{ billing.phone }}
					<br/> <strong><?php echo __( 'Email', 'nexo' );?>:</strong> {{ billing.email }}
					<br ng-if="billing.address_1 != null && billing.address_1 != ''"> {{ billing.address_1 ? billing.address_1 + ',' : '' }}
					<br ng-if="billing.address_2 != null && billing.address_2 != ''"> {{ billing.address_2 ? billing.address_2 + ',' : '' }}
					<br ng-if="billing.city != null && billing.city != ''"> {{ billing.city ? billing.city + ',' : '' }} {{ billing.pobox ? billing.pobox + ',' : '' }}
				</address>
			</div>

			<div class="col-sm-4 col-xs-4">
				<b>
					<?php echo __( 'Facture N°:', 'nexo' );?> </b>{{ ( "00000" + data.order[0].ID ).slice(-6) }}
				<br/>
				<b>
					<?php echo __( 'Code :', 'nexo' );?>
				</b> {{ data.order[0].CODE }}
				<br/>
				<b><?php echo __( 'Date', 'nexo' );?>:</b> {{ data.order[0].DATE_CREATION | date }}
			</div>

			<div class="col-sm-4 col-xs-4">
				<strong><?php echo __( 'Addresse de livraison', 'nexo' );?></strong><br>
				<address>
					<strong><?php echo __( 'Customer :', 'nexo' );?></strong> {{ data.order[0].customer_name }}
					<br> 
					<strong><?php echo __( 'Téléphone', 'nexo' );?>:</strong> {{ shipping.phone }}
					<br/> <strong><?php echo __( 'Email', 'nexo' );?>:</strong> {{ shipping.email }}
					<br ng-if="shipping.address_1 != null && shipping.address_1 != ''"> {{ shipping.address_1 ? shipping.address_1 + ',' : '' }}
					<br ng-if="shipping.address_2 != null && shipping.address_2 != ''"> {{ shipping.address_2 ? shipping.address_2 + ',' : '' }}
					<br ng-if="shipping.city != null && shipping.city != ''"> {{ shipping.city ? shipping.city + ',' : '' }} {{ shipping.pobox ? shipping.pobox + ',' : '' }}

				</address>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- Table row -->
		<div class="row">
			<div class="col-xs-12 table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>
								<?php echo __( 'Produit', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Prix', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Remise', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Quantité', 'nexo' );?>
							</th>
							<th>
								<?php echo __( 'Total', 'nexo' );?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in data.products">
							<td>{{ item.DESIGN }}</td>
							<td>{{ item.PRIX_BRUT | moneyFormat }}</td>
							<td>{{ '-' + ( item.PRIX_BRUT - item.PRIX ) | moneyFormat }}</td>
							<td>{{ item.QUANTITE }}</td>
							<td>{{ item.PRIX * item.QUANTITE | moneyFormat }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<div class="row">
			<!-- accepted payments column -->
			<div class="col-xs-6">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th style="width:50%"><?php echo __( 'Sous-Total', 'nexo' );?></th>
							<td>{{ subTotal( data.products ) | moneyFormat }}</td>
						</tr>
						<tr>
							<th><?php echo __( 'Livraison', 'nexo' );?>:</th>
							<td>{{ data.order[0].SHIPPING_AMOUNT | moneyFormat }}</td>
						</tr>
						<tr>
							<th><?php echo __( 'Total', 'nexo' );?></th>
							<td>{{ total() | moneyFormat }}</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="col-xs-6">
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<!-- this row will not appear when printing -->
		<div class="row no-print hidden-print">
			<div class="col-xs-12">
				<a href="javascript:void(0)" print-item=".invoice-container" class="btn btn-default">
					<i class="fa fa-print"></i> <?php echo __( 'Imprimer', 'nexo' );?></a>
			</div>
		</div>
	</div>
</div>