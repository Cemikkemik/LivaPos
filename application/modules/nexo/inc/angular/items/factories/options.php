<script type="text/javascript">
    "use strict";
    tendooApp.factory( 'options', function(){
        return {
            yesOrNo         :   [
                {
                    value       :   'yes',
                    label       :   '<?php echo _s( 'Oui', 'nexo' );?>'
                },{
                    value       :   'no',
                    label       :   '<?php echo _s( 'Non', 'nexo' );?>'
                }
            ]
        }
    });
</script>
