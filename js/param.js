window.onload = function() {

	document.getElementById('sub_localisation').onclick = function(e) {
		e.preventDefault();
		let pays = document.getElementById('pays_loc').value;
		let ville = document.getElementById('ville_loc').value;
		let region = document.getElementById('region_loc').value;
		$.post('reload/loc.php', {pays:pays, ville:ville, region:region});
	}

	init_all();
	function init_all() {
		$.post('reload/param.php', {id:3}, function (data) {
			dat = data.split("|");
			document.getElementById('matchs_infos').innerHTML = dat[0];
			document.getElementById('bloque_infos').innerHTML = dat[1];
			var nop_match = document.querySelectorAll('.match_del');
			var nop_bloque = document.querySelectorAll('.bloq_del');
			var mess = document.querySelectorAll('.msg_env');
			add_event(nop_match, del_match);
			add_event(nop_bloque, del_bloque);
			add_event(mess, envoye_msg);
		});
	}

	function add_event(obj, fonction) {
		let i = 0;
		while (i < obj.length) {
			obj[i].addEventListener('click', fonction);
			i++;
		}
	}

	function remove_event(obj, fonction) {
		let i = 0;
		while (i < obj.length) {
			obj[i].removeEventListener('click', fonction);
			i++;
		}
	}

	function del_match(e) {
		remove_event(document.querySelectorAll('.match_del'), del_match);
		$.post('reload/suppr_match.php', {id:e.target.id}, function(data) {
			init_all();
		});
	}

	function del_bloque(e) {
		remove_event(document.querySelectorAll('.bloq_del'), del_match);
		$.post('reload/suppr_bloque.php', {id:e.target.id}, function(data) {
			init_all();
		});
	}

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

	function envoye_msg(e) {
		let id = e.target.id.replace("_msg_env_f", "");
		localStorage.setItem(id, id);
		init_msg();
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
		if (document.getElementById(id)) {
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