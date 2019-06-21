window.onload = function() {
	var click_pro = 0;
	var is_finsih = 0;
	var save_elem;
	var myVar;
	var next = document.getElementById('next');
	var love = document.getElementById('love');
	var barre = document.getElementById('barre_next');
	var pro = document.getElementById('profile');
	var home = document.getElementById('home');
	var co_bar = document.getElementById('co_bar');
	var co_bar1 = document.getElementById('co_bar1');
	var notif_bar = document.getElementById('notif_bar');
	var close_form1 = document.getElementById('close_form1');
	var close_form = document.getElementById('close_form');
	var nom_utilisteur = document.getElementById('input_nom_utilisteur');
	var pass1 = document.getElementById('exampleInputpass');
	var push_ins_co = document.getElementById('ins_co');
	var timer_musiaue_h;
	var mini_menu = 0;
	var debu = 0;
	var is = 0;

	document.getElementById('small_btn').onclick = function() {
		$('#small_menu').slideToggle();
	}

	if (document.getElementById('reset_mdp')) {
		document.getElementById('reset_mdp').onclick = function() {
			$('#mdp_oublier').fadeToggle();
			document.getElementById('nom_util').onkeydown = function(e) {
				if (e.keyCode == 13) {
					$.post('mdp.php', {name:document.getElementById('nom_util').value, value:0}, function(data) {
						if (data != "Probleme avec votre requette") {
							let change = document.getElementById('nom_util');
							change.type = "email";
							change.value = "";
							change.placeholder = "Votre mail";
							document.getElementById('nom_util_for').innerText = "Mail : ";
							document.getElementById('nom_util').onkeydown = function(e) {
								if (e.keyCode == 13) {
									$.post('mdp.php', {name:document.getElementById('nom_util').value, value:data}, function(data) {
										if (data == "Probleme avec votre requette") {
											document.getElementById('alert_mdp_prob').innerText = data;
											document.getElementById('alert_mdp_prob').style.display = 'block';
											setTimeout(function(){
												document.getElementById('alert_mdp_prob').style.display = 'none';
											}, 3000);
										} else {
											$('#mdp_oublier').fadeToggle();
											document.getElementById('alert_mdp_ok').innerText = data;
											document.getElementById('alert_mdp_ok').style.display = 'block';
											setTimeout(function(){
												document.getElementById('alert_mdp_ok').style.display = 'none';
											}, 3000);
										}
										document.getElementById('nom_util_for').innerText = "Nom d'utilisateur : ";
										let change = document.getElementById('nom_util');
										change.type = "text";
										change.value = "";
										change.placeholder = "Votre Nom d'utilisateur";
									});
								}
							}
						} else {
							document.getElementById('alert_mdp_prob').innerText = data;
							document.getElementById('alert_mdp_prob').style.display = 'block';
							setTimeout(function(){
								document.getElementById('alert_mdp_prob').style.display = 'none';
							}, 3000);
						}
					});
				}
			}
		}
	}
	
	if (push_ins_co) {
		push_ins_co.onclick = function() {
			var nom_utilisteur_val = nom_utilisteur.value;
			var mdp_val = pass1.value;
			$.post("connexion.php", {nom_utilisteur:nom_utilisteur_val, mdps:mdp_val}, function(data) {
				if (data !== "") {
					var dat = data.split('<');
					dat[0] = dat[0].trim();
					if (dat[0] != "ok") {
						var error = document.getElementById('errors');
						error.innerHTML = data;
					} else {
						data = data.replace('ok', '');
						document.getElementById('momo').innerHTML = data;
						document.getElementById('momo').style.top = '10%';

						var close_form1 = document.getElementById('close_form1');
						var close_form = document.getElementById('close_form');
						var inscription = document.getElementById('ins');
						var mail = document.getElementById('exampleInputEmail1');
						var pass1 = document.getElementById('exampleInputpass');
						var pass = document.getElementById('exampleInputpass1');
						var nom = document.getElementById('exampleInputnom1');
						var prenom = document.getElementById('exampleInputPrenom1');
						var nom_utilisteur = document.getElementById('exampleInputnom_utilisateur');

						pass.value = mdp_val;
						pass1.value = mdp_val;

						inscription.onclick = function() {
							var mail_val = mail.value;
							var mdp_val = pass1.value;
							var mdp1_val = pass.value;
							var nom_val = nom.value;
							var prenom_val = prenom.value;
							var nom_utilisteur_val = nom_utilisteur.value;
							$.post("inscription.php", {mails:mail_val, mdps:mdp_val, mdps1:mdp1_val, noms:nom_val, prenoms:prenom_val, utilisateur:nom_utilisteur_val}, function(data) {
								if (data === "") {
									document.getElementById('alert_mail').style.display = 'block';
									setTimeout(function(){
										location.reload();
									}, 2000);
								} else {
									document.getElementById('errors').innerText = data;
								}
							});
						}

						prenom.onkeyup = function() {
							if (prenom.value.length > 2) {
								prenom.classList.remove('is-invalid');
								prenom.classList.add('is-valid');
							} else {
								prenom.classList.remove('is-valid');
								prenom.classList.add('is-invalid');
							}
						}

						nom.onkeyup = function() {
							if (nom.value.length > 1) {
								nom.classList.remove('is-invalid');
								nom.classList.add('is-valid');
							} else {
								nom.classList.remove('is-valid');
								nom.classList.add('is-invalid');
							}
						}

						pass1.onkeyup = function() {
							if (pass1.value.length > 4) {
								pass1.classList.remove('is-invalid');
								pass1.classList.add('is-valid');
							} else {
								pass1.classList.remove('is-valid');
								pass1.classList.add('is-invalid');
							}
						}

						pass.onkeyup = function() {
							if (pass.value.length > 4 && pass.value == pass1.value) {
								pass.classList.remove('is-invalid');
								pass.classList.add('is-valid');
							} else {
								pass.classList.remove('is-valid');
								pass.classList.add('is-invalid');
							}
						}

						mail.onkeyup = function() {
							if (validateEmail(mail.value)) {
								mail.classList.remove('is-invalid');
								mail.classList.add('is-valid');
							} else {
								mail.classList.remove('is-valid');
								mail.classList.add('is-invalid');
							}
						}

						nom_utilisteur.onkeyup = function() {
							if (nom_utilisteur.value.length > 5) {
								nom_utilisteur.classList.remove('is-invalid');
								nom_utilisteur.classList.add('is-valid');
							} else {
								nom_utilisteur.classList.remove('is-valid');
								nom_utilisteur.classList.add('is-invalid');
							}
						}

						close_form.onclick = function() {
							document.getElementById('co_menu').style.display = 'none';
						}

						close_form1.onclick = function() {
							document.getElementById('co_menu').style.display = 'none';
						}
					}
				} else {
					location.reload();
				}
			});
		}
	}

	pass1.onkeyup = function() {
		if (pass1.value.length > 4) {
			pass1.classList.remove('is-invalid');
			pass1.classList.add('is-valid');
		} else {
			pass1.classList.remove('is-valid');
			pass1.classList.add('is-invalid');
		}
	}

	nom_utilisteur.onkeyup = function() {
		if (nom_utilisteur.value.length > 5) {
			nom_utilisteur.classList.remove('is-invalid');
			nom_utilisteur.classList.add('is-valid');
		} else {
			nom_utilisteur.classList.remove('is-valid');
			nom_utilisteur.classList.add('is-invalid');
		}
	}

	love.onclick = function() {
		var width = barre.style.width;
		width = width.split('%');
		if (width[0] >= 10) {
			var dehors = document.getElementById('dehors');
			dehors.innerHTML = "";
			dehors.innerHTML = document.getElementById('in').innerHTML;
			dehors.style.backgroundColor = "white";
			dehors.style.zIndex = "200";
			$(dehors).show();
			var round = 0;
			var rotate = 0;
			var left = dehors.style.left;
			var opa = 0.8;
			left = left.split('%');
			function myTimer() {
				round++;
				dehors.style.left = left[0]+"%";
				dehors.style.opacity = opa;
				dehors.style.transform = "rotate("+rotate+"deg)";
				rotate = rotate + 15;
				opa = opa - 0.2;
				left[0] = parseInt(left[0]) + 10;
				if (round == 7) {
					clearInterval(myVar);
					dehors.style.display = 'none';
					dehors.style.left = "20%";
					dehors.style.transform = "rotate(-"+rotate+"deg)";
					dehors.innerHTML = "";
				}
			}
			barre.style.width = (width[0] - 10)+"%";
			if (width[0] <= 30)
				document.getElementById('alert_min_match').style.display = 'block';
			$.post("get_next_pro.php", {profil_min:click_pro, id:1, taille:barre.style.width, debu:debu}, function(data) {
				debu++;
				if (data == 'pas de sessions') {
					barre.style.width = (width[0] + 10)+"%";
					return (0);
				}
				myVar = setInterval(myTimer, 90);
				var dat = data.split('{');
				click_pro = dat[0];
				var inf_user = dat[1].split('$');
				document.getElementById('in').innerHTML = dat[2];
				setTimeout(function(){
					var pro = document.getElementById('profile');
					var home = document.getElementById('home');
					home.onclick = function() {
						pro.classList.remove("active");
						pro.classList.remove("show");
						home.classList.add("active");
						home.classList.add("show");
						var sexe;
						var att;
						if (inf_user[0] == "F")
							sexe = "Femme &#x2640;";
						if (inf_user[0] == "M")
							sexe = "Homme &#x2642;";
						if (inf_user[0] == "A")
							sexe = "Autre";
						if (inf_user[1] == "F")
							att = "Femme &#x2640;";
						if (inf_user[1] == "M")
							att = "Homme &#x2642;";
						if (inf_user[1] == "A")
							att = "Autre";
						if (inf_user[10] == "env_match") {
							document.getElementById('alert_match').style.display = "block";
							setTimeout(function(){
								document.getElementById('alert_match').style.display = "none";
							}, 3000);
						} else if (inf_user[10] == "deja_match") {
							width = barre.style.width.split("%");
							barre.style.width = (parseInt(width[0]) + 10)+"%";
							document.getElementById('alert_allready_match').style.display = "block";
							setTimeout(function(){
								document.getElementById('alert_allready_match').style.display = "none";
							}, 3000);
						} else if (inf_user[10] == "auto_m") {
							width = barre.style.width.split("%");
							barre.style.width = (parseInt(width[0]) + 10)+"%";
							document.getElementById('alert_allready_match_m').style.display = "block";
							setTimeout(function(){
								document.getElementById('alert_allready_match_m').style.display = "none";
							}, 3000);
						} else {
							width = barre.style.width.split("%");
							barre.style.width = (parseInt(width[0]) + 10)+"%";
							document.getElementById('alert_allready_match_b').style.display = "block";
							setTimeout(function(){
								document.getElementById('alert_allready_match_b').style.display = "none";
							}, 3000);
						}
						document.getElementById('myTabContent').innerHTML = "";
						document.getElementById('myTabContent').innerHTML = "<table class='table table-hover'><tr><td>Age : "+inf_user[3]+" ans</td><td>grade : "+inf_user[5]+"</td></tr><tr><td>Sexe : "+sexe+"</td><td>nb folowers : "+inf_user[6]+"</td></tr><tr><td>Attirance : "+att+"</td><td>"+inf_user[9]+"</td></tr><tr><td>jeux : </td><td>"+inf_user[8]+"</td><tr></table><div class='desciption'><blockquote class='blockquote text-center'> <p class='mb-0'>"+inf_user[2]+"</p><footer class='blockquote-footer'><cite title='Source Title'>"+inf_user[7]+"</cite></footer></blockquote></div>";
					}

					pro.onclick = function() {
						home.classList.remove("active");
						home.classList.remove("show");
						pro.classList.add("active");
						pro.classList.add("show");
						document.getElementById('myTabContent').innerHTML = "";
						var imgs = inf_user[4].split('|');
						var i = 0;
						var chaine = "";
						while (i < imgs.length) {
							if (imgs[i].length > 0) {
								let link = imgs[i].split('https://');
								if (link.length == 1)
									imgs[i] = 'photos/'+imgs[i];
								chaine = chaine+"<img src="+imgs[i]+" style='position: relative; height: 40%; width: auto; max-width: 40%; margin-top: 5%; margin-left: 5%;'/>";
							}
							i++;
						}
						document.getElementById('myTabContent').innerHTML = chaine;
					}

					home.click();
					set_signal();
					set_bloque();
				}, 700);
			});
		} else {
			document.getElementById('alert_min_match').style.display = 'none';
			document.getElementById('alert_fin_match').style.display = 'block';
		}
	}

	next.onclick = function() {
		var width = barre.style.width;
		width = width.split('%');
		if (width[0] >= 10) {
			var dehors = document.getElementById('dehors');
			dehors.innerHTML = "";
			dehors.innerHTML = document.getElementById('in').innerHTML;
			dehors.style.backgroundColor = "white";
			dehors.style.zIndex = "200";
			$(dehors).show();
			var round = 0;
			var rotate = 0;
			var left = dehors.style.left;
			var opa = 0.8;
			left = left.split('%');
			function myTimer() {
				round++;
				dehors.style.left = left[0]+"%";
				dehors.style.opacity = opa;
				dehors.style.transform = "rotate(-"+rotate+"deg)";
				rotate = rotate + 15;
				opa = opa - 0.2;
				left[0] = parseInt(left[0]) - 10;
				if (round == 7) {
					clearInterval(myVar);
					dehors.style.display = 'none';
					dehors.style.left = "20%";
					dehors.style.transform = "rotate("+rotate+"deg)";
					dehors.innerHTML = "";
				}
			}
			$.post("get_next_pro.php", {profil_min:click_pro, id:0, debu:debu}, function(data) {
				if (data == 'pas de sessions')
					return (0);
				debu++;
				myVar = setInterval(myTimer, 90);
				var dat = data.split('{');
				click_pro = dat[0];
				var inf_user = dat[1].split('$');
				document.getElementById('in').innerHTML = dat[2];
				setTimeout(function(){
					var pro = document.getElementById('profile');
					var home = document.getElementById('home');
					home.onclick = function() {
						pro.classList.remove("active");
						pro.classList.remove("show");
						home.classList.add("active");
						home.classList.add("show");
						var sexe;
						var att;
						if (inf_user[0] == "F")
							sexe = "Femme &#x2640;";
						if (inf_user[0] == "M")
							sexe = "Homme &#x2642;";
						if (inf_user[0] == "A")
							sexe = "Autre";
						if (inf_user[1] == "F")
							att = "Femme &#x2640;";
						if (inf_user[1] == "M")
							att = "Homme &#x2642;";
						if (inf_user[1] == "A")
							att = "Autre";
						document.getElementById('myTabContent').innerHTML = "";
						document.getElementById('myTabContent').innerHTML = "<table class='table table-hover'><tr><td>Age : "+inf_user[3]+" ans</td><td>grade : "+inf_user[5]+"</td></tr><tr><td>Sexe : "+sexe+"</td><td>nb folowers : "+inf_user[6]+"</td></tr><tr><td>Attirance : "+att+"</td><td>"+inf_user[9]+"</td></tr><tr><td>jeux : </td><td>"+inf_user[8]+"</td><tr></table><div class='desciption'><blockquote class='blockquote text-center'> <p class='mb-0'>"+inf_user[2]+"</p><footer class='blockquote-footer'><cite title='Source Title'>"+inf_user[7]+"</cite></footer></blockquote></div>";
					}

					pro.onclick = function() {
						home.classList.remove("active");
						home.classList.remove("show");
						pro.classList.add("active");
						pro.classList.add("show");
						document.getElementById('myTabContent').innerHTML = "";
						var imgs = inf_user[4].split('|');
						var i = 0;
						var chaine = "";
						while (i < imgs.length) {
							if (imgs[i].length > 0) {
								let link = imgs[i].split('https://');
								if (link.length == 1)
									imgs[i] = 'photos/'+imgs[i];
								chaine = chaine+"<img src="+imgs[i]+" style='position: relative; height: 40%; width: auto; max-width: 40%; margin-top: 5%; margin-left: 5%;'/>";
							}
							i++;
						}
						document.getElementById('myTabContent').innerHTML = chaine;
					}

					home.click();
					set_signal();
					set_bloque();
				}, 700);
			});
		} else {
			document.getElementById('alert_min_match').style.display = 'none';
			document.getElementById('alert_fin_match').style.display = 'block';
		}
	}

	if (document.getElementById('close_alert_match_fin')) {
		document.getElementById('close_alert_match_fin').onclick = function() {
			document.getElementById('alert_fin_match').style.display = 'none';	
		}
	}

	if (document.getElementById('close_alert_match_min')) {
		document.getElementById('close_alert_match_min').onclick = function() {
			document.getElementById('alert_min_match').style.display = 'none';	
		}
	}

	home.onclick = function() {
		pro.classList.remove("active");
		pro.classList.remove("show");
		home.classList.add("active");
		home.classList.add("show");
		if (is_finsih == 0) {
			is_finsih = 1;
			$.post("pro_c.php", function(data) {
				save_elem = data;
				var dat = data.split('{');
				if (!dat[1])
					return (0);
				var inf_user = dat[1].split('$');
				document.getElementById('in').innerHTML = dat[2];
				var sexe;
				var att;
				if (inf_user[0] == "F")
					sexe = "Femme &#x2640;";
				if (inf_user[0] == "M")
					sexe = "Homme &#x2642;";
				if (inf_user[0] == "A")
					sexe = "Autre";
				if (inf_user[1] == "F")
					att = "Femme &#x2640;";
				if (inf_user[1] == "M")
					att = "Homme &#x2642;";
				if (inf_user[1] == "A")
					att = "Autre";
				if (inf_user[6] == "")
					inf_user[6] = 'inconnu';
				setTimeout(function(){
					var pro = document.getElementById('profile');
					var home = document.getElementById('home');
					document.getElementById('myTabContent').innerHTML = "";
					document.getElementById('myTabContent').innerHTML = "<table class='table table-hover'><tr><td>Age : "+inf_user[3]+" ans</td><td>grade : "+inf_user[5]+"</td></tr><tr><td>Sexe : "+sexe+"</td><td>nb folowers : "+inf_user[6]+"</td></tr><tr><td>Attirance : "+att+"</td><td>"+inf_user[9]+"</td></tr><tr><td>jeux : </td><td>"+inf_user[8]+"</td><tr></table><div class='desciption'><blockquote class='blockquote text-center'> <p class='mb-0'>"+inf_user[2]+"</p><footer class='blockquote-footer'><cite title='Source Title'>"+inf_user[7]+"</cite></footer></blockquote></div>";
					pro.onclick = function() {
						home.classList.remove("active");
						home.classList.remove("show");
						pro.classList.add("active");
						pro.classList.add("show");
						document.getElementById('myTabContent').innerHTML = "";
						var imgs = inf_user[4].split('|');
						var i = 0;
						var chaine = "";
						while (i < imgs.length) {
							if (imgs[i].length > 0) {
								let link = imgs[i].split('https://');
								if (link.length == 1)
									imgs[i] = 'photos/'+imgs[i];
								chaine = chaine+"<img src="+imgs[i]+" style='position: relative; height: 40%; width: auto; max-width: 40%; margin-top: 5%; margin-left: 5%;'/>";
							}
							i++;
						}
						document.getElementById('myTabContent').innerHTML = chaine;
					}
					home.onclick = function() {
						pro.classList.remove("active");
						pro.classList.remove("show");
						home.classList.add("active");
						home.classList.add("show");
						var sexe;
						var att;
						if (inf_user[0] == "F")
							sexe = "Femme &#x2640;";
						if (inf_user[0] == "M")
							sexe = "Homme &#x2642;";
						if (inf_user[0] == "A")
							sexe = "Autre";
						if (inf_user[1] == "F")
							att = "Femme &#x2640;";
						if (inf_user[1] == "M")
							att = "Homme &#x2642;";
						if (inf_user[1] == "A")
							att = "Autre";
						document.getElementById('myTabContent').innerHTML = "";
						document.getElementById('myTabContent').innerHTML = "<table class='table table-hover'><tr><td>Age : "+inf_user[3]+" ans</td><td>grade : "+inf_user[5]+"</td></tr><tr><td>Sexe : "+sexe+"</td><td>nb folowers : "+inf_user[6]+"</td></tr><tr><td>Attirance : "+att+"</td><td>"+inf_user[9]+"</td></tr><tr><td>jeux : </td><td>"+inf_user[8]+"</td><tr></table><div class='desciption'><blockquote class='blockquote text-center'> <p class='mb-0'>"+inf_user[2]+"</p><footer class='blockquote-footer'><cite title='Source Title'>"+inf_user[7]+"</cite></footer></blockquote></div>";
					}
				}, 50);
			});
		} else {
			var data = save_elem;
			var dat = data.split('{');
			if (!dat[1])
				return (0);
			var inf_user = dat[1].split('$');
			document.getElementById('in').innerHTML = dat[2];
			var sexe;
			var att;
			if (inf_user[0] == "F")
				sexe = "Femme &#x2640;";
			if (inf_user[0] == "M")
				sexe = "Homme &#x2642;";
			if (inf_user[0] == "A")
				sexe = "Autre";
			if (inf_user[1] == "F")
				att = "Femme &#x2640;";
			if (inf_user[1] == "M")
				att = "Homme &#x2642;";
			if (inf_user[1] == "A")
				att = "Autre";
			setTimeout(function(){
				var pro = document.getElementById('profile');
				var home = document.getElementById('home');
				document.getElementById('myTabContent').innerHTML = "";
				document.getElementById('myTabContent').innerHTML = "<table class='table table-hover'><tr><td>Age : "+inf_user[3]+" ans</td><td>grade : "+inf_user[5]+"</td></tr><tr><td>Sexe : "+sexe+"</td><td>nb folowers : "+inf_user[6]+"</td></tr><tr><td>Attirance : "+att+"</td><td>"+inf_user[9]+"</td></tr><tr><td>jeux : </td><td>"+inf_user[8]+"</td><tr></table><div class='desciption'><blockquote class='blockquote text-center'> <p class='mb-0'>"+inf_user[2]+"</p><footer class='blockquote-footer'><cite title='Source Title'>"+inf_user[7]+"</cite></footer></blockquote></div>";
			}, 50);
		}
	}

	pro.onclick = function() {
		home.classList.remove("active");
		home.classList.remove("show");
		pro.classList.add("active");
		pro.classList.add("show");
		data = save_elem;
		var dat = data.split('{');
		click_pro = dat[0];
		if (!dat[1])
			return (0);
		var inf_user = dat[1].split('$');
		setTimeout(function(){
			var pro = document.getElementById('profile');
			var home = document.getElementById('home');
			document.getElementById('myTabContent').innerHTML = "";
			var imgs = inf_user[4].split('|');
			var i = 0;
			var chaine = "";
			while (i < imgs.length) {
				if (imgs[i].length > 0) {
					let link = imgs[i].split('https://');
					if (link.length == 1)
						imgs[i] = 'photos/'+imgs[i];
					chaine = chaine+"<img src="+imgs[i]+" style='position: relative; height: 40%; width: auto; max-width: 40%; margin-top: 5%; margin-left: 5%;'/>";
				}
				i++;
			}
			document.getElementById('myTabContent').innerHTML = chaine;
		}, 50);
	}

	home.click();

	if (notif_bar) {
		notif_bar.onclick = function() {
			document.getElementById('co_menu').style.display = 'none';
			document.getElementById('notif_menu').style.display = 'block';
		}
	}
	if (co_bar) {
		co_bar.onclick = function() {
			document.getElementById('notif_menu').style.display = 'none';
			document.getElementById('co_menu').style.display = 'block';
			$("#google_connection").load('google_connexion/index.php');
			$("#steam_connection").load('steam_connexion/index.php', function() {
				document.getElementById('steam_log').onclick = function(e) {
					e.preventDefault();
					window.location = 'steam_connexion/index.php?login';
				}
			});
			document.getElementById('insta_connection').onclick = function() {
				window.location = 'insta_connexion/index.php';
			}
		}   
	}

	if (co_bar1) {
		co_bar1.onclick = function() {
			$('#small_menu').slideToggle();
			document.getElementById('notif_menu').style.display = 'none';
			document.getElementById('co_menu').style.display = 'block';
			$("#google_connection").load('google_connexion/index.php');
			$("#steam_connection").load('steam_connexion/index.php', function() {
				document.getElementById('steam_log').onclick = function(e) {
					e.preventDefault();
					window.location = 'steam_connexion/index.php?login';
				}
			});
			document.getElementById('insta_connection').onclick = function() {
				window.location = 'insta_connexion/index.php';
			}
		}   
	}

	close_form.onclick = function() {
		document.getElementById('co_menu').style.display = 'none';
	}

	close_form1.onclick = function() {
		document.getElementById('co_menu').style.display = 'none';
	}

	function validateEmail(email) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(String(email).toLowerCase());
	}

	if (document.getElementById('close_open_lecteur')) {
		document.getElementById('close_open_lecteur').onclick = function() {
			let r = 0;
			let show;
			let max = 27;
			if (document.getElementById('musique_lecteur').style.right == '-27%' || document.getElementById('musique_lecteur').style.right == "") {
				show = null;
				r = -27;
				max = 0;
			} else {
				show = '-';
			}
			timer_musiaue_h = setInterval( function() { r = s_h(r, show, max); }, 25);
		}
	}

	function s_h(r, show, max) {
		if (r >= max)
			clearInterval(timer_musiaue_h);
		document.getElementById('musique_lecteur').style.right = show+r+'%';
		return (r + 1);
	}

	function set_signal() {
		var set_s = document.querySelector('.signal');
		set_s.onclick = function(e) {
			var id = e.target.id;
			if (is == 0) {
				is = 1;
				$.post("signal.php", {identifiant:id}, function(data) {
					if (data == "ok") {
						$('#alert_sign').slideToggle();
						setTimeout(function() {
							$('#alert_sign').slideToggle('slow');
							is = 0;
						}, 3000);
					}
				});
			}
		}
	}

	function set_bloque() {
		var set_b = document.querySelector('.bloq');
		set_b.onclick = function(e) {
			var id = e.target.id;
			if (is == 0) {
				is = 1;
				$.post("bloque.php", {identifiant:id}, function(data) {
					dat = data.split("|");
					document.getElementById('alert_bloq').innerHTML = dat[1];
					$('#alert_bloq').slideToggle();
					if (dat[1] == "L'utilisateur a bien ete debloque") {
						set_b.innerHTML = "Bloquer";
					} else {
						set_b.innerHTML = "Debloquer";
					}
					setTimeout(function() {
						$('#alert_bloq').slideToggle('slow');
						is = 0;
					}, 3000);
				});
			}
		}
	}
}