<?php
global $tendoo_cmd;

return;

class CMD_Create_Module {

}

if( $tendoo_cmd[1] == 'module' ) {
    if( @$argv[2] != null ) {
        global $module;
        $name       =   $argv[2];
        $module     =   [];

        if( is_dir( APPPATH . 'modules/' . $name ) ) {
            echo 'a folder with that name already exists !' . PHP_EOL;
            exit;
        }

        mkdir( APPPATH . 'modules/' . $name );
        global $module;
        $module[ 'application' ]                    =   [];
        $module[ 'application' ][ 'namspace' ]      =   $name;
        echo "module has been created" . PHP_EOL;
    }
    echo "module name is missing" . PHP_EOL;
    exit;
}
exit;

AksModuleName:
prompt( "What is the name of the module ?", function( $name ){
    if( $name != '' ) {
        global $module;
        $module[ 'application' ][ 'name' ]      =   $name;
        return [
            'msg'   =>  "Name has been saved",
            'goto'  =>  'AskAuthorName';
        ]
        echo "Name has been saved";
        goto AskAuthorName;
    } else {
        echo "Invalid Name";
        goto AskModuleName;
    }
});
exit;

// Skip this
AskFileName:
prompt( "What should be the main file name ? ", function( $name ) {
    if( $name != '' ) {
        global $module;
        $module[ 'application' ][ 'main' ]      =   $name;
        file_put_contents( APPPATH . 'modules/' . $module[ 'application' ][ 'main' ] . '.php', "created using flash" );
        echo "Name has been saved";

        // Ask Author Name
        goto AskAuthorName;
    } else {
        echo "Invalid File name";
        goto AskFileName;
    }
});
exit;

AskAuthorName:
prompt( "What is the author name ? ", function( $name ) {
    if( $name != '' ) {
        global $module;
        $module[ 'application' ][ 'author' ]      =   $name;
        echo "Author has been saved";
    } else {
        echo "Invalid Author Name";
        goto AskAuthorName;
    }
});
exit;
