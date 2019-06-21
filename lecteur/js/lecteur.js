var tag = document.createElement('script');
tag.src = "https://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var styleElem = document.head.appendChild(document.createElement("style"));

document.getElementById('img_player').src = 'lecteur/img/base.jpg';

var player;
var currentTime = 0;
var tim_song = 0;
var autoplay_val = 0;
var id_video = "";
var name_song;
var timer_m;
var place = 0;
var init_s = 0;
var list_video = document.querySelectorAll('.musique');
var a = 0;
var artist_song;

$.post('lecteur/php/import_musique.php', {id:"gg", name:"gg"}, function(data){
	document.getElementById('load_musique').innerHTML = data;
	list_video = document.querySelectorAll('.musique');
});

if (localStorage.getItem("name_song"))
	name_song = localStorage.getItem("name_song");
if (localStorage.getItem("artist_song"))
	artist_song = localStorage.getItem("artist_song");
if (localStorage.getItem("currentTime"))
	currentTime = localStorage.getItem("currentTime");
if (localStorage.getItem("autoplay"))
	autoplay_val = localStorage.getItem("autoplay");
if (localStorage.getItem("time_song"))
	tim_song = localStorage.getItem("time_song");
if (localStorage.getItem("id_video"))
	id_video = localStorage.getItem("id_video");
if (localStorage.getItem("volume"))
	document.getElementById('sound').value = localStorage.getItem("volume");
styleElem.innerHTML = "#barre:before {width: "+(Math.round(currentTime) / tim_song * 100)+"%;}";
styleElem.innerHTML = styleElem.innerHTML+"#barre:after {left: "+(Math.round(currentTime) / tim_song * 100)+"%;}";

function onYouTubePlayerAPIReady() {

	document.getElementById('back_r').onclick = function() {
		document.getElementById('list_front').style.transform = 'rotateY(0deg)';
		document.getElementById('load_musique').style.transform = 'rotateY(180deg)';
	}

	add_event(list_video, choose_vid);

	if (a == 0) {
		player = new YT.Player('ytplayer', {
			height: '0',
			width: '0',
			playerVars: {'rel': 0, 'showinfo': 0},
			videoId: id_video,
			host: 'https://www.youtube.com',
			events: {
				'onReady' : onPlayerReady,
				'onStateChange': onPlayerStateChange,
				'onError': onPlayerError
			}
		});
		a = 1;
	}

	function onPlayerReady() {
		let val_sound = document.getElementById('sound').value;
		player.setVolume(val_sound * 10);
		if (id_video != "") {
			if (autoplay_val == 0) {
				player.cueVideoById(id_video, currentTime);
			}
			else {
				document.getElementById('play_m').classList.add("not");
				document.getElementById('pause').classList.remove("not");
				document.getElementById('play_m').classList.add("not");
				document.getElementById('pause').classList.remove("not");
				player.loadVideoById(id_video, currentTime);
				setTimeout(function (){
					tim_song = player.getDuration();
					add_localstorage(currentTime, autoplay_val, tim_song, id_video);
					timer_m = setInterval( function() { time_pass(tim_song); }, 1000);
				}, 2300);
			}
			document.getElementById('img_player').src = 'https://img.youtube.com/vi/'+id_video+'/0.jpg';
		}

		if (val_sound == 0) {
			document.getElementById('vol').classList.remove('fa-volume-up');
			document.getElementById('vol').classList.add('fa-volume-off');
		} else {
			document.getElementById('vol').classList.add('fa-volume-up');
			document.getElementById('vol').classList.remove('fa-volume-off');
		}
	}

	function onPlayerError(e) {
		let value_error;
		if (e.data == 150 || e.data == 101)
			value_error = "error[0] : video blockee";
		else if (e.data == 5)
			value_error = "error[1] : video non lisible";
		else if (e.data == 2)
			value_error = "error[2] : request fault";
		setTimeout(function (){
			document.getElementById('name_song').innerHTML = value_error;
		}, 2350);
	}

	document.getElementById('play_m').onclick = function() {
		if (id_video != "") {
			document.getElementById('play_m').classList.add("not");
			document.getElementById('pause').classList.remove("not");
			player.playVideo();
			autoplay_val = 1;
			setTimeout(function (){
				tim_song = player.getDuration();
				add_localstorage(currentTime, autoplay_val, tim_song, id_video);
				timer_m = setInterval( function() { time_pass(tim_song); }, 1000);
			}, 2300);
		}
	};
	
	document.getElementById('pause').onclick = function() {
		document.getElementById('play_m').classList.remove("not");
		document.getElementById('pause').classList.add("not");
		player.pauseVideo();
		currentTime = player.getCurrentTime();
		autoplay_val = 0;
		tim_song = player.getDuration();
		add_localstorage(currentTime, autoplay_val, tim_song, id_video);
		clearInterval(timer_m);
	};

	document.getElementById('next_m').onclick = function() {
		document.getElementById('play_m').classList.add("not");
			document.getElementById('pause').classList.remove("not");
		let number_max = document.querySelectorAll('.musique').length;
		if (document.getElementById(id_video)) {
			place = document.getElementById(id_video).getAttribute('name');
			if (place == (number_max - 1))
				place = 0;
			else
				place++;
			click_auto(place);
		}
	};

	document.getElementById('precedent').onclick = function() {
		document.getElementById('play_m').classList.add("not");
			document.getElementById('pause').classList.remove("not");
		let number_max = document.querySelectorAll('.musique').length;
		if (document.getElementById(id_video)) {
			place = document.getElementById(id_video).getAttribute('name');
			if (place == 0)
				place = number_max - 1;
			else
				place--;
			click_auto(place);
		}
	};

	document.getElementById('barre').onclick = function(e) {
		let parentOffset = $(this).offset();
		let relX = e.pageX - parentOffset.left;
		let size_x = $(this).width();
		let get_percent = relX / size_x * 100;
		let time_is = player.getCurrentTime();
		let time_var = player.getDuration();
		let percent = time_is / time_var * 100;
		let nv_time = get_percent * time_is / percent;
		player.loadVideoById(id_video, nv_time);
		add_localstorage(nv_time, 1, time_var, id_video);
		timer_m = setInterval( function() { time_pass(time_var); }, 1000);
	}

	function click_auto(number) {
		var i = 0;
		var e = document.querySelectorAll('.musique');
		while(i < e.length) {
			if (e[i].getAttribute('name') == number) {
				e[i].click();
				return (0);
			}
			i++;
		}
	}

	document.getElementById('sound').onchange = function() {
		let val_sound = document.getElementById('sound').value;
		player.setVolume(val_sound * 10);
		localStorage.setItem("volume", val_sound * 10);
		if (val_sound == 0) {
			document.getElementById('vol').classList.remove('fa-volume-up');
			document.getElementById('vol').classList.add('fa-volume-off');
		} else {
			document.getElementById('vol').classList.add('fa-volume-up');
			document.getElementById('vol').classList.remove('fa-volume-off');
		}
	}

	var set_add_song = 1;

	function onPlayerStateChange(event) {    
		if(event.data === 0)
			document.getElementById('next_m').click();
		if (player.getVideoData()) {
			if (set_add_song == 1) {
				set_add_song = 0;
				$.post('lecteur/php/add_song_matchs.php', {id_musique:player.getVideoData().video_id}, function(data) {
					setTimeout(function() {
						set_add_song = 1;
					}, 2000);
				});
			}
			name_song = player.getVideoData().title;
			artist_song = player.getVideoData().author;
			localStorage.setItem("name_song", player.getVideoData().title);
			localStorage.setItem("artist_song", player.getVideoData().author);
			document.getElementById('name_song').innerHTML = name_song;
			document.getElementById('artist').innerHTML = artist_song;
		}
	}

	function choose_vid(e) {
		document.getElementById('list_front').style.transform = 'rotateY(0deg)';
		document.getElementById('load_musique').style.transform = 'rotateY(180deg)';
		remove_event(list_video, choose_vid);
		id_video = e.target.id;
		player.loadVideoById(id_video);
		document.getElementById('play_m').classList.add("not");
		document.getElementById('pause').classList.remove("not");
		place = document.getElementById(id_video).getAttribute('name');
		setTimeout(function (){
			tim_song = player.getDuration();
			currentTime = player.getCurrentTime();
			autoplay_val = 1;
			add_localstorage(currentTime, autoplay_val, tim_song, id_video);
			add_event(list_video, choose_vid);
			document.getElementById('img_player').src = 'https://img.youtube.com/vi/'+id_video+'/0.jpg';
			timer_m = setInterval( function() { time_pass(tim_song); }, 1000);
		}, 2300);
	}

	window.onbeforeunload = function(e) {
		tim_song = player.getDuration();
		currentTime = player.getCurrentTime();
		add_localstorage(currentTime, autoplay_val, tim_song, id_video);
	}

	document.getElementById('list_musique').onclick = function() {
		if (document.getElementById('res_search').style.display == 'block') {
			$('#res_search').slideToggle(function() {
				document.getElementById('list_front').style.transform = 'rotateY(180deg)';
				document.getElementById('load_musique').style.transform = 'rotateY(0deg)';
			});
		}
		document.getElementById('list_front').style.transform = 'rotateY(180deg)';
		document.getElementById('load_musique').style.transform = 'rotateY(0deg)';
	}
}

document.getElementById('vol').onclick = function() {
	$('#sound').slideToggle();
}

function time_pass(time_max) {
	styleElem.innerHTML = "#barre:before {width: "+(Math.round(player.getCurrentTime()) / time_max * 100)+"%;}";
	styleElem.innerHTML = styleElem.innerHTML+"#barre:after {left: "+(Math.round(player.getCurrentTime()) / time_max * 100)+"%;}";
}

function add_event(obj, func) {
	var i = 0;
	while(i < obj.length) {
		obj[i].addEventListener('click', func);
		i++;
	}
}

function remove_event(obj, func) {
	var i = 0;
	while(i < obj.length) {
		obj[i].removeEventListener('click', func);
		i++;
	}
}

function add_localstorage(currentTime, autoplay_val, tim_song, id_video) {
	localStorage.setItem("currentTime", currentTime);
	localStorage.setItem("autoplay", autoplay_val);
	localStorage.setItem("time_song", tim_song);
	localStorage.setItem("id_video", id_video);
}

document.getElementById('search_vid').onkeydown = function(e) {
	if (e.keyCode == 13) {
		if (init_s != 1)
			return (0);
		gapi.client.load("youtube", 'v3', function() {
			var req = gapi.client.youtube.search.list({
				part : 'snippet',
				type : 'video',
				q : encodeURIComponent(document.getElementById('search_vid').value).replace(/%20/g, '+'),
				order: 'viewCount',
				maxResults: 20
			});
			req.execute(function(data) {
				var res = data.result;
				var st = "<table><tr><td id='close_musique_s'>fermer</td></tr>";
				$.each(res.items, function(index, item) {
					st = st+'<tr><td><img src="'+item.snippet.thumbnails.default.url.replace("default.jpg", "0.jpg")+'" style="height: 24%; max-height: 24%; width: 100%; min-height:24%;"/></td><td><p class="txt_res">'+item.snippet.title+'</p></td><td><p id="'+item.id.videoId+"|"+item.snippet.title+'" class="ajouter_mu">Ajouter<p></td><tr>';
				});
				st = st+"</table>";
				$("#search_vid").animate({width:'toggle'},350);
				document.getElementById('res_search').innerHTML = st;
				if (document.getElementById('res_search').style.display == '' || document.getElementById('res_search').style.display == 'none')
					$('#res_search').slideToggle();
				document.getElementById('close_musique_s').onclick = function() {
					$('#res_search').slideToggle();
				}
				add_event(document.querySelectorAll('.ajouter_mu'), add_musique);
			})
		});
	}
}

document.getElementById('serach_a_video').onclick = function() {
	$("#search_vid").animate({width:'toggle'},350);
	document.getElementById('search_vid').focus();
}

function add_musique(e) {
	let ids = e.target.id.split('|');
	$.post('lecteur/php/import_musique.php', {id:ids[0], name:ids[1]}, function(data){
		document.getElementById('load_musique').innerHTML = data;
		list_video = document.querySelectorAll('.musique');
		$('#res_search').slideToggle();
		onYouTubePlayerAPIReady();
	});
}

function init() {
	init_s = 1;
	gapi.client.setApiKey('api key');
}
