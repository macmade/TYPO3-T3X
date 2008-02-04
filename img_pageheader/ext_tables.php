<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	$tempColumns = Array (
		'tx_imgpageheader_picture' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:img_pageheader/locallang_db.php:pages.tx_imgpageheader_picture',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,jpg,jpeg,png',
				'max_size' => 500,
				'uploadfolder' => 'uploads/tx_imgpageheader',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	);
	
	t3lib_div::loadTCA('pages');
	t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('pages','tx_imgpageheader_picture;;;;1-1-1');
?>
