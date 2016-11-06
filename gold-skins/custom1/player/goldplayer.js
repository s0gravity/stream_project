/*****************************************************
*  
*  Copyright 2015 Gold PLAYER. Author: ThemesGold. All Rights Reserved.
*  
*****************************************************/
(function ( $ ) {
 
    $.fn.goldplayer = function( options ) {
    
        // Gold PLAYER Default Options.
        var options = $.extend({
            // These are the defaults.
            src: "",
            poster: "",
            directory: sub_folder+"/gold-skins/default/player/"
        }, options );

        this.userAgent = navigator.userAgent;

        this.isiPad = this.userAgent.match(/iPad/i) != null;
        this.isiPhone = this.userAgent.match(/iPhone/i) != null;
        this.isAndroid = this.userAgent.match(/Android/i) != null;
        
        this.screenWidth = screen.width;
        this.screenHeight = screen.width;
        this.isPhone = this.screenHeight < 360;
        this.isTablet = this.screenHeight >= 360 && this.screenHeight <= 768;
        this.isDesktop = this.screenHeight > 768;

        if(this.isiPad || this.isAndroid)
        {
            return this.html('<video class="fixed_player" width="100%" height="100%" poster="'+options.poster+'" controls><source src="'+options.src+'" type="video/mp4" /></video>');
        }
        else
        {
            return this.html('<object class="fixed_player" type="application/x-shockwave-flash" name="player" data="'+options.directory+'GOLDPLAYER.swf" width="100%" height="100%" id="player" style="visibility: visible;"><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><param name="wmode" value="opaque"><param name="flashvars" value="src='+options.src+'&controlBarAutoHide=true&loop=false&skin='+options.directory+'GOLDPLAYER.xml"></object>');
        }
 
    };
 
}( jQuery ));