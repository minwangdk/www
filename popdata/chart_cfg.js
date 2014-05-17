// rickshaw script start -->

//popid for gangnam style

var tv = 250;

// instantiate our graph!
var graph = new Rickshaw.Graph( {
	element: document.getElementById("chart"),
	width: 450,
	height: 250,
	renderer: 'line',
	series: new Rickshaw.Series.FixedDuration([{ 
		name: 'one', color: 'gold' 
		}], undefined, {
		timeInterval: tv,
		maxDataPoints: 100,
		timeBase: new Date().getTime() / 1000
	}) 
} );

for (i=0; i<80; i++){
	data = {
		one: Math.random() * 50
	};
	graph.series.addData(data);
}

graph.render();
console.log(graph.series, "logged!");



// add some data every so often
var iv = setInterval( function() {
//Get stockprice on interval with ajax	
	function getStockprice() {
		if (window.XMLHttpRequest) {
		    // code for IE7+, Firefox, Chrome, Opera, Safari
		    xmlhttp=new XMLHttpRequest();
		} else { // code for IE6, IE5
		    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		  
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState===4 && xmlhttp.status===200) {
			   //update graph series with new data
		       var data = {one: +xmlhttp.responseText};
			   graph.series.addData(data);
			   graph.render();
			   document.getElementById("stockprice").innerHTML=xmlhttp.responseText;
			}
		}

		xmlhttp.open("GET","../../../sql_getstockprice.php?p="+popid,true);
		xmlhttp.send();

	}

	getStockprice();

}, tv );

//rickshaw script end