var newyearBar = (function(){
	
	var maxWidth = 1280;
	
	function start(){
		var bar = $('.newyear_bar');
		
		if(!bar.length) return;
		
		$(window).resize(function(){
			var winWidth = $(window).width();
			
			if(winWidth<maxWidth)
				bar.addClass('hide');
			else if (bar.hasClass('hide'))
				bar.removeClass('hide');
		}).resize();
		
	}
	
	return {
		start : start
	}
	
})();

$(function(){
	newyearBar.start();
})