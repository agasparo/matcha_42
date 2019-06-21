<html>
<head>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/404.css">
	<title>Matcha - 404</title>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<a class="navbar-brand" href="#">Matcha</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="index.php">Home</a>
				</li>
			</ul>
		</div>
	</nav>
	<div id="error">
		<h1 id="error404"></h1>
		<p id="error4041"></p>
	</div>
</body>
<script type="text/javascript">
	let txt = [];
	txt[0] = "ERROR 404\n\n";
	txt[1] = "A problem has been detected and system has been shutdown to prevent damage to your computer.\n\n";
	txt[2] = "DRIVER_IRQL_NOT_LES_OR_EQUAL\n\n";
	txt[3] = "If this is the first time you've seen this stop error screen, restart your computer, if this screen appears again, follow these steps:\n\n";
	txt[4] = "Check to make sure any new hardware or software is properly installed. If this is a new installation, ask your hardware or software manufacturer for any system updates you might need.\n\n";
	txt[5] = "If problems continue, disable or remove any newly installed hardware or software. Disable BIOS memory options such as caching or shadowing. If you need to use Safe Mode to remove or disable components, restart your computer, press F8 to select Advanced Startup Options, and then select Safe Mode.\n\n";
	txt[6] = "Technical information:\n\n";
	txt[7] = "*** STOP: Ox000000D1 (0x0000000R, 0000000007, 0000000000, OxG74H2S74)\n\n";
	txt[8] = "*** strtt.sys - Address G74H2S74 base at G74H0000, DateStamp 4eh2534df\n\n"
	txt[9] = "Beginning dump of physical memory\n\n";
	txt[10] = "Physical memory dump complete.\n\n";
	txt[11] = "Contact your system administrator or technical support group for further assistance.\n\n";
	txt[12] = 'shutdown your computer in 3 seconds ...';
	panel(txt, 0, txt[0].split(""), 0);

	function panel(txt, i, mot, j) {
		setTimeout(function() {
			if (txt[i]) {
				if (mot[j]) {
					if (mot[j] == ' ') {
						document.getElementById('error4041').innerHTML += "&nbsp;";
						j++;
					}
					if (i == 0)
						document.getElementById('error404').innerText += mot[j];
					else
						document.getElementById('error4041').innerText += mot[j];
					j++;
					panel(txt, i, mot, j);
				} else {
					i++;
					if (txt[i])
						panel(txt, i, txt[i].split(""), 0);
					else
						finish();
				}
			}
		}, 10);
	}

	function finish() {
		setTimeout(function() {
			window.location = 'index.php';
		}, 3000);
	}

</script>
</html>