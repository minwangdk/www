var resizeCanvas;

$(document).ready(function () {

//delay load
setTimeout(function () {


	var chart = new SmoothieChart({
		millisPerPixel:25,
		maxValueScale:1.1,
		scaleSmoothing:0.15,

		grid:{		
			fillStyle: chartColors.background,
			strokeStyle:'#842266',
			millisPerLine:10000,
			verticalSections:4,
			borderVisible:false},
		labels:{
			fillStyle:'rgba(16,184,248,0.83)',
			fontSize:13,
			precision:0},
		timestampFormatter:SmoothieChart.timeFormatter
	});
	
	resizeCanvas = function () {
		//target canvas
	    canvas = document.getElementById('smoothie-chart');
		//for live data
		chart.streamTo(canvas, 1000);
	};
	resizeCanvas();
	
    //declare lines
    series1 = new TimeSeries();
    series2 = new TimeSeries();
	
	chart.addTimeSeries(series1, {lineWidth:3.9,strokeStyle:'#40fe10',fillStyle: ''});		
	chart.addTimeSeries(series2, {lineWidth:3.9,strokeStyle:'#13AFFB',fillStyle: ''});

	// Dig out the chart's seriesOptions object for our TimeSeries
	// We should add a nicer API for this...
				
	
	//draw lines function
	(function drawLines() {

		var stockPrice1 = 0;
		var stockPrice2 = 0;
		var timer;	
		var seriesOptions1;
		var seriesOptions2;

		//dig out options for nice color fill animation
		for (var j = 0; j < chart.seriesSet.length; j++) {
		    if (chart.seriesSet[j].timeSeries === series1) {
		        seriesOptions1 = chart.seriesSet[j].options;	        
		    }	   
		    if (chart.seriesSet[j].timeSeries === series2) {
		        seriesOptions2 = chart.seriesSet[j].options;	        
		    }	  
		    if (seriesOptions1 && seriesOptions2) {
		    	break;
		    }
		};	

		// Add some random data points
		var loadDate = new Date().getTime();		
		var i = 27;
		var k = 1;
		var interval = setInterval(drawRandomData, 33.33);
		function drawRandomData() {	
			if (i > -1) {
				stockPrice1 += (((Math.random() - 0.5) * 2) / Math.random() / 10) + ((Math.random() - 0.5) * 2);
				stockPrice2 += (((Math.random() - 0.5) * 2) / Math.random() / 10) + ((Math.random() - 0.5) * 2);
				series1.append(loadDate - i * 1000 + k * 33.33, stockPrice1);
		  		series2.append(loadDate - i * 1000 + k * 33.33, stockPrice2);	
		  		i--;
		  		k++;		  		
			} else { //set options for nice color fill animation
				seriesOptions1.fillStyle = 'rgba(0,253,6,0.38)';	
	   			seriesOptions2.fillStyle = 'rgba(33, 107, 221, 0.38)';
	   			clearInterval(interval);	   			
			}
		}
		
		//series1 and 2 use method .append(timestamp, value)


			
		// Add a random value to each line every second		
		setTimeout(function() {
			timer = setInterval(function() {
				stockPrice1 += (((Math.random() - 0.5) * 2) / Math.random() / 10) + ((Math.random() - 0.5) * 2);
				stockPrice2 += (((Math.random() - 0.5) * 2) / Math.random() / 10) + ((Math.random() - 0.5) * 2);
				series1.append(new Date().getTime(), stockPrice1);
				series2.append(new Date().getTime(), stockPrice2);
			}, 1000);			
		}, 633.24);

	}());

	

	
//timeout function end
}, 500);

//document.ready end
});