<?php
class Nexo_Tours extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'dashboard_footer', array( $this, 'demo_prompt' ) );
		$this->events->add_action( 'dashboard_footer', array( $this, 'general_guides' ) );
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
					bootbox.confirm( '<?php echo addslashes( __( 'C\'est la première fois que Nexo est exécuté. Souhaitez-vous créer un exemple de boutique en activité, pour tester toutes les fonctionnalités ?<br><br><em>En appuyant sur "Annuler", Vous pourrez toujours activer cette option depuis les réglages.</em>', 'nexo' ) );?>', function( action ) {
						if( action == true ) {
							tendoo.options.success(function(){
								document.location = '<?php echo site_url( array( 'dashboard', 'nexo', 'settings','reset?hightlight_box=input-group' ) );?>';
							}).set( 'nexo_first_run', true );
						} else {
							tendoo.options.set( 'nexo_first_run', true );
						}
					});
				}
			};
			this.ShowPrompt();
		};
		</script>
        <script type="text/javascript" src="<?php echo module_url( 'nexo' );?>/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
        <link rel="stylesheet" media="all" href="<?php echo module_url( 'nexo' ) . '/bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css';?>" />
        <?php if( @$_GET[ 'hightlight_box' ] == 'input-group' ):?>
        <script>
		$( document ).ready(function(e) {
           var tour = new Tour({
			  steps: [
			  {
				element: ".<?php echo $_GET[ 'hightlight_box' ];?>",
				title: '<?php echo addslashes( __( 'Choisissez une option de reinitialisation', 'nexo' ) );?>',
				content: '<?php echo addslashes( __( 'Veuillez choisir une option dans la liste de réinitialisation', 'nexo' ) );?>',
				placement: 'right',
				backdrop	: true,
				storage		: false
			  }
			]});
			// Initialize the tour
			tour.init();
			
			// Start the tour
			tour.start(); 
        });
		</script>
        <?php endif;?>
        <?php
	}
	
	/** 
	 * General Guide
	**/
	
	public function general_guides()
	{
		if( @$_GET[ 'guide' ] != 'true' ) : return; endif;
		
		if( uri_string() == 'dashboard/nexo/commandes/lists/add' ) {
			$this->load->module_view( 'nexo', 'guides/checkout' );
		}
	}
}
new Nexo_Tours;