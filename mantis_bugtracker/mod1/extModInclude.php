<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 Jean-David Gadina (info@macmade.net)
	 * All rights reserved
	 * 
	 * This script is part of the TYPO3 project. The TYPO3 project is 
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/
	
	// Module configuration
	unset($MCONF);
	$MCONF['extModInclude'] = 1;
	include ('../conf.php');
	
	// Check configuration
	if ($MCONF['mantisSubDir']) {
		
		// Init
		require_once($BACK_PATH . 'init.php');
		require_once($BACK_PATH . 'template.php');
		$BE_USER->modAccess($MCONF,1);
		
		// Get extension configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mantis_bugtracker']);
		
		// Database
		$g_hostname = TYPO3_db_host;
		$g_db_username = TYPO3_db_username;
		$g_db_password = TYPO3_db_password;
		$g_database_name = TYPO3_db;
		$g_db_type = 'mysql';
		$g_db_table_prefix = 'tx_mantisbugtracker_';
		
		// Stylesheet
		$g_css_include_file = $g_path . 'css/typo3.css';
		
		// Signup & password settings
		$g_allow_signup = OFF;
		$g_send_reset_password = OFF;
		$g_lost_password_feature = OFF;
		
		// Email settings
		$g_administrator_email = $extConf['email_admin'];
		$g_webmaster_email = $extConf['email_webmaster'];
		$g_from_email = $extConf['email_from'];
		$g_return_path_email = $extConf['email_return'];
		$g_enable_email_notification = ($extConf['email_notifications']) ? ON : OFF;
		$g_email_receive_own = ($extConf['email_own']) ? ON : OFF;
		
		// Colors
		$g_status_colors = array(
			'new' => $extConf['color_new'],
			'feedback' => $extConf['color_feedback'],
			'acknowledged' => $extConf['color_acknowledged'],
			'confirmed' => $extConf['color_confirmed'],
			'assigned' => $extConf['color_assigned'],
			'resolved' => $extConf['color_resolved'],
			'closed' => $extConf['color_closed'],
		);
		
		// Date & Time
		$g_short_date_format = $extConf['date_short'];
		$g_normal_date_format = $extConf['date_normal'];
		$g_complete_date_format = $extConf['date_full'];
		
		// Files
		$g_allow_file_upload = ($extConf['file_upload']) ? ON : OFF;
		$g_max_file_size = $extConf['file_max'];
		$g_allowed_files = $extConf['file_allowed'];
		$g_disallowed_files = $extConf['file_disallowed'];
		$g_document_files_prefix = $extConf['file_docprefix'];
		
		// Interface
		$g_enable_project_documentation = ($extConf['enable_doc']) ? ON : OFF;
		$g_show_footer_menu = ($extConf['show_footermenu']) ? ON : OFF;
		$g_show_project_menu_bar = ($extConf['show_projectmenubar']) ? ON : OFF;
		$g_show_assigned_names = ($extConf['show_assignednames']) ? ON : OFF;
		$g_show_priority_text = ($extConf['show_prioritytext']) ? ON : OFF;
		$g_show_bug_project_links = ($extConf['show_bugproject']) ? ON : OFF;
		$g_show_realname = ($extConf['show_realname']) ? ON : OFF;
		
		// HTML
		$g_html_make_links = ($extConf['html_makelinks']) ? ON : OFF;
		$g_html_valid_tags = $extConf['html_tags'];
		
		// Miscellaneous
		$g_page_title = $extConf['title_page'];
		
		// Language mapping
		$langMap = array(
			'ch' => 'chinese_simplified',
			'hk' => 'chinese_traditional',
			'hr' => 'croatian',
			'cz' => 'czech',
			'dk' => 'danish',
			'nl' => 'dutch',
			'default' => 'english',
			'et' => 'estonian',
			'fi' => 'finnish',
			'fr' => 'french',
			'de' => 'german',
			'hu' => 'hungarian',
			'it' => 'italian',
			'jp' => 'japanese_utf8',
			'kr' => 'korean',
			'lv' => 'latvian',
			'lt' => 'lithuanian',
			'no' => 'norwegian',
			'pl' => 'polish',
			'pt' => 'portuguese_standard',
			'ro' => 'romanian',
			'ru' => 'russian',
			'sk' => 'slovak',
			'si' => 'slovene',
			'es' => 'spanish',
			'se' => 'swedish',
			'tr' => 'turkish',
			'ua' => 'ukrainian'
		);
		
		// Lang key
		$langKey = (array_key_exists($LANG->lang,$langMap)) ? $LANG->lang : 'default';
		
		// Default language
		$g_default_language = $langMap[$langKey];
		
	} else {
		
		// Exit
		die('ERROR: Mantis is not configured!');
	}
?>
