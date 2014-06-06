var chart = new SmoothieChart({
	millisPerPixel:13,
	maxValueScale:1,
	grid:{
		fillStyle:'#69145a',
		strokeStyle:'#842266',
		millisPerLine:10000,
		verticalSections:4,
		borderVisible:false},
	labels:{
		fillStyle:'rgba(16,184,248,0.83)',
		fontSize:13,
		precision:0},
	timestampFormatter:SmoothieChart.timeFormatter}),

    canvas = document.getElementById('smoothie-chart'),
    series1 = new TimeSeries();
    series2 = new TimeSeries();
	chart.addTimeSeries(series1, {lineWidth:3.9,strokeStyle:'#40fe10',fillStyle:'rgba(0,253,6,0.38)'});
	chart.streamTo(canvas, 1000);
	chart.addTimeSeries(series2, {lineWidth:3.9,strokeStyle:'#13AFFB',fillStyle:'rgba(33, 107, 221, 0.38)'});
	chart.streamTo(canvas, 1000);
		// Add a random value to each line every second
	setInterval(function() {
	  series1.append(new Date().getTime(), Math.random());
	  series2.append(new Date().getTime(), Math.random());
	}, 1000);

//fit to window /bodywidth
resizeCanvas = function() {	
	canvas.width = document.body.clientWidth;	
}
window.addEventListener('resize', resizeCanvas, false);
window.onload = resizeCanvas;