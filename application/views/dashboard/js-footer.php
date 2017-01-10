<script type="text/javascript">
/**
 * Introducing Angular on Tendoo CMS
**/

var tendooApp		=	angular.module( 'tendooApp', <?php echo json_encode( ( Array ) $this->events->apply_filters( 'dashboard_dependencies', array() ) );?> );
</script>
