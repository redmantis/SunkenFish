(function($) {
	
	//plugin methods
	var methods = {
		
		init : function(options) { //object initialize                                                   
			return this.each(function() {
				//define element data
                                var seting=$.extend({}, $.fn.jqFloat.defaults, options);
				$(this).data('jSetting', seting);
				$(this).data('jDefined', true);
                                                       
			});                       
                   
		},
		update : function(options) {
			$(this).data('jSetting', $.extend({}, $.fn.jqFloat.defaults, options));
		},
                
                play: function () { 	//start floating
                    var setting =  $(this).data('jSetting');
                    var obj=this;
                    var adid = setInterval(function () {
                     floating(obj);
                    }, setting.speed);
                    $(this).data('jFloating', adid);
                },
		stop : function() {		//stop floating
			clearInterval($(this).data('jFloating'));
		}
	}
        
	var randvar = function makerand(max,min){
            var rand = Math.floor(Math.random()*(max-min+1)+min);
            return rand;
        }
        
	//private methods
	var floating = function(obj) {        
      
		var setting = $(obj).data('jSetting'); 
                var of= $(obj).offset(); 
                var le=of.left + setting.leftspeed;
                var te=of.top + setting.topspeed; 
                var outheight=$(obj).outerHeight(true);
                if(le>=setting.clientWidth-of.left || le<=0){setting.leftspeed=-1*setting.leftspeed}
                if(te>= setting.clientHeight-of.top-outheight || te<=0){setting.topspeed=-1*setting.topspeed}                
                of.left +=setting.leftspeed;
                of.top +=setting.topspeed;
                $(obj).offset(of);   
	}
	
	$.fn.jqFloat = function(method, options) {
		
		var element = $(this);	
//              console.log($(this).length);
           
		if ( methods[method] ) {			
			if(element.data('jDefined')) {
				//reset settings
				if (options && typeof options === 'object')
					methods.update.apply(this, Array.prototype.slice.call( arguments, 1 ));
			}
			else
				methods.init.apply(this, Array.prototype.slice.call( arguments, 1 ));
			
			methods[method].apply(this);
			
		} else if ( typeof method === 'object' || !method ) {
			if(element.data('jDefined')) {
                            console.log('jDefined');
				if(method)
					methods.update.apply(this, arguments);
			}		
			else {
//                             console.log(arguments);
				methods.init.apply(this, arguments);	}

			methods.play.apply(this);
		} else 
			$.error( 'Method ' +  method + ' does not exist!' );
		
		return this;
	}
	
	$.fn.jqFloat.defaults = {
		topspeed: 2,
		leftspeed: 2,
		speed: 50,
		clientWidth:1000,
                clientHeight:500
	}
	
})(jQuery);

//var clientWidth=$(document.body)[0].clientWidth;
//var clientHeight = $(document.body)[0].clientHeight;

var clientWidth=document.body.clientWidth;
var clientHeight = document.body.clientHeight;
$(".floatufo").each(function () {
    $(this).jqFloat({
        topspeed: 2,
        leftspeed: 2,
        speed: 50,
        clientWidth: clientWidth,
        clientHeight: clientHeight
    });

    $(this).on('mouseover', function () {
        $(this).jqFloat('stop');
    });
    $(this).on('mouseout', function () {
        $(this).jqFloat('play');
    });
});   


function floatclose(paretnid){
    $("#"+paretnid).hide();
}