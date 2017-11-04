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

        for( $i = 0; $i <= 2; $i++ ) {
            $widgets[ $i ]    =   get_option( $this->events->apply_filters( 'column_' . $i . '_widgets', 'column_' . $i . '_widgets' ), [] );
            
            // var_dump( 
            //     'from_db',
            //     $widgets[ $i ]
            // );

            $widgetsNamespaces     =   [];
            foreach( $widgets[ $i ] as $widget ) {
                $widgetsNamespaces[]    =   $widget[ 'namespace' ];
            }

            // var_dump( $widgetsNamespaces );die;

            if( in_array( $namespace, $widgetsNamespaces ) ) {
                $widgetsExists      =   true;
            }
        }

        var_dump( $widgetExists );die;

        // register widget if it doesn't exist
        if( ! $widgetExists ) {
            $defaults                   =   [
                'wrapper'       =>  true,
            ];

            $config[ 'namespace' ]      =   $namespace;
            $widgets[0][]               =   array_merge( $defaults, $config );

            // var_dump( 'final', $widgets[0] );
            
            set_option(
                $this->events->apply_filters( 'column_0_widgets', 'column_0_widgets' ),
                $widgets[0]
            );
        }
    }
}