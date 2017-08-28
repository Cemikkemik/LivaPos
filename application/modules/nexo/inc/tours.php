<?php
class Nexo_Tours extends CI_Model
{
    public function __construct()
    {
		parent::__construct();
		
		if( get_option( 'nexo_first_run' ) == null && ! in_array( $this->uri->segment( 3 ), [
			'about'
		] ) && $this->uri->segment( 1 ) == 'dashboard' && ! in_array( $this->uri->segment( 2 ), [ 'modules', 'options' ] ) ) {
			redirect([ 'dashboard', 'nexo', 'about' ]);
		}

        $this->events->add_action( 'load_dashboard', function(){
            get_instance()->enqueue->css_namespace( 'dashboard_header' );
            get_instance()->enqueue->css( '../modules/nexo/bower_components/bootstrap-tour/build/css/bootstrap-tour.min' );

            get_instance()->enqueue->js_namespace( 'dashboard_footer' );
            get_instance()->enqueue->js( '../modules/nexo/bower_components/bootstrap-tour/build/js/bootstrap-tour.min' );
		});
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
			this.IsFirstRun	=	<?php echo @$Options[ 'nexo_first_run' ] ? 'false' : 'true';?>;
			this.ShowPrompt	=	function(){
				if( this.IsFirstRun == true ){
					bootbox.confirm( '<?php
                        echo
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

			$( '#Nexo_restaure_value' ).bind( 'focus', function(){
				tour.end();
			});
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
