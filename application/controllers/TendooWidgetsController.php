<?php
class TendooWidgetsController extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $request    =   json_decode( file_get_contents( 'php://input' ), true );

        foreach( $request[ 'widgets' ] as $i => $column ) {
            set_option( $this->events->apply_filters( 'column_' . $i . '_widgets', 'column_' . $i . '_widgets' ), $column );
        }
    }

    public function sample()
    {
        return response()->json([
            'foo' => 'bar'
        ]);
    }

    public function foo()
    {
        $data       =   [];
        for( $i = 0; $i < 10 ; $i++ ) {
            $data[]     =   [
                'title'     =>  'Title ' . $i,
                'message'   =>  'Message' . $i
            ];
        }
        return response()->json( $data );
    }
}