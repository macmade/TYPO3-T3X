<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Playlists TCA
	 */
	$TCA['tx_femp3player_playlists'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_femp3player_playlists']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_femp3player_playlists']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'type' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.type',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.type.I.0', '0'),
						Array('LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.type.I.1', '1'),
						Array('LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.type.I.2', '2'),
						Array('LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.type.I.3', '3'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'playlist' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.playlist',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:fe_mp3player/flexform_ds.xml',
					),
				)
			),
			'dir_path' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.dir_path',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'nospace,required',
				)
			),
			'dir_songs' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.dir_songs',
				'config' => Array (
					'type' => 'select',
					'items' => Array (),
					'itemsProcFunc' => 'tx_femp3player_handleMP3Files->main',
					'size' => 10,
					'maxitems' => 50,
				)
			),
			'dir_titles' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.dir_titles',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '10',
				)
			),
			'podcast_url' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.podcast_url',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'nospace,required',
				)
			),
			'nbo_podcast' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.nbo_podcast',
				'displayCond' => 'EXT:nbo_podcast:LOADED:true',
				'config' => Array (
					'type' => 'select',
					'items' => Array (),
					'itemsProcFunc' => 'tx_femp3player_handlePodCastFiles->main',
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'nbo_podcast_false' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.nbo_podcast_false',
				'displayCond' => 'EXT:nbo_podcast:LOADED:false',
				'config' => Array (
					'type' => 'user',
					'userFunc' => 'tx_femp3player_handlePodCastFiles->warning',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, playlist;;;;3-3-3'),
			'1' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, dir_path;;;;3-3-3, dir_songs, dir_titles'),
			'2' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, podcast_url;;;;3-3-3'),
			'3' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, nbo_podcast;;;;3-3-3,nbo_podcast_false;;;;3-3-3'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
