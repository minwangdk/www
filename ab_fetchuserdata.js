ab.connect(
	// The WebSocket URI of the WAMP server
	'ws://localhost:8080',

	// The onconnect handler
	function (session) {			
		sess = session; 
		console.log('connected!');
		//no subscribe
	  	// fetch latest stats, without popstats
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
function fetch() {
	//RPC with params(topicid, function, more params...	)
	//call has no topic, so we dont get popstats
	sess.call('', 'fetch', 'param2jingjing')
	.then(  
	    function (result) {// callback runs async after result returns from ws server .
	    	console.log(result); //use result in displaying userstats on html using jquery
	    }
    );
}