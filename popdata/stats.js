//import chart colors from CSS
var chartColors = {
	background: '',
	lines: ''
};

var videoPlayerColors = {
	frame: '',
	highlight: ''	
}

//function - import css property to javascript
function getStyleProp(elem, prop){
	    if(window.getComputedStyle)
	        return window.getComputedStyle(elem, null).getPropertyValue(prop);
	    else if(elem.currentStyle) return elem.currentStyle[prop]; //IE
	}

$(document).ready(function () {
	// Import chart colors from CSS
	//chart colors:
	chartColors.background = getStyleProp(document.getElementById('chart-colors'), 'background-color');
	//videoplayer colors:
	videoPlayerColors.frame = getStyleProp(document.getElementById('player-colors'), 'background-color');
	videoPlayerColors.highlight = getStyleProp(document.getElementById('player-colors'), 'border-color');

	// Color the graph button according to graph color scheme
 	$('#graph-btn').css('background-color', chartColors.background);

	// Hide video
	$('#video-frame').fadeTo(0, 0);

	// Fit to window /bodywidth
	//chart
	$('#smoothie-chart').animate({width: document.body.clientWidth}, {duration: 0, queue:false} ),
	$('#smoothie-chart').animate({height: '350px'}, {duration: 0, queue:false} )
	$(window).resize(function() {
		$('#smoothie-chart').animate({width: document.body.clientWidth}, {duration: 0, queue:false} )
	}),
	//video-mask
	$('#video-mask').animate({width: document.body.clientWidth}, {duration: 0, queue:false} ),
	$('#video-mask').animate({height: '350px'}, {duration: 0, queue:false} ),
	//video-frame
	$('#video-frame').animate({height: '350px'}, {duration: 0, queue:false} )
	$('#video-frame').animate({width: '622px'}, {duration: 0, queue:false} )

	// Clickfunctions
	var transDur = 200;	
	//graph button
	$('#graph-btn').click(function() {				
		//show/hide
		$('#smoothie-chart').fadeTo(transDur, 1);
		$('#video-frame').fadeTo(transDur, 0);
		$('#video-mask').show();

		//resize	
		$('#video-frame').animate({height: '350px'}, {duration: transDur, queue:false} )
		$('#video-frame').animate({width: '622px'}, {duration: transDur, queue:false} )
		$('#smoothie-chart').animate({height: '350px'}, {duration: transDur, queue:false} )
		$('#smoothie-chart').animate({width: document.body.clientWidth}, {duration: transDur, queue:false} )

		//change button color
		$('#graph-btn').css('background-color', chartColors.background);
		$('#video-btn').css('background-color', '');

	}),

	//hide graph
	hideGraph = function() { 		
		//show/hide
		$('#video-mask').hide();
	 	$('#smoothie-chart').fadeTo(transDur, 0);
		$('#video-frame').fadeTo(transDur, 1);

		//resize
		$('#video-frame').animate({height: '480px'}, {duration: transDur, queue:false} )
		$('#video-frame').animate({width: '853px'}, {duration: transDur, queue:false} )
		$('#smoothie-chart').animate({height: '480px'}, {duration: transDur, queue:false} )
		$('#smoothie-chart').animate({width: '853px'}, {duration: transDur, queue:false} )

		//change color of buttons
		$('#graph-btn').css('background-color', '');
		$('#video-btn').css('background-color', videoPlayerColors.frame);
	}

	//video button
	$('#video-btn').click(function() {
		hideGraph();
	}),

	//video-mask click
	$('#video-mask').click(function() { 
		hideGraph();
	})



//document.ready end
});

