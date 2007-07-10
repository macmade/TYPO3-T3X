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
			'dir_covers' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists.dir_covers',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'jpg,jpeg',
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_femp3player',
					'show_thumbs' => 1,
					'size' => 5,
					'maxitems' => 50,
					'minitems' => 0,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, playlist;;;;3-3-3'),
			'1' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type;;;;3-3-3, dir_path;;;;3-3-3, dir_songs, dir_titles,dir_covers'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
