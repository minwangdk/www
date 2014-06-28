

//topic for wamp, unique to poppage
var popTopic = 'gangnamstyle';

//jquery handles for stats
var price = $('#price');
var dailyChange = $('#dailychange');
var percent = $('#percent');
var todaysRange = $('#todaysrange');
var popularity = $('#popularity');
var totalStocks = $('#totalstocks');

//buysell handles
var amount = 1; //bind to buysell amount at buysell buttons

//functions
function setStats(data) {
	if (data.price) {
		lastPrice = data.price[data.price.length - 1];
		price.text('$' + lastPrice[1]);
	}
	if (data.dailyChange) {
		dailyChange.text('$' + data.dailyChange);
	}
	if (data.percent) {
		percent.text(data.percent + '%');
	}
	if (data.todaysRangeLow && data.todaysRangeHigh) {
		todaysRange.text('$' + data.todaysRangeLow + '-' + data.todaysRangeHigh);
	}
	if (data.popularity) {
		popularity.text('$' + data.popularity);
	}
	if (data.totalStocks) {
		totalStocks.text(data.totalStocks);
	}
	return lastPrice; //run function and return array
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



function onEvent(topicUri, event) // Called when an event is triggered 
{
	//new price points to add to smoothiechart, array [0] = timestamp [1] = value			
	var lastPrice = setStats(event);
}

function fetch()
{
	sess.call('gangnamstyle', 'fetch', 'param2jingjing')
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) // callback runs async after result returns from ws server .
	    {  
			//new price points to add to smoothiechart, array [0] = timestamp [1] = value			
	    	var lastPrice = setStats(result);  
	    }
    );
}

function buy()
{
	sess.call('gangnamstyle', 'buysell', 'buy', amount)
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) // callback runs async after result returns from ws server .
	    {  
			//sessionID return new popcorn and stocks amount
	    }
    );
}

function sell()
{
	sess.call('gangnamstyle', 'buysell', 'sell', amount)
	.then( //RPC with params, topicid, function, more params...	 
	    function (result) // callback runs async after result returns from ws server .
	    {  
			//sessionID return new popcorn and stocks amount
	    }
    );
}
