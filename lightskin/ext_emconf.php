<?php

########################################################################
# Extension Manager/Repository config file for ext: "lightskin"
#
# Auto generated 01-02-2006 11:11
#
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'Light Skin For Typo3',
	'description' => '[PREVIEW VERSION - Not finished yet, and still a lot of work to do. Please contact me for suggestions / comments] A smooth and light skin for Typo3 3.7 and later. This is not an adaptation of an existing skin, but a complete redesign of the Typo3 backend, from scratch, including icons and stylesheets. It also includes several options to let you customize the skin. Enjoy...',
	'category' => '',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'TYPO3_version' => '0.0.2-0.0.2',
	'PHP_version' => '0.0.2-0.0.2',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'private' => 0,
	'download_password' => '',
	'version' => '0.1.5',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:112:{s:21:"ext_conf_template.txt";s:4:"3f2e";s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"554f";s:14:"stylesheet.css";s:4:"2e32";s:23:"backgrounds/pattern.gif";s:4:"2229";s:28:"backgrounds/pattern_dark.gif";s:4:"2cc4";s:29:"backgrounds/pattern_light.gif";s:4:"ccbc";s:20:"icons/module_doc.gif";s:4:"6e4f";s:21:"icons/module_file.gif";s:4:"fe68";s:28:"icons/module_file_images.gif";s:4:"936f";s:26:"icons/module_file_list.gif";s:4:"61d3";s:21:"icons/module_help.gif";s:4:"bafa";s:27:"icons/module_help_about.gif";s:4:"bafa";s:34:"icons/module_help_aboutmodules.gif";s:4:"bafa";s:22:"icons/module_tools.gif";s:4:"6287";s:29:"icons/module_tools_config.gif";s:4:"b3b1";s:28:"icons/module_tools_dbint.gif";s:4:"4b2a";s:25:"icons/module_tools_em.gif";s:4:"ee01";s:30:"icons/module_tools_install.gif";s:4:"21a7";s:30:"icons/module_tools_isearch.gif";s:4:"0cdc";s:26:"icons/module_tools_log.gif";s:4:"52a5";s:33:"icons/module_tools_phpmyadmin.gif";s:4:"83be";s:27:"icons/module_tools_user.gif";s:4:"55cb";s:21:"icons/module_user.gif";s:4:"30c9";s:27:"icons/module_user_setup.gif";s:4:"d516";s:32:"icons/module_user_taskcenter.gif";s:4:"22ff";s:20:"icons/module_web.gif";s:4:"6226";s:25:"icons/module_web_func.gif";s:4:"5122";s:25:"icons/module_web_info.gif";s:4:"cb44";s:27:"icons/module_web_layout.gif";s:4:"7a4d";s:25:"icons/module_web_list.gif";s:4:"8ceb";s:28:"icons/module_web_modules.gif";s:4:"941d";s:26:"icons/module_web_perms.gif";s:4:"70c3";s:23:"icons/module_web_ts.gif";s:4:"5a0a";s:25:"icons/module_web_view.gif";s:4:"3fd2";s:39:"icons/ext/chc_forum/mod1/moduleicon.gif";s:4:"4cd9";s:46:"icons/ext/content_uneraser/mod1/moduleicon.gif";s:4:"1cc6";s:37:"icons/ext/dam/mod_list/moduleicon.gif";s:4:"61d3";s:37:"icons/ext/dam/mod_main/moduleicon.gif";s:4:"cc15";s:41:"icons/ext/dam_catedit/mod1/moduleicon.gif";s:4:"b01f";s:38:"icons/ext/dam_file/mod1/moduleicon.gif";s:4:"7441";s:39:"icons/ext/dam_index/mod1/moduleicon.gif";s:4:"9139";s:36:"icons/ext/dbsync/mod1/moduleicon.gif";s:4:"2aa3";s:38:"icons/ext/de_phpot/mod1/moduleicon.gif";s:4:"d363";s:40:"icons/ext/extdeveval/mod1/moduleicon.gif";s:4:"b5c0";s:41:"icons/ext/templavoila/mod1/moduleicon.gif";s:4:"7a4d";s:41:"icons/ext/templavoila/mod2/moduleicon.gif";s:4:"5a0a";s:41:"icons/ext/templavoila/mod3/moduleicon.gif";s:4:"4f34";s:45:"icons/ext/th_mailformplus/mod1/moduleicon.gif";s:4:"d070";s:30:"icons/gfx/alt_backend_logo.gif";s:4:"913c";s:25:"icons/gfx/altmenuline.gif";s:4:"fb93";s:25:"icons/gfx/button_down.gif";s:4:"8c84";s:25:"icons/gfx/button_hide.gif";s:4:"c33e";s:25:"icons/gfx/button_left.gif";s:4:"ca64";s:26:"icons/gfx/button_right.gif";s:4:"6282";s:27:"icons/gfx/button_unhide.gif";s:4:"f715";s:23:"icons/gfx/button_up.gif";s:4:"e945";s:29:"icons/gfx/clear_all_cache.gif";s:4:"95fc";s:25:"icons/gfx/clear_cache.gif";s:4:"011a";s:41:"icons/gfx/clear_cache_files_in_typo3c.gif";s:4:"05c1";s:23:"icons/gfx/clip_copy.gif";s:4:"75ad";s:22:"icons/gfx/clip_cut.gif";s:4:"d9c5";s:23:"icons/gfx/close_12h.gif";s:4:"768f";s:22:"icons/gfx/closedok.gif";s:4:"2213";s:28:"icons/gfx/content_client.gif";s:4:"b41e";s:23:"icons/gfx/deletedok.gif";s:4:"95fc";s:19:"icons/gfx/edit2.gif";s:4:"5cd7";s:21:"icons/gfx/edit2_d.gif";s:4:"0b93";s:23:"icons/gfx/edit_page.gif";s:4:"5cd7";s:21:"icons/gfx/garbage.gif";s:4:"8f33";s:20:"icons/gfx/goback.gif";s:4:"ca64";s:24:"icons/gfx/helpbubble.gif";s:4:"54d5";s:18:"icons/gfx/list.gif";s:4:"49d1";s:30:"icons/gfx/minusbullet_list.gif";s:4:"5cce";s:20:"icons/gfx/new_el.gif";s:4:"2b57";s:22:"icons/gfx/new_page.gif";s:4:"2b57";s:18:"icons/gfx/perm.gif";s:4:"b4af";s:29:"icons/gfx/plusbullet_list.gif";s:4:"3952";s:23:"icons/gfx/refresh_n.gif";s:4:"ce60";s:29:"icons/gfx/saveandclosedok.gif";s:4:"09e6";s:21:"icons/gfx/savedok.gif";s:4:"81ab";s:24:"icons/gfx/savedoknew.gif";s:4:"7447";s:25:"icons/gfx/savedokshow.gif";s:4:"a563";s:22:"icons/gfx/shortcut.gif";s:4:"cf64";s:23:"icons/gfx/typo3logo.gif";s:4:"f720";s:18:"icons/gfx/zoom.gif";s:4:"7ebb";s:19:"icons/gfx/zoom2.gif";s:4:"98c2";s:29:"icons/gfx/i/_icon_website.gif";s:4:"f0aa";s:25:"icons/gfx/i/be_groups.gif";s:4:"b3af";s:24:"icons/gfx/i/be_users.gif";s:4:"39c5";s:30:"icons/gfx/i/be_users_admin.gif";s:4:"8cc8";s:21:"icons/gfx/i/pages.gif";s:4:"ca8c";s:24:"icons/gfx/i/pages__h.gif";s:4:"f74f";s:31:"icons/gfx/i/pages_notinmenu.gif";s:4:"8eb9";s:24:"icons/gfx/i/pages_up.gif";s:4:"5ee5";s:20:"icons/gfx/i/sysf.gif";s:4:"abeb";s:23:"icons/gfx/i/sysf__h.gif";s:4:"88b2";s:25:"icons/gfx/ol/halfline.gif";s:4:"5db2";s:21:"icons/gfx/ol/join.gif";s:4:"f6c4";s:27:"icons/gfx/ol/joinbottom.gif";s:4:"828d";s:21:"icons/gfx/ol/line.gif";s:4:"5e24";s:22:"icons/gfx/ol/minus.gif";s:4:"ccab";s:28:"icons/gfx/ol/minusbottom.gif";s:4:"1d31";s:28:"icons/gfx/ol/minusbullet.gif";s:4:"e538";s:26:"icons/gfx/ol/minusonly.gif";s:4:"d276";s:21:"icons/gfx/ol/plus.gif";s:4:"8c69";s:27:"icons/gfx/ol/plusbottom.gif";s:4:"eb8d";s:27:"icons/gfx/ol/plusbullet.gif";s:4:"c2dd";s:25:"icons/gfx/ol/plusonly.gif";s:4:"d4fb";s:24:"icons/gfx/ol/stopper.gif";s:4:"4970";s:15:"loginpics/1.jpg";s:4:"aa24";s:15:"loginpics/2.jpg";s:4:"5df2";}',
);

?>