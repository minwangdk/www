//import chart colors from CSS
//colors are in the head of doc.ready
var chartColors = {
	background: '',
	lines: ''
};

var videoPlayerColors = {
	frame: '',
	highlight: ''	
}

//function - import css property to javascript
var getStyleProp = function(elem, prop) {
	    if(window.getComputedStyle)
	        return window.getComputedStyle(elem, null).getPropertyValue(prop);
	    else if(elem.currentStyle) return elem.currentStyle[prop]; //IE
};

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
	$('#smoothie-chart').replaceWith('<canvas id="smoothie-chart" width="' + document.body.clientWidth + '" height="350"></canvas>');
	
	//video-mask
	$('#video-mask').animate({width: document.body.clientWidth}, {duration: 0, queue:false} );
	$('#video-mask').animate({height: '350px'}, {duration: 0, queue:false} );
	//video-frame
	$('#video-frame').animate({height: '350px'}, {duration: 0, queue:false} );
	$('#video-frame').animate({width: '622px'}, {duration: 0, queue:false} );

	//chart or video showing var			
	var videoIsVisible = false;			
	//resize function
	$(window).resize(function() {		
		$('#smoothie-chart').replaceWith('<canvas id="smoothie-chart" width="' + document.body.clientWidth + '" height="350"></canvas>');
		//call to smoothie_chart.js streamTo.canvas
		resizeCanvas();		
		if (videoIsVisible === true) {
			$('#smoothie-chart').fadeTo(0, 0);
		}
	});

	

	// Clickfunctions
	var transDur = 75;	
	//graph button
	$('#graph-btn').click(function() {				
		//show/hide
		$('#smoothie-chart').fadeTo(transDur, 1);
		$('#video-frame').fadeTo(transDur, 0);
		$('#video-mask').show();

		//resize	
		$('#video-frame').animate({height: '350px'}, {duration: transDur, queue:false} );
		$('#video-frame').animate({width: '622px'}, {duration: transDur, queue:false} );
		$('#smoothie-chart').animate({height: '350px'}, {duration: transDur, queue:false} );
		$('#smoothie-chart').animate({width: document.body.clientWidth}, {duration: transDur, queue:false} );

		//change button color
		$('#graph-btn').css('background-color', chartColors.background);
		$('#video-btn').css('background-color', '');
		videoIsVisible = false;
	});

	//hide graph
	hideGraph = function hideGraph() { 		
		//show/hide
		$('#video-mask').hide();
	 	$('#smoothie-chart').fadeTo(transDur, 0);
		$('#video-frame').fadeTo(transDur, 1);

		//resize;
		$('#video-frame').animate({height: '480px'}, {duration: transDur, queue:false} );
		$('#video-frame').animate({width: '853px'}, {duration: transDur, queue:false} );
		$('#smoothie-chart').animate({height: '480px'}, {duration: transDur, queue:false} );
		$('#smoothie-chart').animate({width: '853px'}, {duration: transDur, queue:false} );

		//change color of buttons
		$('#graph-btn').css('background-color', '');
		$('#video-btn').css('background-color', videoPlayerColors.frame);
		videoIsVisible = true;
	};

	//video button
	$('#video-btn').click(function() {
		hideGraph();
	});

	//video-mask click
	$('#video-mask').click(function() { 
		hideGraph();
	});



//document.ready end
});

