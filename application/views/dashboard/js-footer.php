<script type="text/javascript">
/**
 * Introducing Angular on Tendoo CMS
**/

<?php echo $this->events->apply_filters( 'load_tendoo_app', "var tendooApp		=	angular.module( 'tendooApp', " . json_encode( ( Array ) $this->events->apply_filters( 'dashboard_dependencies', array() ) ) . " );" );?>

/**
 *  Prepare Clock
 *
**/

setInterval( () => {
    tendoo.date     =   moment( tendoo.date ).add( 1, 'seconds' ).toDate();
}, 1000 );
</script>
