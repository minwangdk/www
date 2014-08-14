

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
var bitcoin = $('');
var dollars = $('');
var ownedStocks = $('');

//buysell handles
var amount = 1; //bind to buysell amount at buysell buttons

//functions
function setPopStats(data) {
	if (data.popstats.hasOwnProperty('dailyChange')) {
		dailyChange.text('$' + data.popstats.dailyChange);
	}
	if (data.popstats.hasOwnProperty('percent')) {
		percent.text(data.popstats.percent + '%');
	}
	if (data.popstats.hasOwnProperty('todaysRangeLow') && data.popstats.hasOwnProperty('todaysRangeHigh') ) {
		todaysRange.text('$' + data.popstats.todaysRangeLow + '-' + data.popstats.todaysRangeHigh);
	}
	if (data.popstats.hasOwnProperty('popularity')) {
		popularity.text('$' + data.popstats.popularity);
	}
	if (data.popstats.hasOwnProperty('totalStocks')) {
		totalStocks.text(data.popstats.totalStocks);
	}	
	if (data.popstats.hasOwnProperty('price')) {
		lastPrice = data.popstats.price[data.popstats.price.length - 1];
		price.text('$' + lastPrice);
	} else {
		return;
	}
	return lastPrice; //run function and return array
}

function setUserStats(data) {
	if (data.userstats.hasOwnProperty('currency') ) {
		popcorn.text(data.userstats.currency.popcorn);
		bitcoin.text(data.userstats.currency.bitcoin);
		dollars.text(data.userstats.currency.dollars);
	}
	if (data.userstats.hasOwnProperty('ownedpops') ) {
		//only displaying stocks for onpage pop (popTopic)
		//data.userstats.ownedpops is an array(popid => quantity)
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

function setStats(data) {
	if ( data.hasOwnProperty('popstats') )	{
		//new price points to add to smoothiechart, array [0] = timestamp [1] = value	
		var lastPrice = setPopStats(data);
	}
	if ( data.hasOwnProperty('userstats') )	{
		setUserStats(data);
	}
}

function onEvent(topic, event) {// Called when an event is triggered	
	setStats(event);
	console.log("onEvent():");
	console.log(event);
}

function fetch() {
	//RPC with params( topicid, function, more params...)	 
	sess.call(popTopic, 'fetch', 'param2jingjing')
	.then( 
	    function (result) {// callback runs async after result returns from ws server .
			setStats(result);
	    	console.log("fetch result:");
	    	console.log(result);
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
