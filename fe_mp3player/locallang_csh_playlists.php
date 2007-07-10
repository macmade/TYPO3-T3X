<?php
	/**
	 * Default TCA_DESCR for table 'tx_femp3player_playlists'
	 */
	$LOCAL_LANG = Array (
		'default' => Array (
			'.description' => 'Create a playlist for the Flash MP3 Player',
			'.details' => 'This record type will let you add as many MP3 files as you want, either by uploading them or by giving a file path. You\'ll then be able to link this playlist in the Flash MP3 Player plugin, to display and play your files. Please see the table description for more details.',
			'hidden.description' => 'Hide this playlist',
			'hidden.details' => 'If this is checked, the current playlist will be deactivated in the frontend. Note that the Flash player won\'t be displayed in that case.',
			'title.description' => 'The title of the playlist',
			'title.details' => 'This is the title of the playlist. It\'s juste used for the backend and it won\'t be displayed in the Flash player.',
			'type.image' => 'EXT:fe_mp3player/csh/type_0.jpg,EXT:fe_mp3player/csh/type_1.jpg,EXT:fe_mp3player/csh/type_2.jpg,EXT:fe_mp3player/csh/type_3.jpg',
			'type.description' => 'The type of the playlist',
			'type.details' => 'You can choose here the way you will add the MP3 files to the current playlist.
				
				<strong>1) Create each MP3 (default):</strong>
				With this option, you\'ll be able to add as many MP3 files you want, either by uploading a file from your computer, or by giving a specific path. Simply choose "NEW MP3" or "NEW PATH" in the select field and save to add a new MP3 section. Repeat this step as many time as needed.
				
				<strong>2) Get MP3s from a directory:</strong>
				With this option, you\'ll have to give the path to a directory (on the server) containing MP3 files. When it\'s done, you\'ll see below a list of the MP3 files found in that directory. Then just add the files you want by clicking on their names, and add the corresponding titles (if you want it) in the text field below.
				
				<strong>3) Get MP3s from remote PodCast:</strong>
				With this option, you just need to give a PodCast URL. The MP3 files will then be loaded in the player directly from that URL.
				
				<strong>4) Get MP3s from Typo3 PodCast extension (nbo_podcast):</strong>
				With this option, if the Typo3 PodCast extension is installed, you\'ll be able to directly select PodCasts created by this extension.',
			'playlist.description' => 'Add MP3 files to the playlist',
			'playlist.details' => 'In that section, you\'ll have the possibility to add as many MP3 as you want. Either choose "NEW MP3" to upload a file from your computer or "NEW PATH" to add the reference to a file which is already on the server. This step can be repeated as many time as needed. You can also give a specific title for each file, that will be displayed in the Flash player. If there is none, the filename will be used.',
			'playlist.image' => 'EXT:fe_mp3player/csh/mp3_upload.jpg,EXT:fe_mp3player/csh/mp3_path.jpg',
			'dir_path.description' => 'The path of the directory containing your MP3 files',
			'dir_path.details' => 'This is the path (relative) of the directory where your MP3 files are stored. When you have filled this field, the current playlist will be reloaded, so each MP3 file found in that directory will be displayed in the field below, giving you the possibility to add them to the playlist.',
			'dir_songs.description' => 'Add MP3 files to the playlist',
			'dir_songs.details' => 'This field let you select the MP3 files that you want to load on the playlist. Jusk click on a filename to add it.',
			'dir_titles.description' => 'List of MP3 titles for the MP3 player',
			'dir_titles.details' => 'In that field, you have the possibility to give a title for each selected MP3. Just separate each title by a carriage return. If there\'s no title for a file, the filename will be used.',
			'podcast_url.description' => 'Enter the URL of the PodCast to load',
			'podcast_url.details' => 'A PodCast is an XML file who contains informations and links to MP3 files. Be sure to enter a valid URL here.',
			'nbo_podcast.description' => 'Select the PodCast to load',
			'nbo_podcast.details' => 'The PodCasts in this selectorbox are taken directly from the Typo3 PodCast extension (nbo_podcast).',
			'nbo_podcast_false.description' => 'Extension not loaded',
			'nbo_podcast_false.details' => 'This field appears only if the Typo3 PodCast extension is not loaded. Before using this function, you must install it through the extension manager.',
		),
		'fr' => Array (
			'.description' => 'Création d\'une liste de lecture pour le lecteur MP3 Flash',
			'.details' => 'Ce type d\'enregistrement vous permettra d\'ajouter autant de fichiers MP3 que vous le désirez, soit en les téléchargeant, soit en donnant un chemin vers un fichier. Vous pourrez ensuite lier cette liste de lecture dans le lecteur MP3 Flash pour afficher et jouer vos morceaux. Veuillez consulter la déscription de la table pour de plus amples détails.',
			'hidden.description' => 'Cacher la liste de lecture',
			'hidden.details' => 'Si cette case est cochée, la liste de lecture courante sera désactivée en frontend. Notez qu\'à ce moment là, le lecteur Flash ne s\'affichera pas.',
			'title.description' => 'Le titre de la liste de lecture',
			'title.details' => 'Le titre de la liste de lecture est utilisé uniquement en backend, et n\'est pas affiché dans le lecteur Flash.',
			'type.image' => 'EXT:fe_mp3player/csh/type_0.jpg,EXT:fe_mp3player/csh/type_1.jpg,EXT:fe_mp3player/csh/type_2.jpg',
			'type.description' => 'Le type de la liste de lecture',
			'type.details' => 'Vous pouvez choisir ici comment ajouter des fichiers MP3 à la liste de lecture courrante.
				
				<strong>1) Créer chaque MP3 (par défaut):</strong>
				Avec cette option, vous pourrez ajouter autant de fichiers MP3 que vous le dŽsirez, soit en les téléchargeant, soit en donnant un chemin vers un fichier. Choisissez simplement "NEW MP3" ou "NEW PATH" dans le menu déroulant pour ajouter une nouvelle section. Répétez cette Žtape autant de fois que nécessaire.
				
				<strong>2) Obtenir les MP3s depuis un répertoire:</strong>
				Avec cette option, vous devrez donner le chemin vers un répertoire (sur le serveur) contenant des fichiers MP3. Une fois ceci fait, vous verrez la liste des fichiers MP3 contenus dans ce répertoire. Ajoutez simplement les fichiers en cliquant sur leur nom, et ajoutez le titre correspondant (si vous le désirez) dans le champ texte en dessous.
				
				<strong>3) Obtenir les MP3s depuis un PodCast distant:</strong>
				Avec cette option, vous avez simplement besoin de donner l\'URL d\'un PodCast. Les fichiers MP3 seront ensuite chargés dans le lecteur directement depuis cet URL.
				
				<strong>4) Obtenir les MP3s depuis l\'extension PodCast Typo3 (nbo_podcast):</strong>
				Avec cette option, si l\'extension PodCast Typo3 est installée, vous pourrez directement sélectionner les PodCasts crées avec cette extension.',
			'playlist.description' => 'Ajout de fichiers MP3 à la liste de lecture',
			'playlist.details' => 'Dans cette section, vous avez la possibilité d\'ajouter autant de MP3 que vous le voulez. Choisissez "NEW MP3" pour télécharger un fichier depuis votre ordinateur, ou "NEW PATH" pour donner la référence vers un fichier présent sur le serveur. Vous pouvez répéter cette étape autant de fois que vous le désirez. Vous pouvez également donner un titre pour chaque fichier, qui sera affiché dans le lecteur Flash. S\'il n\'y en a pas, c\'est le nom du fichier qui sera utilisé.',
			'playlist.image' => 'EXT:fe_mp3player/csh/mp3_upload.jpg,EXT:fe_mp3player/csh/mp3_path.jpg',
			'dir_path.description' => 'Le chemin vers le répertoire contenant les fichiers MP3',
			'dir_path.details' => 'Il s\'agit du chemin relatif vers le répertoire dans lequel vos fichiers MP3 sont stockés. Lorsque ce champ est rempli, la liste de lecture se rechargera pour afficher la liste des fichiers MP3 présents dans le répertoire spécifié et pour vous donner la possibilité de les ajouter à la liste de lecture.',
			'dir_songs.description' => 'Ajout de fichiers MP3 à la liste de lecture',
			'dir_songs.details' => 'Ce champ vous permet de sélectionner les fichiers MP3 que vous désirez ajouter à la liste de lecture. Cliquez simplement sur un nom de fichier pour l\'ajouter.',
			'dir_titles.description' => 'Liste des titres pour le lecteur MP3',
			'dir_titles.details' => 'Dans ce champ, vous avez la possibilité de donner un titre pour chaque MP3 sélectionné. Séparez simplement chaque titre par un retour chariot. S\'il n\'y a pas de titre pour un fichier, c\'est son nom qui sera utilisé.',
			'podcast_url.description' => 'L\'URL du PodCast à charger',
			'podcast_url.details' => 'Un PodCast est un fichier XML contenant des informations et des liens vers des fichiers MP3. Assurez vous d\'entrer ici un URL valide.',
			'nbo_podcast.description' => 'Sélectionner le PodCast à charger',
			'nbo_podcast.details' => 'Les PodCasts présents dans ce menu déroulant sont directements tirés de l\'extension PodCast Typo3.',
			'nbo_podcast_false.description' => 'Extension non chargée',
			'nbo_podcast_false.details' => 'Ce champ est visible uniquement si l\'extension PodCast Typo3 n\'est pas chargée. Avant d\'utiliser cette fonction, veuillez l\'installer avec le gestionnaire d\'extensions.',
		),
	);
?>
