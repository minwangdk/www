$(document).ready(function () {

	var chart = new SmoothieChart({
		millisPerPixel:13,
		maxValueScale:1.05,

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
	//target canvas
    canvas = document.getElementById('smoothie-chart');

	//for live data
	chart.streamTo(canvas, 1000);
	
    //declare lines
    series1 = new TimeSeries();
    series2 = new TimeSeries();
	
	chart.addTimeSeries(series1, {lineWidth:3.9,strokeStyle:'#40fe10',fillStyle: ''});		
	chart.addTimeSeries(series2, {lineWidth:3.9,strokeStyle:'#13AFFB',fillStyle: ''});

	// Dig out the chart's seriesOptions object for our TimeSeries
	// We should add a nicer API for this...
	var seriesOptions1;
	var seriesOptions2;

	for (var j = 0; j < chart.seriesSet.length; j++) {
	    if (chart.seriesSet[j].timeSeries === series1) {
	        seriesOptions1 = chart.seriesSet[j].options;	        
	    }	   
	    if (chart.seriesSet[j].timeSeries === series2) {
	        seriesOptions2 = chart.seriesSet[j].options;	        
	    }	   
	}				
	
	// Add some random data points
	var loadDate = new Date().getTime();		
	var i = 15;
	var loadDelay;
	setTimeout(function () {
		loadDelay = setInterval(function () {	
			if (i > -1) {
				series1.append(loadDate - i * 1016.67, Math.random().toFixed(2));
		  		series2.append(loadDate - i * 1016.67, Math.random().toFixed(2));	
		  		i--;
			} else {
				seriesOptions1.fillStyle = 'rgba(0,253,6,0.38)';	
	   			seriesOptions2.fillStyle = 'rgba(33, 107, 221, 0.38)';	
	   			clearInterval(loadDelay);
			}
		}, 50);	
	}, 300);
	

	
	
				
	// Add a random value to each line every second	
	var timer;			
	setTimeout(function() {
		timer = setInterval(function() {
		  series1.append(new Date().getTime(), Math.random().toFixed(2));
		  series2.append(new Date().getTime(), Math.random().toFixed(2));
		}, 1000);			
	}, 200);
	
	
	

	



// doc.ready end
});