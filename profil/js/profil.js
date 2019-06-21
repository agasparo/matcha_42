window.onload = function() {

	//******************************************************************************\\

	var prenom_in = document.getElementById('prenom_input');
	var prenom = document.getElementById('prenom');

	prenom_in.onkeyup = function() {
		prenom.innerHTML = "<h3>"+prenom_in.value+"</h3>";
	}

	var age_in = document.getElementById('age_input');
	var age = document.getElementById('age');

	age_in.onkeyup = function() {
		age.innerHTML = age_in.value;
	}

	var sexe_in = document.getElementById('sexe_input');
	var sexe = document.getElementById('sexe');

	sexe_in.onkeyup = function() {
		sexe.innerHTML = sexe_in.value;
	}

	var attirance_in = document.getElementById('attirance_input');
	var attirance = document.getElementById('attirance');

	attirance_in.onkeyup = function() {
		attirance.innerHTML = attirance_in.value;
	}

	var description_in = document.getElementById('description_input');
	var description = document.getElementById('des_p');

	description_in.onkeyup = function() {
		description.innerHTML = description_in.value;
	}

	var interets_in = document.getElementById('interets_input');
	var interets = document.getElementById('interets');

	interets_in.onkeyup = function() {
		interets.innerHTML = interets_in.value;
	}

	//*************************************************************************************\\

	var btn_maj = document.getElementById('valider');
	
	if (btn_maj) {
		btn_maj.onclick = function() {
			var mail = document.getElementById('mail_input').value;
			var mdp = document.getElementById('mdp_input').value;
			var interets = document.getElementById('interets_input').value;
			var description = document.getElementById('description_input').value;
			var attirance = document.getElementById('attirance_input').value;
			var sexe = document.getElementById('sexe_input').value;
			var age = document.getElementById('age_input').value;
			var prenom = document.getElementById('prenom_input').value;
			var nom = document.getElementById('nom_input').value;
			
			$.post("check_pro.php", {mails:mail, mdps:mdp, interets:interets, descriptions:description, attirances:attirance, sexes:sexe, ages:age, prenoms:prenom, noms:nom}, function(data) {
				if (data == "ok") {
					document.getElementById('home').click();
				} else {
					document.getElementById('err').innerHTML = data;
				}
			});	
		}
	}

	//*******************************************************************************************\\

	var get_img_vide = document.querySelectorAll('.img_manque');
	var i = 0;
	while (i < get_img_vide.length) {
		get_img_vide[i].addEventListener('click', push_img);
		i++;
	}

	if (document.getElementById('photos_bloc')) {
		document.getElementById('photos_bloc').onclick = function() {
			document.getElementById('file_add').click();
				document.getElementById('file_add').onchange = function(e) {
					var file = document.getElementById('file_add').files[0];
					if (file) {
					var imageType = /image.*/;
					if (file.type.match(imageType)) {
						var reader = new FileReader();

						reader.onload = function(e) {
							loadMime(file, function(res) {
								if (res != "unknown")
									insert_img(reader.result, 0);
								else
									document.getElementById('err').innerHTML = "Ficher non supporte";
							});
						}

						reader.readAsDataURL(file);	
					} else {
						document.getElementById('err').innerHTML = "Ficher non supporte";
					}
				}
			}
		}
	}

	function push_img(e) {
		var place = e.target.id;
		document.getElementById('file_add').click();
		document.getElementById('file_add').onchange = function(e) {
			var file = document.getElementById('file_add').files[0];
			if (file) {
				var imageType = /image.*/;
				if (file.type.match(imageType)) {
					var reader = new FileReader();

					reader.onload = function(e) {
						loadMime(file, function(res) {
							if (res != "unknown")
								insert_img(reader.result, place);
							else
								document.getElementById('err').innerHTML = "Ficher non supporte";
						});
					}

					reader.readAsDataURL(file);	
				} else {
					document.getElementById('err').innerHTML = "Ficher non supporte";
				}
			}
		}
	}

	function insert_img(link, place) {
		$.post("insert_img.php", {line:link, place:place}, function(data) {
			if (data == "")
				location.reload();
			else
				document.getElementById('err').innerHTML = data;
		});
	}

	function loadMime(file, callback) {
	    var mimes = [
	    {
	    	mime: 'image/jpeg',
	    	pattern: [0xFF, 0xD8, 0xFF],
	    	mask: [0xFF, 0xFF, 0xFF],
	    },
	    {
	    	mime: 'image/png',
	    	pattern: [0x89, 0x50, 0x4E, 0x47],
	    	mask: [0xFF, 0xFF, 0xFF, 0xFF],
	    }
        // https://mimesniff.spec.whatwg.org/#matching-an-image-type-pattern
        ];

        function check(bytes, mime) {
        	for (var i = 0, l = mime.mask.length; i < l; ++i) {
        		if ((bytes[i] & mime.mask[i]) - mime.pattern[i] !== 0) {
        			return false;
        		}
        	}
        	return true;
        }

	    var blob = file.slice(0, 4); //lire les 4 premier bytes

	    var reader = new FileReader();
	    reader.onloadend = function(e) {
	    	if (e.target.readyState === FileReader.DONE) {
	    		var bytes = new Uint8Array(e.target.result);

	    		for (var i=0, l = mimes.length; i<l; ++i) {
	    			if (check(bytes, mimes[i])) return callback(mimes[i].mime);
	    		}

	    		return callback("unknown");
	    	}
	    };
	    reader.readAsArrayBuffer(blob);
	}
	//*********************************************************************************************************\\

	var get_img = document.querySelectorAll('.img_match');
	var i = 0;
	while (i < get_img.length) {
		get_img[i].addEventListener('click', push_img);
		i++;
	}
}