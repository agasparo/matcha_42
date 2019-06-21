window.onload = function() {

	var resize_w = 0;
	var interval_msg;
	var notif_bar = document.getElementById('notif_bar');
	var mini_menu = 0;
	var time_chat_min;


	document.getElementById('small_btn').onclick = function() {
		$('#small_menu').slideToggle();
	}
	
	window.onresize = function() {
		if (document.body.clientWidth >= '992') {
			if (mini_menu == 0) {
				if (document.getElementById('small_menu').style.display == 'block') {
					mini_menu = 1;
					$('#small_menu').slideToggle(function() {
						mini_menu = 0;
					});
				}
			}
			document.getElementById('notif_show_mobil').style.display = 'none';
		} else {
			if (document.getElementById('notif_menu').style.display == 'block')
				document.getElementById('notif_menu').style.display = 'none';
			document.getElementById('notif_show_all').style.display = 'none';

		}
		$.post("message_create.php", {pass:1, type:2, storage:allStorage(), w_w:document.body.clientWidth}, function(data) {
			clearInterval(interval_msg);
			let elem = data.split('[');
			let i = 0;
			let list_id = [];
			let a = 0;
			document.getElementById('chat_mes').innerHTML = "";
			while (elem[i]) {
				if (elem[i].length > 0) {
					let get_e = elem[i].split("<div class='header_msg'>");
					let deb = get_e[0].replace("<div class='msg_speak'", "");
					let id = deb.split("id='");
					if (id[1]) {
						id = id[1].split("' name");
						list_id[a] = id[0];
						a++;
					}
					$("#chat_mes").append(elem[i]);
					document.getElementById(list_id[a - 1]+"_txt_msg").scrollTo(0, document.getElementById(list_id[a - 1]+"_txt_msg").scrollHeight);
				}
				i++;
			}
			refres_msg(list_id);
			get_text();
			close_chat();
			mini_chat();
		});
	}

	$.post("message_create.php", {pass:1, type:2, storage:allStorage(), w_w:document.body.clientWidth}, function(data) {
		clearInterval(interval_msg);
		let elem = data.split('[');
		let i = 0;
		let list_id = [];
		let a = 0;
		while (elem[i]) {
			if (elem[i].length > 0) {
				let get_e = elem[i].split("<div class='header_msg'>");
				let deb = get_e[0].replace("<div class='msg_speak'", "");
				let id = deb.split("id='");
				if (id[1]) {
					id = id[1].split("' name");
					list_id[a] = id[0];
					a++;
				}
				$("#chat_mes").append(elem[i]);
				document.getElementById(list_id[a - 1]+"_txt_msg").scrollTo(0, document.getElementById(list_id[a - 1]+"_txt_msg").scrollHeight);
			}
			i++;
		}
		refres_msg(list_id);
		get_text();
		close_chat();
		mini_chat();
	});


	function allStorage() {

		var values = [],
		keys = Object.keys(localStorage),
		i = keys.length;

		while ( i-- ) {
			values.push(localStorage.getItem(keys[i]));
		}

		return values;
	}


	var is = 0;
	document.getElementById('rechercher_des').onclick = function () {
		$('#more').slideToggle('slow');
	}
	document.getElementById('sub').onclick = function(e) {
		e.preventDefault();
		$('#more').slideToggle('slow');
		let prenom = document.getElementById('prenom_serach').value;
		let interets = document.getElementById('Interets_serach').value;
		let sexe = sexe_value();
		let ori = ori_value();
		let age_min = document.getElementById('age_search_min').value;
		let age_max = document.getElementById('age_search_max').value;
		let jeux = document.getElementById('Jeux_serach').value;
		let time_jeux = document.getElementById('time_game_min').value;
		let fl = document.getElementById('Nb_followers').value;
		let city = document.getElementById('ville_serach').value;
		let region = document.getElementById('region_serach').value;
		let pays = document.getElementById('pays_serach').value;
		let type_trie = document.getElementById('type_trie').value;
		let trie_val = document.getElementById('order_value').value+" "+document.getElementById('value').value;
		let order_trie = document.getElementById('order_trie').value;

		$.post("req.php", {prenom:prenom, sexe:sexe, interets:interets, attirance:ori, age_min:age_min, age_max:age_max, game:jeux, time_played:time_jeux, nb_followers:fl, city:city, region:region, pays:pays, type_trie:type_trie, trie_val:trie_val, order_trie:order_trie}, function(data) {
			document.getElementById('res').innerHTML = "";
			document.getElementById('res').innerHTML = data;
			get_pro();
		});
	}

	function sexe_value() {
		let sexe = document.querySelectorAll('.sex:checked');
		let i = 0;
		let final_sex = "";
		while ((i + 1) < sexe.length) {
			final_sex = final_sex+sexe[i].value+"|";
			i++;
		}
		final_sex = final_sex+sexe[i].value;
		return (final_sex);
	}

	function ori_value() {
		let ori = document.querySelectorAll('.ori:checked');
		let i = 0;
		let final_ori = "";
		while ((i + 1) < ori.length) {
			final_ori = final_ori+ori[i].value+"|";
			i++;
		}
		final_ori = final_ori+ori[i].value;
		return (final_ori);
	}

	document.getElementById('type_trie').onchange = function() {
		document.getElementById('value').disabled = false;
		document.getElementById('order_value').disabled = false
		document.getElementById('order_trie').disabled = false;
		if (document.getElementById('type_trie').value == 'age') {
			document.getElementById('value').disabled = true;
			document.getElementById('order_value').disabled = true;
		}
		if (document.getElementById('type_trie').value == 'none') {
			document.getElementById('value').disabled = true;
			document.getElementById('order_value').disabled = true;
			document.getElementById('order_trie').disabled = true;
		}
		if (document.getElementById('type_trie').value == 'localisation') {
			document.getElementById('value').placeholder = "nombre de km";
		}
		if (document.getElementById('type_trie').value == 'grade') {
			document.getElementById('value').placeholder = "nombre de matchs";
		}
	}

	function get_pro() {
		var get_pro = document.querySelectorAll('.show_pro_search');
		var i = 0;
		while (i < get_pro.length) {
			get_pro[i].addEventListener('click', show_profil);
			i++;
		}
	}

	function show_profil(e) {
		var profil = document.getElementById('profil');
		var id = e.target.id;
		$.post("get_user.php", {identifiant:id}, function(data) {
			profil.innerHTML = data;
			profil.style.display = 'block';
			profil.style.zIndex = 200;
			document.getElementById('close').onclick = function() {
				profil.style.display = 'none';
			}
			let pro = data.split("(vous)");
			if (pro.length == 1) {
				set_signal();
				set_bloque();
				set_match();
				set_msg();
			}
		});
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
						document.getElementById(id+"matchs").innerHTML = dat[0];
						if (dat[0] == "Envoyer un message")
							set_msg();
					} else {
						set_b.innerHTML = "Debloquer";
						document.getElementById(id+"matchs").innerHTML = '';
					}
					setTimeout(function() {
						$('#alert_bloq').slideToggle('slow');
						is = 0;
					}, 3000);
				});
			}
		}
	}

	function set_match() {
		var set_m = document.querySelector('.matchs');
		if (set_m) {
			set_m.onclick = function(e) {
				var id = e.target.id;
				var matches = id.match('matchs');
				if (is == 0 && matches) {
					is = 1;
					var e = 1;
					$.post("matchs.php", {identifiant:id, satus:document.getElementById(id).innerText}, function(data) {
						if (data == "Demande de match envoyee") {
							document.getElementById('alert_allready_match').innerHTML = data;
							document.getElementById('alert_allready_match').classList.remove("alert-primary"); 
							document.getElementById('alert_allready_match').classList.add("alert-success");
							$('#alert_allready_match').slideToggle();
						} else if(data == "Demande de match deja envoyee") {
							document.getElementById('alert_allready_match').innerHTML = data;
							document.getElementById('alert_allready_match').classList.remove("alert-success");
							document.getElementById('alert_allready_match').classList.add("alert-primary");
							$('#alert_allready_match').slideToggle();
						} else {
							set_m.innerText = "Envoyer un message";
							set_m.classList.remove("matchs");
							set_m.classList.add("msg");
							set_m.id = id.replace("matchs", "msg");
							set_m = document.querySelector('.matchs');
							set_msg();
							e = 0;
						}
						if (e == 1) {
							setTimeout(function() {
								$('#alert_allready_match').slideToggle('slow');
								is = 0;
							}, 3000);
						} else {
							is = 1;
						}
					});
				}
			}
		}
	}

	function set_msg() {
		var set_msg = document.querySelector('.msg');
		if (set_msg) {
			set_msg.onclick = function() {
				let id = parseInt(set_msg.id);
				$.post("message_create.php", {id:id, type:1}, function(data) {
					localStorage.setItem(data, data);
					$.post("message_create.php", {id:id, type:2, storage:allStorage(), w_w:document.body.clientWidth}, function(data) {
						clearInterval(interval_msg);
						let elem = data.split('[');
						document.getElementById('chat_mes').innerHTML = "";
						let i = 0;
						let list_id = [];
						let a = 0;
						while (elem[i]) {
							if (elem[i].length > 0) {
								let get_e = elem[i].split("<div class='header_msg'>");
								let deb = get_e[0].replace("<div class='msg_speak'", "");
								let id = deb.split("id='");
								id = id[1].split("' name");
								list_id[a] = id[0];
								a++;
								$("#chat_mes").append(elem[i]);
							}
							i++;
						}
						refres_msg(list_id);
						get_text();
						close_chat();
						mini_chat();
					});
				});
			}

			function allStorage() {

				var values = [],
				keys = Object.keys(localStorage),
				i = keys.length;

				while ( i-- ) {
					values.push(localStorage.getItem(keys[i]));
				}

				return values;
			}
		}
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

	function get_text() {
		let txtarea = document.querySelectorAll('.txt_msg');
		let i = 0;
		while (i < txtarea.length) {
			txtarea[i].addEventListener('keydown', env_msg);
			i++;
		}
	}

	function close_chat() {
		let chat = document.querySelectorAll('.close_chat_g');
		let i = 0;
		while (i < chat.length) {
			chat[i].addEventListener('click', close_local_chat);
			i++;
		}
	}

	function close_local_chat(e) {
		let id = e.target.id.replace("close_chat", "");
		localStorage.setItem(id, "0");
		$('#'+id).remove();
	}

	function env_msg(e) {
		if (e.keyCode == 13) {
			$.post("message.php", {id:e.target.id, msg:document.getElementById(e.target.id).value}, function(data) {
				document.getElementById(e.target.id).value = "";
				let es = e.target.id+"_msg";
				$("#"+es).append(data);
				document.getElementById(es).scrollTo(0, document.getElementById(es).scrollHeight);
			});
		}
	}

	function refres_msg(list_id) {
		var id_go = 0;
		if (list_id.length > 0)
			interval_msg = setInterval( function() { msg_show_time(list_id, list_id[id_go], id_go); }, 3000);
	}

	function msg_show_time(list_id, id, id_go) {
		clearInterval(interval_msg);

		var decodeHTML = function (html) {
			let txt = document.createElement('textarea');
			txt.innerHTML = html;
			return (txt.value);
		};

		$.post("reload/msg.php", {id:id}, function(data) {
			let content = data.split("{");
			let nb = content[0];
			data = content[1];
			if (document.getElementById(id)) {
				let an_nb = document.getElementById(id).getAttribute('name');
				if (an_nb !== nb) {
					let old = document.getElementById(id+"_txt_msg").innerHTML.replace(/'/g, '"');
					old = decodeHTML(old);
					data = data.replace(/'/g, '"');
					data = decodeHTML(data);
					let new_content = data.replace(old, "");
					document.getElementById(id).setAttribute('name', nb);
					let ids = id+"_txt_msg";
					$("#"+ids).append(new_content);
					document.getElementById(ids).scrollTo(0, document.getElementById(ids).scrollHeight);
				}
			}
			id_go++;
			if (list_id[id_go]) {
				msg_show_time(list_id, list_id[id_go], id_go);
			} else {
				id_go = 0;
				interval_msg = setInterval( function() { msg_show_time(list_id, list_id[id_go], id_go); }, 3000);
			}
		});
	}

	document.getElementById('notif_bar1').onclick = function() {
		$('#notif_php1').slideToggle();
		document.getElementById('notif_show_mobil').style.display = 'none';
	}

	document.getElementById('notif_php1').onclick = function(e) {
		let target = e.target.innerText.split(" :");
		$.post('notif_show.php', {type:target[0]}, function(data) {
			document.getElementById('notif_show_mobil').style.display = 'block';
			document.getElementById('notif_show_mobil').innerHTML = data;
			let get_msg_notif = document.querySelectorAll('.go_to_msg');
			let i = 0;
			while (i < get_msg_notif.length) {
				get_msg_notif[i].addEventListener('click', open_msg_notif);
				i++;
			}
		});
	}

	notif_bar.onclick = function() {
		if (document.getElementById('notif_menu').style.display == 'none' || document.getElementById('notif_menu').style.display == '') {
			document.getElementById('notif_menu').style.display = 'block';
		} else {
			document.getElementById('notif_menu').style.display = 'none';
			document.getElementById('notif_show_all').style.display = 'none';
		}
	}

	document.getElementById('notif_php').onclick = function(e) {
		document.getElementById('notif_show_all').style.display = 'block';
		let target = e.target.innerText.split("\n");
		$.post('notif_show.php', {type:target[0]}, function(data) {
			document.getElementById('notif_show_all').innerHTML = data;
			let get_msg_notif = document.querySelectorAll('.go_to_msg');
			let i = 0;
			while (i < get_msg_notif.length) {
				get_msg_notif[i].addEventListener('click', open_msg_notif);
				i++;
			}
		});
	}

	function open_msg_notif(e) {
		e.preventDefault();
		e.target.id = e.target.id.replace("noti_msg", "");
		localStorage.setItem(e.target.id, e.target.id);
		init_msg();
		$.post('update_msg_norif.php', {id:e.target.id}, function(data) {
			document.getElementById('notif_show_mobil').style.display = 'none';
			document.getElementById('notif_menu').style.display = 'none';
			document.getElementById('notif_show_all').style.display = 'none';
		});
	}

	function init_msg() {
		$.post("message_create.php", {pass:1, type:2, storage:allStorage(), w_w:document.body.clientWidth}, function(data) {
			clearInterval(interval_msg);
			let elem = data.split('[');
			let i = 0;
			let list_id = [];
			let a = 0;
			document.getElementById('chat_mes').innerHTML = "";
			while (elem[i]) {
				if (elem[i].length > 0) {
					let get_e = elem[i].split("<div class='header_msg'>");
					let deb = get_e[0].replace("<div class='msg_speak'", "");
					let id = deb.split("id='");
					if (id[1]) {
						id = id[1].split("' name");
						list_id[a] = id[0];
						a++;
					}
					$("#chat_mes").append(elem[i]);
					document.getElementById(list_id[a - 1]+"_txt_msg").scrollTo(0, document.getElementById(list_id[a - 1]+"_txt_msg").scrollHeight);
				}
				i++;
			}
			refres_msg(list_id);
			get_text();
			close_chat();
			mini_chat();
		});
	}

	function mini_chat() {
		let chat = document.getElementById('chat_mes').querySelectorAll('.header_msg');
		let i = 0;
		while (i < chat.length) {
			chat[i].addEventListener('click', chat_slidde);
			ini_mini_chat(chat[i].id);
			i++;
		}
	}

	function ini_mini_chat(id) {
		if (localStorage.getItem(id)) {
			document.getElementById(id).parentNode.style.bottom = localStorage.getItem(id);
		}
	}

	function chat_slidde(e) {
		let id = e.target.id;
		let elem = document.getElementById(id).parentNode;
		if (elem.style.bottom == "" || elem.style.bottom == "0%") {
			localStorage.setItem(id, '-36%');
			var i = 0;
			time_chat_min = setInterval( function() { i = slide(elem, i, '-', 36); }, 25);
		} else {
			localStorage.setItem(id, '0%');
			var i = 36;
			time_chat_min = setInterval( function() { i = slide(elem, i, '-', 0); }, 25);
		}
		document.getElementById(id).style.bottom = '0';
	}

	function slide(obj, bottom, sign, val) {
		if (bottom == val)
			clearInterval(time_chat_min);
		obj.style.bottom = sign+bottom+'%';
		if (val == 36)
			return (bottom + 2);
		return (bottom - 2);
	}

	setInterval(function() {load_notif(); }, 3000);
	function load_notif() {
		$("#notif_php").load('reload/notif.php');
	}

	setInterval(function() {load_notifs(); }, 3000);
	function load_notifs() {
		$("#notif_php1").load('reload/notif_mobil.php');
	}
}
