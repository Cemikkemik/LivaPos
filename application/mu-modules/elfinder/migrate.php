<?php
return [
     '1.4'     =>   function(){
          get_instance()->aauth        =    get_instance()->users->auth;
          get_instance()->aauth->create_perm( 'view_file_manager',    __( 'File Manager Access', 'nexo'),            __('Let the use have access to the file manager.', 'nexo'));
          get_instance()->aauth->allow_group( 'master', 'view_file_manager');
     }
];