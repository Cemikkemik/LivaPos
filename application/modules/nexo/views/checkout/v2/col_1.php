<div class="box box-primary direct-chat direct-chat-primary" id="cart-details-wrapper" style="margin-bottom:0px;visibility:hidden">
    <div class="box-header with-border" id="cart-header">
        <h3 class="box-title">
            <?php _e( 'Caisse', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="<?php _e( 'Sélectionner un client	' );?>" data-widget="chat-pane-toggle"> <i class="fa fa-users"></i>
            <?php _e( 'Sélectionner un client' );?>
            </button>
            <button type="button" class="btn btn-box-tool" title="<?php _e( 'Ajouter un client' );?>"> <i class="fa fa-comments"></i>
            <?php _e( 'Ajouter un client' );?>
            </button>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-search-wrapper">
        <form action="#" method="post">
            <div class="input-group">
                <input type="text" name="message" placeholder="<?php _e( 'Codebarre ou UGS...' );?>" class="form-control">
                <span class="input-group-btn">
                <button type="submit" class="btn btn-large btn-primary"><?php _e( 'Rechercher' );?></button>
                </span> </div>
        </form>
    </div>
    <!-- /.box-footer--> 
    <!-- /.box-header -->
    <div class="box-body">
    	<table class="table" id="cart-item-table-header">
        	<thead>
                <tr class="active">
                    <td width="250" class="text-left"><?php _e( 'Article', 'nexo' );?></td>
                    <td width="100" class="text-center"><?php _e( 'Prix Unitaire', 'nexo' );?></td>
                    <td width="150" class="text-center"><?php _e( 'Quantité', 'nexo' );?></td>
                    <td width="100" class="text-right"><?php _e( 'Prix Total', 'nexo' );?></td>
                </tr>
            </thead>
        </table>
        <div class="direct-chat-messages" id="cart-table-body" style="padding:0px;">
            <table class="table" style="margin-bottom:0;">                
                <tbody>
                	<tr id="cart-table-notice">
                    	<td colspan="4"><?php _e( 'Veuillez ajouter un produit...', 'nexo' );?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table" id="cart-details">
            <tfoot>
                <tr class="active">
                    <td width="300" class="text-right"></td>
                    <td width="100" class="text-right"></td>
                    <td width="100" class="text-right"><?php _e( 'Net hors taxe', 'nexo' );?></td>
                    <td width="100" class="text-right"><span id="cart-value"></span></td>
                </tr>
                <tr class="danger">
                    <td width="300" class="text-right"></td>
                    <td width="100" class="text-right"></td>
                    <td width="100" class="text-right"><?php _e( 'TVA', 'nexo' );?></td>
                    <td width="100" class="text-right"><span id="cart-vat"></span></td>
                </tr>
                <tr class="info">
                    <td width="300" class="text-right"></td>
                    <td width="100" class="text-right"></td>
                    <td width="100" class="text-right"><?php _e( 'Remise', 'nexo' );?></td>
                    <td width="100" class="text-right"><span id="cart-discount"></span></td>
                </tr>
                <tr class="success">
                    <td width="300" class="text-right"></td>
                    <td width="100" class="text-right"></td>
                    <td width="100" class="text-right"><strong><?php _e( 'Net à payer', 'nexo' );?></strong></td>
                    <td width="100" class="text-right"><span id="cart-topay"></span></td>
                </tr>
            </tfoot>
        </table>
        <!-- Contacts are loaded here -->
        <div class="direct-chat-contacts">
            <ul class="contacts-list">
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <li> <a href="#"> <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info"> <span class="contacts-list-name"> Count Dracula <small class="contacts-list-date pull-right">2/28/2015</small> </span> <span class="contacts-list-msg">How have you been? I was...</span> </div>
                    <!-- /.contacts-list-info --> 
                    </a> </li>
                <!-- End Contact Item -->
            </ul>
            <!-- /.contatcts-list --> 
        </div>
        <!-- /.direct-chat-pane --> 
    </div>
    <!-- /.box-body -->
    <div class="box-footer" id="cart-panel">
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg" style="margin-bottom:0px;">
			<i class="fa fa-money"></i>
			<?php _e( 'Payer', 'nexo' );?>
            </button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg"  style="margin-bottom:0px;"><?php _e( 'Remise', 'nexo' );?></button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-app btn-default btn-lg"  style="margin-bottom:0px;"><?php _e( 'Annuler', 'nexo' );?></button>
          </div>
        </div>
    </div>
    <!-- /.box-footer--> 
</div>
