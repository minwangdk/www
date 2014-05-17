<?php
require 'session_yourlogin.php';
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Gangnam Style</title>
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="popdata/stylesheets/style.css">
	<!-- this pop's vars-->
	<script src="popdata/GangnamStyle/GangnamStyle.js"></script>

	
	<!-- rickshaw-->
	<link type="text/css" rel="stylesheet" href="popdata/lib/chartfiles/graph.css">
	<link type="text/css" rel="stylesheet" href="popdata/lib/chartfiles/detail.css">
	<link type="text/css" rel="stylesheet" href="popdata/lib/chartfiles/legend.css">
	<link type="text/css" rel="stylesheet" href="popdata/lib/chartfiles/extensions.css">

	<script src="popdata/lib/chartfiles/d3.v3.js"></script>
	<script src="popdata/lib/chartfiles/rickshaw.js"></script>
    <!-- rickshaw end-->
   
</head>
<body>


	<header>   
		<h1 id="poptitle">Gangnam Style</h1>
	</header>

	<nav role='navigation'>
	  <ul>
	    <li><a href="#">Home</a></li>
	    <li><a href="#">About</a></li>
	    <li><a href="#">Clients</a></li>
	    <li><a href="#">Contact Us</a></li>
	  </ul>
	</nav>  



	<!-- Video -->
	<div id="popvideo">
		<iframe width="560" height="315" src="//www.youtube.com/embed/9bZkp7q19f0" frameborder="0"></iframe>
	</div>

	<!-- Rickshaw Graph container-->
	<div id="content">
		<div id="chart">
		</div>
	</div>

	<!--rickshaw script-->
	<script src="popdata/chart_cfg.js"></script>
	<p>Stockprice is: <span id="stockprice"></span></br></p>

	<!--buy script-->
	<script src="popdata/buysell.js"></script>

	<!-- Buttons -->
	<div class=buttons>
		<form action="">
		<button id="buy_button" type="button" onclick="order('buy')">Buy</button>
		<button id="sell_button" type="button" onclick="order('sell')">Sell</button>
		</form>
	</div>


	<div id="buysell_echo">	
	</div>




	
</body>
</html>
