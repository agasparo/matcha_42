<?php

class musique {

	public function get_all_musique() {
		return (file_get_contents('http://192.168.99.100:41062/www/matcha/api/index.php?type=musique_all'));
	}

	public function show_all_musique($tab) {
		$tabs = json_decode($tab, true);
		$str = "";
		foreach ($tabs['infos']['musique'] as $key => $value) {
			$str .= 'id_video = '.$key.' title : '.$value.'<br>';
		}
		return ($str);
	}

	public function get_musique($id, $titre, $tab) {
		$tabs = json_decode($tab, true);
		if (empty($id) && empty($titre))
			return ('no data found');
		if (empty($titre)) {
			foreach ($tabs['infos']['musique'] as $key => $value) {
				if ($key == $id)
					return ($value);
			}
			return ('no data found for id = '.$id);
		}
		if (empty($id)) {
			foreach ($tabs['infos']['musique'] as $key => $value) {
				if ($value == $titre)
					return ($id);
			}
			return ('no data found for titre = '.$titre);
		}
	}
}

?>