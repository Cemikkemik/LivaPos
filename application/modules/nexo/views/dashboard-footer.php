<script type="text/javascript">

        "use strict";

    /**
        * Popup Print dialog
        * @param string data
        * @return bool
    **/

    NexoAPI.Popup			=	function(data) {
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title><?php echo addslashes(Html::get_title());
?></title>');
        mywindow.document.write('<link rel="stylesheet" href="<?php echo module_url('nexo') . 'bower_components/bootstrap/dist/css/bootstrap.min.css';
?>" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        setTimeout( function(){
            mywindow.print();
            // mywindow.close();
        }, 600 );

        return true;
    };

    /**
        * Bind Print item
        *
    **/

    NexoAPI.BindPrint		=	function() {
        $( '[print-item]' ).bind( 'click', function(){
            NexoAPI.PrintElement( $(this).attr( 'print-item' ) );
        });
    }

    /**
        * Currency Position
    **/

    NexoAPI.CurrencyPosition	=	function( amount ) {
        return '<?php echo addslashes($this->Nexo_Misc->display_currency('before'));
?> ' + amount + ' <?php echo addslashes($this->Nexo_Misc->display_currency('after'));
?>';
    }

    /**
        * Currency Position + Money Format
    **/

    NexoAPI.DisplayMoney		=	function( amount ) {
        return NexoAPI.CurrencyPosition( NexoAPI.Format( parseFloat( amount ) ) );
    }



var NexoSound		=	'<?php echo asset_url('/modules/nexo/sound/sound-');
?>';

$( document ).ready(function(e) {
    // @since 2.6.1

    NexoAPI.Bootbox	=	function(){
        <?php if (in_array('bootbox', $this->config->item('nexo_sound_fx'))):?>
        NexoAPI.Sound(2);
        return bootbox;
        <?php endif;
?>
    }

    NexoAPI.Notify	=	function(){
        NexoAPI.Sound(1);
        return tendoo.notify;
    }

    NexoAPI.Toast    =   function(){
        NexoAPI.Sound(1);
        var showtoast = new ToastBuilder({
            defaultText: 'Toast, yo!',
            displayTime: 2000,
            target: 'body'
        })
        return showtoast;
    }

    NexoAPI.Sound	=	function( sound_index ){
        var SoundEnabled				=	'<?php echo @$Options[ store_prefix() . 'nexo_soundfx' ];
?>';
        if( ( SoundEnabled.length != 0 || SoundEnabled == 'enable' ) && SoundEnabled != 'disable' ) {
            var music = new buzz.sound( NexoSound + sound_index , {
                formats: [ "mp3" ]
            });
            music.play();
        }
    }

    NexoAPI.BindPrint();

$(".knob").knob({
        /*change : function (value) {
        //console.log("change : " + value);
        },
        release : function (value) {
        console.log("release : " + value);
        },
        cancel : function () {
        console.log("cancel : " + this.value);
        },*/
        draw: function () {

        // "tron" case
        if (this.$.data('skin') == 'tron') {

            var a = this.angle(this.cv)  // Angle
                , sa = this.startAngle          // Previous start angle
                , sat = this.startAngle         // Start angle
                , ea                            // Previous end angle
                , eat = sat + a                 // End angle
                , r = true;

            this.g.lineWidth = this.lineWidth;

            this.o.cursor
            && (sat = eat - 0.3)
            && (eat = eat + 0.3);

            if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value);
            this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3);
            this.g.beginPath();
            this.g.strokeStyle = this.previousColor;
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
            this.g.stroke();
            }

            this.g.beginPath();
            this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
            this.g.stroke();

            this.g.lineWidth = 2;
            this.g.beginPath();
            this.g.strokeStyle = this.o.fgColor;
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
            this.g.stroke();

            return false;
        }
        }
    });
});
</script>