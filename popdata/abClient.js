

//topic for wamp, unique to poppage
var popTopic = '1'; //popid for gangnamstyle

//jquery handles for stats
var price = $('#price');
var dailyChange = $('#dailychange');
var percent = $('#percent');
var todaysRange = $('#todaysrange');
var popularity = $('#popularity');
var totalStocks = $('#totalstocks');

var popcorn = $('');
var ownedStocks = $('');

//buysell handles
var amount = 1; //bind to buysell amount at buysell buttons

//functions
function setPopStats(data) {
	if (data.popstats.price) {
		lastPrice = data.popstats.price[data.popstats.price.length - 1];
		price.text('$' + lastPrice[1]);
	}
	if (data.popstats.dailyChange) {
		dailyChange.text('$' + data.popstats.dailyChange);
	}
	if (data.popstats.percent) {
		percent.text(data.popstats.percent + '%');
	}
	if (data.popstats.todaysRangeLow && data.popstats.todaysRangeHigh) {
		todaysRange.text('$' + data.popstats.todaysRangeLow + '-' + data.popstats.todaysRangeHigh);
	}
	if (data.popstats.popularity) {
		popularity.text('$' + data.popstats.popularity);
	}
	if (data.popstats.totalStocks) {
		totalStocks.text(data.popstats.totalStocks);
	}
	return lastPrice; //run function and return array
}

function setUserStats(data) {
	if (data.userstats.popcorn) {
		popcorn.text(data.userstats.popcorn);
	}
	if (data.userstats.ownedpops[popTopic]) {
		ownedStocks.text(data.userstats.ownedpops[popTopic]);
	}
}

//autobahn
ab.connect(
	// The WebSocket URI of the WAMP server
	'ws://localhost:8080',

	// The onconnect handler
	function (session) {			
		sess = session; 
		console.log('connected!');

		// WAMP session established here ..
		sess.subscribe(popTopic, onEvent);
	  	// fetch latest stats 
	  	fetch();
	},

	// The onhangup handler
	function (code, reason, detail) {
	  // WAMP session closed here ..
	},

	// The session options
	{
	  'maxRetries': 60,
	  'retryDelay': 2000
	}
);



function onEvent(topicUri, event) {// Called when an event is triggered
	//new price points to add to smoothiechart, array [0] = timestamp [1] = value			
	var lastPrice = setPopStats(event);
}

function fetch() {
	sess.call(popTopic, 'fetch', 'param2jingjing')
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) {// callback runs async after result returns from ws server .
			//new price points to add to smoothiechart, array [0] = timestamp [1] = value			
	    	var lastPrice = setPopStats(result);  
	    	setUserStats(result);
	    	// console.log(result);
	    }
    );
}

function buy() {
	sess.call(popTopic, 'buysell', 'buy', amount)
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) { // callback runs async after result returns from ws server .
	     
			//sessionID return new popcorn and stocks amount
	    }
    );
}

function sell() {
	sess.call(popTopic, 'buysell', 'sell', amount)
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) {// callback runs async after result returns from ws server .
	      	//sessionID return new popcorn and stocks amount
	    }
    );
}
