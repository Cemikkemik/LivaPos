<?php
class Nexo_Tours extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action('dashboard_footer', array( $this, 'demo_prompt' ));
        // $this->events->add_action('dashboard_footer', array( $this, 'general_guides' )); deprecated
    }
    
    /**
     * Demo Prompt
     *
     * @return void 
    **/
    
    public function demo_prompt()
    {
        global $Options;
        ?>
        <script type="text/javascript">
		var	NexoFirstRun	=	new function(){
			this.IsFirstRun	=	<?php echo @$Options[ 'nexo_first_run' ] ? 'false' : 'true';
        ?>;
			this.ShowPrompt	=	function(){
				if( this.IsFirstRun == true ){
					bootbox.confirm( '<?php echo
						'<div class="row text-justified">' .
							'<div class="col-lg-6">' .
								_s( '<h4 class="text-center">Bienvenue sur NexoPOS</h4>', 'nexo' ) . '<br>' .
								_s( 'Merci d\'avoir choisi d\'utiliser <strong>NexoPOS</strong> pour votre gérer votre boutique.', 'nexo' ) .
								'<br>' . '<br>' . 
								_s('C\'est la première fois que <strong>NexoPOS</strong> est exécuté. Souhaitez-vous créer un exemple de boutique en activité, pour tester toutes les fonctionnalités ?<br><br><em>En appuyant sur "Annuler", Vous pourrez toujours activer cette option depuis les réglages.</em>', 'nexo' ) . 	
							'</div>' . 
							'<div class="col-lg-6 text-justified">' .
								_s( '<h4 class="text-center">Comment ça marche ?</h4>', 'nexo' ) . '<br>' .
								'<iframe style="width:100%" height="300" src="https://www.youtube.com/embed/Pcs0vr3Izao" frameborder="0" allowfullscreen></iframe>' .
							'</div>' . 
							
						'</div>';
        ?>', function( action ) {
						if( action == true ) {
							tendoo.options.success(function(){
								document.location = '<?php echo site_url(array( 'dashboard', 'nexo', 'settings', 'reset?hightlight_box=input-group' ));
        ?>';
							}).set( 'nexo_first_run', true );
						} else {
							tendoo.options.set( 'nexo_first_run', true );
						}
					});
					$( '.modal-dialog' ).css( 'width', '80%' );
					$( '.bootbox-close-button' ).remove();
				}
			};
			this.ShowPrompt();
		};
		</script>
        <script type="text/javascript" src="<?php echo module_url('nexo');
        ?>/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
        <link rel="stylesheet" media="all" href="<?php echo module_url('nexo') . '/bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css';
        ?>" />
        <?php if (@$_GET[ 'hightlight_box' ] == 'input-group'):?>
        <script>
		$( document ).ready(function(e) {
           var tour = new Tour({
			  steps: [
			  {
				element: ".<?php echo $_GET[ 'hightlight_box' ];
        ?>",
				title: '<?php echo addslashes(__('Choisissez une option de reinitialisation', 'nexo'));
        ?>',
				content: '<?php echo addslashes(__('Veuillez choisir une option dans la liste de réinitialisation', 'nexo'));
        ?>',
				placement: 'right'
				
			  }
			], 
			backdrop	: true,
			storage		: false });
			// Initialize the tour
			tour.init();
			
			// Start the tour
			tour.start(); 
        });
		</script>
        <?php endif;
    }
    
    /** 
     * General Guide
    **/
    
    public function general_guides()
    {
        if (@$_GET[ 'guide' ] != 'true') : return;
        endif;
        
        if (uri_string() == 'dashboard/nexo/commandes/lists/add') {
            $this->load->module_view('nexo', 'guides/checkout');
        }
    }
}
new Nexo_Tours;
