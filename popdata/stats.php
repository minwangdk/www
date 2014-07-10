<?php 
require '../common.php';

	print_r($session->all());
?>

<!doctype html>
<html lang='en'>
<head>
	<meta charset='UTF-8' />
	<title>Stats</title>
	<link rel='stylesheet' type='text/css' href='stylesheets/stats.css'>	
	<script type='text/javascript' src='global.js'></script>
	<script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
	<script type='text/javascript' src='lib/smoothie.js'></script>	
	<script type='text/javascript' src='video_chart.js'></script>		
	<script type='text/javascript' src='lib/autobahn.min.js'></script>
	
	
	
</head>
<body>
<?php
echo $session->get('name');
?>
<h1>Gangnam Style</h1>

<!-- Stats -->
<!-- <script type='text/javascript' src='popdata/ws_stats.js'></script> -->
<ul class='stats' id='small-stats'>
	<li>Current Price <span id='price'>$200.00</span></li>	
	<li id='change'>Daily Change	
		<container>
			<span id='dailychange'>$26.31</span>				
			<span id='percent'>10.00%</span>					
		</container>	
		<div class='arrow-up'></div>	
		<div class='arrow-down hidden'></div>		
	</li>
	<li>Today's Range <span id='todaysrange'>$200.00 - $400.00</span></li>	
</ul>
<ul class='stats' id='big-stats'>
	<li><div>Popularity </div><span id='popularity'>$20,000,000,000.00</span></li>
	<li><div>Total Stocks </div><span id='totalstocks'>200,000,000.00</span></li>
</ul>

<!-- Stats END -->

<!-- Video-chart -->
<div class='toggle' id='graph-btn'>	
	Graph
</div>
<div class='toggle' id='video-btn'>	
	Video
</div>

<div class='wrapper'>
	<div id='video-mask'></div>
	<canvas id='smoothie-chart' width='1024' height='350'></canvas>
	<script type='text/javascript' src='smoothie_chart.js'></script>
	
	<iframe id='video-frame' width='853' height='480' src='//www.youtube.com/embed/9bZkp7q19f0?iv_load_policy=3&rel=0&showinfo=0&theme=light&nologo=1&autohide=1' frameborder='0' allowfullscreen></iframe>	
</div>

<i class='fa fa-spinner fa-spin'></i>
<i class='fa fa-circle-o-notch fa-spin'></i>
<i class='fa fa-refresh fa-spin'></i>
<i class='fa fa-cog fa-spin'></i>
<i class='fa fa-female fa-spin'></i>
<div style='color: #39D812;'>Connected</div>


<div id='chart-colors' class='chart-bg chart-lines'></div>
<div id='player-colors' class='player-bg player-highlight'></div>
<!-- Video-chart END -->

<!-- websocket -->
<script type='text/javascript' src='abClient.js'></script>
<!-- websocket END -->
</body>

</html>
