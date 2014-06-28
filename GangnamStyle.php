
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Gangnam Style</title>
	<link rel="shortcut icon" href="favicon.ico"/>

	<script type='text/javascript'src='popdata/lib/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='global.js'></script>

	<!-- this pop's vars-->
	<script src="popdata/GangnamStyle/GangnamStyle.js"></script>

	<!-- Header -->
	<link type='text/css' rel='stylesheet' href='popdata/stylesheets/header.css'>
	<script type='text/javascript'src='popdata/header.js'></script>	

	<!-- Stats -->
	<link rel='stylesheet' type='text/css' href='popdata/stylesheets/stats.css'>		
	<script type='text/javascript' src='popdata/smoothie.js'></script>	
	<script type='text/javascript' src='popdata/video_chart.js'></script>	

	<!-- Video-chart -->
	<link rel='stylesheet' type='text/css' href='popdata/stylesheets/video_chart.css'>	
	<script type='text/javascript' src='popdata/smoothie.js'></script>	
	<script type='text/javascript' src='popdata/video_chart.js'></script>	
	

	

   
</head>
<body>
<!-- Header -->
<div class='mask hidden'></div>
<!--facebook login sdk-->
<script src='lib/fbloginSDK.js'></script>
<header>

	<a class='noSelect' id='logo' href='../index.php'><img src='popdata/popstock-logo2.png' alt='logo' ></a>
	

	<div class='whitebox'>
		<div>
			<p>|</p>
			<a id='signupbtn' href='../user_create_form.php'>Signup</a>	 	
			<a id='loginbtn' href='../user_login_form.php'>Login</a>
		</div>
	</div>
	

	
	
	

	<div class='logbox hidden'>
		<h2>LOGIN AND PLAY</h2>
		<form name='input' action='../sql_user_login.php' method='get'>
		
			<label class='loginlabel' for='username'>Username: </label>
			<input class='logininput' name='username' type='text' autofocus required>
		
		
		
			<label class='loginlabel' for='password'>Password: </label>
			<input class='logininput' name='password' type='password' required>
			
			
			
			<div class='checkbox'>
				<input id='rememberinput' type='checkbox' name='remember'> 
        		<label id='rememberlabel' for='rememberinput'>Stay logged in</label>
			</div>
	        
		    
			
			
			<p class='popuplinks'>
				<a id='popupsignup' href='../user_create_form.php'>Signup now</a>
				|
				<a id='forgotpw' href='.../forgotpw.php'>What's my password?</a>
			</p>

			<input id='loginsubmit' type='submit' value='Login'>

		</form>
		
		


	
		
		<div id='loginfooter'>
			<p>Or login with facebook</p>
			<!-- facebook login button -->
			<div class="fb-login-button" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="true">
			</div>
		</div>
		
			
		
	</div>
</header>	
	<!-- Header END -->
	
	<!-- Stats -->
	
<h1>Gangnam Style</h1>

<!-- Stats -->
<!-- <script type='text/javascript' src='popdata/ws_stats.js'></script> -->
<ul class='stats' id='small_stats'>
	<li id='price'>Current Price <span>$200.00</span></li>	
	<li id='change'>Daily Change	
		<container>
			<span id='changespan'>$26.31</span>				
			<span id='percent'>10.00%</span>					
		</container>	
		<div class='arrow-up'></div>	
		<div class='arrow-down hidden'></div>		
	</li>
	<li id='range'>Today's Range <span>$200.00 - $400.00</span></li>	
</ul>
<ul class='stats' id='big_stats'>
	<li id='worth'><div>Popularity </div><span>$20,000,000,000.00</span></li>
	<li id='total_stocks'><div>Total Stocks </div><span>200,000,000.00</span></li>
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
	<script type='text/javascript' src='popdata/smoothie_chart.js'></script>
	
	<iframe id='video-frame' width='853' height='480' src='//www.youtube.com/embed/9bZkp7q19f0?iv_load_policy=3&rel=0&showinfo=0&theme=light&nologo=1&autohide=1' frameborder='0' allowfullscreen></iframe>	
</div>

<i class="fa fa-spinner fa-spin"></i>
<i class="fa fa-circle-o-notch fa-spin"></i>
<i class="fa fa-refresh fa-spin"></i>
<i class="fa fa-cog fa-spin"></i>
<i class="fa fa-female fa-spin"></i>
<div style='color: #39D812;'>Connected</div>






<div id='chart-colors' class='chart-bg chart-lines'></div>
<div id='player-colors' class='player-bg player-highlight'></div>
	 
	 <!-- Video-chart END-->






















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
</div>
	




	
</body>
</html>

<?php
require 'session_yourlogin.php';
?>
