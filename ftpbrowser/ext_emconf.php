<?php

########################################################################
# Extension Manager/Repository config file for ext: "ftpbrowser"
# 
# Auto generated 14-10-2005 21:32
# 
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'FTP Browser',
	'description' => 'A backend module to help you administrate your WebServer. Actually, it can browse your entire server, edit, create or delete files and folders, change permissions, upload and compress files, etc...',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'TYPO3_version' => '0.0.1-0.0.1',
	'PHP_version' => '0.0.1-0.0.1',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'private' => 0,
	'download_password' => '',
	'version' => '0.1.2',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:76:{s:21:"ext_conf_template.txt";s:4:"bdc1";s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"5b1d";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"3e66";s:22:"mod1/extModInclude.php";s:4:"6521";s:14:"mod1/index.php";s:4:"431d";s:18:"mod1/locallang.php";s:4:"3a5b";s:22:"mod1/locallang_mod.php";s:4:"8e18";s:19:"mod1/moduleicon.gif";s:4:"0690";s:31:"mod1/ftpbrowser-2.0/browser.php";s:4:"691b";s:29:"mod1/ftpbrowser-2.0/index.php";s:4:"2376";s:35:"mod1/ftpbrowser-2.0/conf/config.php";s:4:"cb3e";s:34:"mod1/ftpbrowser-2.0/conf/files.php";s:4:"cfc0";s:37:"mod1/ftpbrowser-2.0/css-js/scripts.js";s:4:"95de";s:37:"mod1/ftpbrowser-2.0/gfx/beta_flag.gif";s:4:"592f";s:41:"mod1/ftpbrowser-2.0/gfx/btn_configure.gif";s:4:"868d";s:46:"mod1/ftpbrowser-2.0/gfx/btn_configure_over.gif";s:4:"ff89";s:42:"mod1/ftpbrowser-2.0/gfx/btn_disconnect.gif";s:4:"7cca";s:47:"mod1/ftpbrowser-2.0/gfx/btn_disconnect_over.gif";s:4:"bb2f";s:34:"mod1/ftpbrowser-2.0/gfx/btn_go.gif";s:4:"7422";s:37:"mod1/ftpbrowser-2.0/gfx/col_atime.gif";s:4:"51fa";s:37:"mod1/ftpbrowser-2.0/gfx/col_ctime.gif";s:4:"a6aa";s:40:"mod1/ftpbrowser-2.0/gfx/col_filename.gif";s:4:"8c98";s:35:"mod1/ftpbrowser-2.0/gfx/col_gid.gif";s:4:"91ea";s:35:"mod1/ftpbrowser-2.0/gfx/col_mod.gif";s:4:"f68f";s:37:"mod1/ftpbrowser-2.0/gfx/col_perms.gif";s:4:"8007";s:36:"mod1/ftpbrowser-2.0/gfx/col_read.gif";s:4:"10b5";s:36:"mod1/ftpbrowser-2.0/gfx/col_size.gif";s:4:"52a2";s:36:"mod1/ftpbrowser-2.0/gfx/col_type.gif";s:4:"2858";s:35:"mod1/ftpbrowser-2.0/gfx/col_uid.gif";s:4:"22a5";s:37:"mod1/ftpbrowser-2.0/gfx/col_write.gif";s:4:"a39f";s:37:"mod1/ftpbrowser-2.0/gfx/separator.gif";s:4:"094a";s:34:"mod1/ftpbrowser-2.0/gfx/spacer.gif";s:4:"3254";s:40:"mod1/ftpbrowser-2.0/gfx/title_browse.gif";s:4:"cc6c";s:45:"mod1/ftpbrowser-2.0/gfx/topbar_background.gif";s:4:"bd67";s:39:"mod1/ftpbrowser-2.0/gfx/topbar_logo.gif";s:4:"8038";s:49:"mod1/ftpbrowser-2.0/gfx/icons/action_compress.gif";s:4:"44cb";s:53:"mod1/ftpbrowser-2.0/gfx/icons/action_compress_off.gif";s:4:"a835";s:54:"mod1/ftpbrowser-2.0/gfx/icons/action_compress_over.gif";s:4:"488e";s:45:"mod1/ftpbrowser-2.0/gfx/icons/action_edit.gif";s:4:"1456";s:49:"mod1/ftpbrowser-2.0/gfx/icons/action_edit_off.gif";s:4:"2e72";s:50:"mod1/ftpbrowser-2.0/gfx/icons/action_edit_over.gif";s:4:"e193";s:46:"mod1/ftpbrowser-2.0/gfx/icons/action_erase.gif";s:4:"5d66";s:50:"mod1/ftpbrowser-2.0/gfx/icons/action_erase_off.gif";s:4:"6aeb";s:51:"mod1/ftpbrowser-2.0/gfx/icons/action_erase_over.gif";s:4:"6b27";s:44:"mod1/ftpbrowser-2.0/gfx/icons/file_audio.gif";s:4:"e66a";s:49:"mod1/ftpbrowser-2.0/gfx/icons/file_audio_over.gif";s:4:"1051";s:43:"mod1/ftpbrowser-2.0/gfx/icons/file_code.gif";s:4:"95ca";s:48:"mod1/ftpbrowser-2.0/gfx/icons/file_code_over.gif";s:4:"45a9";s:47:"mod1/ftpbrowser-2.0/gfx/icons/file_compress.gif";s:4:"e364";s:52:"mod1/ftpbrowser-2.0/gfx/icons/file_compress_over.gif";s:4:"819e";s:48:"mod1/ftpbrowser-2.0/gfx/icons/file_directory.gif";s:4:"7aae";s:53:"mod1/ftpbrowser-2.0/gfx/icons/file_directory_over.gif";s:4:"3e76";s:46:"mod1/ftpbrowser-2.0/gfx/icons/file_picture.gif";s:4:"5b9b";s:51:"mod1/ftpbrowser-2.0/gfx/icons/file_picture_over.gif";s:4:"3925";s:43:"mod1/ftpbrowser-2.0/gfx/icons/file_text.gif";s:4:"d2e2";s:48:"mod1/ftpbrowser-2.0/gfx/icons/file_text_over.gif";s:4:"f046";s:46:"mod1/ftpbrowser-2.0/gfx/icons/file_unknown.gif";s:4:"ef67";s:51:"mod1/ftpbrowser-2.0/gfx/icons/file_unknown_over.gif";s:4:"eb9c";s:44:"mod1/ftpbrowser-2.0/gfx/icons/file_video.gif";s:4:"8dc9";s:49:"mod1/ftpbrowser-2.0/gfx/icons/file_video_over.gif";s:4:"5cd0";s:46:"mod1/ftpbrowser-2.0/include/browser_footer.php";s:4:"5505";s:46:"mod1/ftpbrowser-2.0/include/browser_header.php";s:4:"0c55";s:36:"mod1/ftpbrowser-2.0/include/edit.php";s:4:"61c4";s:38:"mod1/ftpbrowser-2.0/include/footer.php";s:4:"de48";s:38:"mod1/ftpbrowser-2.0/include/header.php";s:4:"cafd";s:37:"mod1/ftpbrowser-2.0/include/login.php";s:4:"5110";s:39:"mod1/ftpbrowser-2.0/include/options.php";s:4:"20b4";s:42:"mod1/ftpbrowser-2.0/include/properties.php";s:4:"7c16";s:31:"mod1/ftpbrowser-2.0/lang/EN.php";s:4:"d1fa";s:31:"mod1/ftpbrowser-2.0/lang/FR.php";s:4:"ffd3";s:42:"mod1/ftpbrowser-2.0/lib/ftplib_actions.php";s:4:"91e4";s:42:"mod1/ftpbrowser-2.0/lib/ftplib_browser.php";s:4:"b610";s:39:"mod1/ftpbrowser-2.0/lib/ftplib_core.php";s:4:"06f9";s:40:"mod1/ftpbrowser-2.0/lib/ftplib_utils.php";s:4:"2cb2";}',
);

?>