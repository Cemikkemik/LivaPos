<div ng-controller="welcomeCTRL">
    <p><?php echo __( 'Merci d\'avoir choisi d\'utiliser <strong>NexoPOS</strong> pour votre gérer votre boutique.', 'nexo' );?></p>
    <iframe width="500" height="300" src="https://www.youtube.com/embed/Pcs0vr3Izao" frameborder="0" allowfullscreen></iframe>
    <br>
    <br>
    <p><?php echo __('C\'est la première fois que <strong>NexoPOS</strong> est exécuté. Souhaitez-vous créer un exemple de boutique en activité, pour tester toutes les fonctionnalités ?<br><br><em>En appuyant sur "Annuler", Vous pourrez toujours activer cette option depuis les réglages.</em>', 'nexo' );?></p>
    <button ng-click="goTo( 'reset' )" class="btn btn-primary"><?php echo __( 'Activer la démo', 'nexo' );?></button>
    <button ng-click="goTo( 'dashboard' )" class="btn btn-default"><?php echo __( 'Non, Merci !', 'nexo' );?></button>
    <!-- <div class="row">
        <div class="col-md-6">
            <h4 class="text-center"><?php echo __( 'Tutoriels', 'nexo' );?></h4>
        </div>
    </div> -->
</div>
<script>
$( document ).ready( function(){
    tendooApp.controller( 'welcomeCTRL', [ '$scope', function( $scope ){
        $scope.goTo         =   function( string ) {
            if( string == 'reset' ) {
                tendoo.options.success(function(){
                    document.location = '<?php echo site_url(array( 'dashboard', 'nexo', 'settings', 'reset?hightlight_box=input-group' ));    ?>';
                }).set( 'nexo_first_run', true );
            } else {
                tendoo.options.success(function(){
                    document.location = '<?php echo site_url(array( 'dashboard' ));    ?>';
                }).set( 'nexo_first_run', true );                
            }
        }
    }])
})
</script>