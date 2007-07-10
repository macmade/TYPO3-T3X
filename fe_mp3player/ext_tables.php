<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include TCA helpers
	if (TYPO3_MODE=='BE') {
		include_once(t3lib_extMgm::extPath('fe_mp3player') . 'class.tx_femp3player_handlemp3files.php');
		include_once(t3lib_extMgm::extPath('fe_mp3player') . 'class.tx_femp3player_handlepodcastfiles.php');
	}
	
	/**
	 * Playlists TCA
	 */
	$TCA['tx_femp3player_playlists'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:fe_mp3player/locallang_db.php:tx_femp3player_playlists',
			
			// Label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Type
			'type' => 'type',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// List of fields that require an update
			'requestUpdate' => 'dir_path',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_femp3player_playlists.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:fe_mp3player/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Adding context sensitive help (CSH)
	t3lib_extMgm::addLLrefForTCAdescr('tx_femp3player_playlists','EXT:fe_mp3player/locallang_csh_playlists.php');
	
	// Wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_femp3player_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_femp3player_pi1_wizicon.php';
	}
?>
