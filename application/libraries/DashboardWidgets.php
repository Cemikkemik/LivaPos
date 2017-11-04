<?php
class DashboardWidgets 
{
    private $widgets    =   [];

    public function __construct()
    {
        $this->events       =   get_instance()->events;

        // Save empty places
        $widgets            =   [];
        for( $i = 0; $i < 2; $i++ ) {
            $widgets[ $i ]    =   get_option( $this->events->apply_filters( 'column_' . $i . '_widgets', 'column_' . $i . '_widgets' ) ); 
            if( $widgets[ $i ] == null ) {
                set_option( $this->events->apply_filters( 'column_' . $i . '_widgets', 'column_' . $i . '_widgets' ), []);
            }
        }
    }

    /**
     * Register Widgets
     * @param string widget namespace
     * @param array widget config
     * @return void
     */
    public function register( $namespace, $config )
    {
        $this->widgets[ $namespace ]    =   $config;

        // check if the widget has yet been placed into one column
        // Lopping columns
        $widgetExists       =   false;
        $widgets            =   [];

        for( $i = 0; $i < 2; $i++ ) {
            $widgets[ $i ]    =   get_option( $this->events->apply_filters( 'column_' . $i . '_widgets', 'column_' . $i . '_widgets' ), [] );

            $widgetsNamespaces     =   [];
            foreach( $widgets[ $i ] as $widget ) {
                $widgetsNamespaces[]    =   $widget[ 'namespace' ];
            }

            if( in_array( $namespace, $widgetsNamespaces ) ) {
                $widgetsExists      =   true;
            }
        }

        // register widget if it doesn't exist
        if( ! $widgetExists ) {
            $config[ 'namespace' ]      =   $namespace;
            $widgets[1][]               =   $config;
            
            set_option(
                $this->events->apply_filters( 'column_0_widgets', 'column_0_widgets' ),
                $widgets[1]
            );
        }
    }
}