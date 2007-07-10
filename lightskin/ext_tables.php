<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	if (TYPO3_MODE=='BE') {
		$presetSkinImgs = is_array($TBE_STYLES['skinImg']) ? $TBE_STYLES['skinImg'] : array();
		
		// Get extension configuration from localconf.php
		$confArray = unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["lightskin"]);
		
		// Path to the extension
		$extRelPath = t3lib_extMgm::extRelPath($_EXTKEY);
		$extPath = t3lib_extMgm::extPath($_EXTKEY);
		
		/**
		 * Backend styles
		 */
		$TBE_STYLES = array(
			
			/**
			 * Colors
			 * 
			 * 0)	Default scheme
			 * 1)	Primary scheme
			 * 2)	Headers scheme
			 * 3)	Main scheme
			 * 4)	Extra scheme
			 * 5)	Special scheme
			 */
			'colorschemes' => Array (
				'0' => '#D9D8CC,' . $confArray['color_header0'] . ',#D9D8CC',
				'1' => '#D9D8CC,' . $confArray['color_header1'] . ',#D9D8CC',
				'2' => '#D9D8CC,' . $confArray['color_header2'] . ',#D9D8CC',
				'3' => '#D9D8CC,' . $confArray['color_header3'] . ',#D9D8CC',
				'4' => '#D9D8CC,' . $confArray['color_header4'] . ',#D9D8CC',
				'5' => '#D9D8CC,' . $confArray['color_header5'] . ',#D9D8CC',
			),
			
			/**
			 * Styles
			 */
			'styleschemes' => Array (
				'0' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
				'1' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
				'2' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
				'3' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
				'4' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
				'5' => array('all'=>'background-color: #E9E8DB; border: solid 1px #0A0808;', 'check'=>''),
			),
			
			/**
			 * Borders
			 */
			'borderschemes' => Array (
				'0' => array('border: solid 1px #120F10;',5),
				'1' => array('border: solid 1px #120F10;',5),
				'2' => array('border: solid 1px #120F10;',5),
				'3' => array('border: solid 1px #120F10;',5),
				'4' => array('border: solid 1px #120F10;',5),
				'5' => array('border: solid 1px #120F10;',5)
			),
			
			/**
			 * Main colors
			 */
			'mainColors' => Array (
				'bgColor' => '#E9E8DB',
				'bgColor2' => '#806A66',
				'bgColor3' => '#BBCCB3',
				'bgColor4' => '#BFBEB4',
				'bgColor5' => '#B38A62',
				'bgColor6' => '#B3CCCA',
				'hoverColor' => '#FFFFFF'
			),
		);
		
		// Frames dimensions
		$TBE_STYLES['dims']['leftMenuFrameW'] = ($confArray['dims_leftmenuframe']) ? $confArray['dims_leftmenuframe'] : false;
		$TBE_STYLES['dims']['topFrameH'] = ($confArray['dims_topframe']) ? $confArray['dims_topframe'] : false;
		$TBE_STYLES['dims']['shortcutFrameH'] = ($confArray['dims_shortcutframe']) ? $confArray['dims_shortcutframe'] : false;
		$TBE_STYLES['dims']['selMenuFrame'] = ($confArray['dims_selmenuframe']) ? $confArray['dims_selmenuframe'] : false;
		$TBE_STYLES['dims']['navFrameWidth'] = ($confArray['dims_navframe']) ? $confArray['dims_navframe'] : false;
		
		// Login pictures
		$TBE_STYLES['loginBoxImage_rotationFolder'] = ($confArray['file_rotationdir']) ? '../' . $confArray['file_rotationdir'] : $extRelPath . 'loginpics/';
		
		// Main stylesheet
		$TBE_STYLES['stylesheet'] = $extRelPath . 'stylesheet.css';
		
		// Additionnal stylesheet
		$TBE_STYLES['styleSheetFile_post'] = ($confArray['file_stylesheetpost']) ? '../' . $confArray['file_stylesheetpost'] : false;
		
		// Alternative icons
		$TBE_STYLES['skinImgAutoCfg'] = array(
			'absDir' => $extPath . 'icons/',
			'relDir' => $extRelPath . 'icons/',
		);
	
		// Manual settings
		$TBE_STYLES['skinImg'] = array_merge($presetSkinImgs, array (
			
			// Web
			'MOD:web/website.gif' => array($extRelPath . 'icons/module_web.gif','width="24" height="24"'),
			'MOD:web_layout/layout.gif' => array($extRelPath . 'icons/module_web_layout.gif','width="24" height="24"'),
			'MOD:web_view/view.gif' => array($extRelPath . 'icons/module_web_view.gif','width="24" height="24"'),
			'MOD:web_list/list.gif' => array($extRelPath . 'icons/module_web_list.gif','width="24" height="24"'),
			'MOD:web_info/info.gif' => array($extRelPath . 'icons/module_web_info.gif','width="24" height="24"'),
			'MOD:web_perm/perm.gif' => array($extRelPath . 'icons/module_web_perms.gif','width="24" height="24"'),
			'MOD:web_func/func.gif' => array($extRelPath . 'icons/module_web_func.gif','width="24" height="24"'),
			'MOD:web_ts/ts1.gif' => array($extRelPath . 'icons/module_web_ts.gif','width="24" height="24"'),
			'MOD:web_modules/modules.gif' => array($extRelPath . 'icons/module_web_modules.gif','width="24" height="24"'),
			
			// File
			'MOD:file/file.gif' => array($extRelPath . 'icons/module_file.gif','width="24" height="24"'),
			'MOD:file_list/list.gif' => array($extRelPath . 'icons/module_file_list.gif','width="24" height="24"'),
			'MOD:file_images/images.gif' => array($extRelPath . 'icons/module_file_images.gif','width="24" height="24"'),
			
			// Doc
			'MOD:doc/document.gif' => array($extRelPath . 'icons/module_doc.gif','width="24" height="24"'),
			
			// User
			'MOD:user/user.gif' => array($extRelPath . 'icons/module_user.gif','width="24" height="24"'),
			'MOD:user_task/task.gif' => array($extRelPath . 'icons/module_user_taskcenter.gif','width="24" height="24"'),
			'MOD:user_setup/setup.gif' => array($extRelPath . 'icons/module_user_setup.gif','width="24" height="24"'),
			
			// Tools
			'MOD:tools/tool.gif' => array($extRelPath . 'icons/module_tools.gif','width="24" height="24"'),
			'MOD:tools_beuser/beuser.gif' => array($extRelPath . 'icons/module_tools_user.gif','width="24" height="24"'),
			'MOD:tools_em/em.gif' => array($extRelPath . 'icons/module_tools_em.gif','width="24" height="24"'),
			'MOD:tools_dbint/db.gif' => array($extRelPath . 'icons/module_tools_dbint.gif','width="24" height="24"'),
			'MOD:tools_config/config.gif' => array($extRelPath . 'icons/module_tools_config.gif','width="24" height="24"'),
			'MOD:tools_install/install.gif' => array($extRelPath . 'icons/module_tools_install.gif','width="24" height="24"'),
			'MOD:tools_log/log.gif' => array($extRelPath . 'icons/module_tools_log.gif','width="24" height="24"'),
			'MOD:tools_txphpmyadmin/thirdparty_db.gif' => array($extRelPath . 'icons/module_tools_phpmyadmin.gif','width="24" height="24"'),
			'MOD:tools_isearch/isearch.gif' => array($extRelPath . 'icons/module_tools_isearch.gif','width="24" height="24"'),
			
			// Help
			'MOD:help/help.gif' => array($extRelPath . 'icons/module_help.gif','width="24" height="24"'),
			'MOD:help_about/info.gif' => array($extRelPath . 'icons/module_help_about.gif','width="24" height="24"'),
			'MOD:help_aboutmodules/aboutmodules.gif' => array($extRelPath . 'icons/module_help_aboutmodules.gif','width="24" height="24"'),
		));
		
		// TemplaVoila
		if (t3lib_extMgm::isloaded('templavoila')) {
			$TBE_STYLES['skinImg']['MOD:web_txtemplavoilaM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/templavoila/mod1/moduleicon.gif','width="24" height="24"');
			$TBE_STYLES['skinImg']['MOD:web_txtemplavoilaM2/moduleicon.gif'] = array($extRelPath . 'icons/ext/templavoila/mod2/moduleicon.gif','width="24" height="24"');
			$TBE_STYLES['skinImg']['MOD:tools_txtemplavoilaM3/moduleicon.gif'] = array($extRelPath . 'icons/ext/templavoila/mod3/moduleicon.gif','width="24" height="24"');
		}
		
		// CHC Forums
		if (t3lib_extMgm::isloaded('chc_forum')) {
			$TBE_STYLES['skinImg']['MOD:web_txchcforumM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/chc_forum/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// Visitor tracking system
		if (t3lib_extMgm::isloaded('de_phpot')) {
			$TBE_STYLES['skinImg']['MOD:tools_txdephpotM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/de_phpot/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// Extension development evaluator
		if (t3lib_extMgm::isloaded('extdeveval')) {
			$TBE_STYLES['skinImg']['MOD:tools_txextdevevalM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/extdeveval/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// DAM
		if (t3lib_extMgm::isloaded('dam')) {
			$TBE_STYLES['skinImg']['MOD:txdamM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/dam/mod_main/moduleicon.gif','width="24" height="24"');
			$TBE_STYLES['skinImg']['MOD:txdamM1_list/moduleicon.gif'] = array($extRelPath . 'icons/ext/dam/mod_list/moduleicon.gif','width="24" height="24"');
		}
		
		// DAM Index
		if (t3lib_extMgm::isloaded('dam_index')) {
			$TBE_STYLES['skinImg']['MOD:txdamM1_txdamindexM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/dam_index/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// DAM File
		if (t3lib_extMgm::isloaded('dam_file')) {
			$TBE_STYLES['skinImg']['MOD:txdamM1_txdamfileM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/dam_file/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// DAM Cat
		if (t3lib_extMgm::isloaded('dam_catedit')) {
			$TBE_STYLES['skinImg']['MOD:txdamM1_txdamcateditM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/dam_catedit/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// Content Uneraser
		if (t3lib_extMgm::isloaded('content_uneraser')) {
			$TBE_STYLES['skinImg']['MOD:tools_txcontentuneraserM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/content_uneraser/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// DB Sync
		if (t3lib_extMgm::isloaded('dbsync')) {
			$TBE_STYLES['skinImg']['MOD:tools_txdbsyncM1/moduleicon.gif'] = array($extRelPath . 'icons/ext/dbsync/mod1/moduleicon.gif','width="24" height="24"');
		}
		
		// Mail Form Plus
		if (t3lib_extMgm::isloaded('th_mailformplus')) {
			$TBE_STYLES['skinImg']['MOD:web_txthmailformplusM2/moduleicon.gif'] = array($extRelPath . 'icons/ext/th_mailformplus/mod1/moduleicon.gif','width="24" height="24"');
		}
	}
?>
