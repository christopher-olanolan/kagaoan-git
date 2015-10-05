if(window.jQuery) (function($){	
	jQuery.extend({
		notiFade : function (){
			$('#notification-container').fadeOut(200);
		},
		
		notiClose : function (){
			console.log(
	  			"MODULE: " + "notiClose" + "\n"
	  		);
    		
    		$( "#notification-container" ).click(function(event) {
    			$.notiFade();
			});
    	},
		
		noti : function (type, msg){
			console.log(
	  			"MODULE: " + "Notification" + "\n"
	  		);
			
			message = '<div id="notification-container" class="noti-' + type + '"><span id="notification-message"> ' + msg + ' </span></div>';
    		
    		$('#notification-container').remove();
    		$('#notification-inner').html(message);
    		$('#notification-container').addClass('animated bounce');
    		$.notiClose();
    		
    		$('#notification-container').delay(8000).fadeOut(200);
		}
	});
})(jQuery);