<?php
session_start();
$_SESSION['admin'] = 1;
?>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="icon" type="image/png" href="../photos/logo.png"/>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<a class="navbar-brand" href="#">Matcha</a>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="../index.php">Home</a>
				</li>
			</ul>
		</div>
	</nav>
	<div class="jumbotron" style="position: absolute; top: 30%; left: 5%; width: 90%;">
		<h1 class="display-3">Configuration de la base de donnee</h1>
		<p class="lead">Cet utilitaire permet de set la base de donnee avec une nouvelle ou une ancienne seed</p>
		<hr class="my-4">
		<p id="req"></p>
	</div>
</body>
<script type="text/javascript">
	get_infos(0);
	var timer;
	function get_infos(value) {
		$.post('panel.php', {val:value}, function(data) {
			clearInterval(timer);
			document.getElementById('req').innerHTML = data;
			if (document.getElementById('regenere')) {
				document.getElementById('regenere').onclick = function() {
					document.getElementById('req').innerHTML = "Creation de la nouvelle seed <font id='point'></font>\n";
					timer = setInterval(function(){ time_point(); }, 1000);
					get_infos(3);
				}
			}

			if (document.getElementById('prendre')) {
				document.getElementById('prendre').onclick = function() {
					document.getElementById('req').innerHTML = "Recuperation de lancienne seed <font id='point'></font>\n";
					timer = setInterval(function(){ time_point(); }, 1000);
					get_infos(2);
				}
			}

			dat = data.split('</head>\n');
			if (dat[1] == "data exec : success" || dat[1] == "data exec : fail") {
				setTimeout(function() {
					get_infos(0);
				}, 2000);
			}

			if (data == "data exec : success" || data == "data exec : fail") {
				setTimeout(function() {
					get_infos(0);
				}, 2000);
			}
		});
	}

	function time_point() {
		if (document.getElementById('point').innerText == '')
			document.getElementById('point').innerText = '.';
		else if (document.getElementById('point').innerText == '.')
			document.getElementById('point').innerText = '..';
		else if (document.getElementById('point').innerText == '..')
			document.getElementById('point').innerText = '...';
		else if (document.getElementById('point').innerText == '...')
			document.getElementById('point').innerText = '';
	}
</script>