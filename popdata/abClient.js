

//get poppage-data from html
var popHeader = document.querySelector('h1'),
              popData = popHeader.dataset;
//topic for wamp, unique to pop-page              
var popTopic = popData.poptopic; //popid for gangnamstyle = 1

//get cookie function
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}
//userTopic from cookie's user identifier
var userTopic = getCookie('UserIdentifier');

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
	if (data.popstats.price) {
		lastPrice = data.popstats.price[data.popstats.price.length - 1];
		price.text('$' + lastPrice[1]);
	} else {
		return;
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
		// WAMP session established here ..
		sess = session; 
		console.log('connected!');	

		//subscribe to pop
		sess.subscribe(popTopic, onEvent);	  	
	  	//subscribe to userTopic and get user data updates across multiple browser tabs
	  	sess.subscribe(userTopic, onEvent);

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



function onEvent(topic, event) {// Called when an event is triggered
	//new price points to add to smoothiechart, array [0] = timestamp [1] = value			
	var lastPrice = setPopStats(event);
	setUserStats(event);
}

function fetch() {
	//RPC with params( topicid, function, more params...)	 
	sess.call(popTopic, 'fetch', 'param2jingjing')
	.then( 
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
