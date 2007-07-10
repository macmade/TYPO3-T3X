<?php

########################################################################
# Extension Manager/Repository config file for ext: "mantis_bugtracker"
# 
# Auto generated 06-12-2005 15:10
# 
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'Mantis Bug Tracking System',
	'description' => 'This extension is a full backend integration of the Mantis Bug Tracking system, with a single sing-on system between Typo3 and Mantis.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => 'cms,lang',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'TYPO3_version' => '0.0.1-0.0.1',
	'PHP_version' => '0.0.1-0.0.1',
	'module' => 'mod1',
	'state' => 'alpha',
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
	'version' => '0.1.0',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:532:{s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"7350";s:14:"ext_tables.sql";s:4:"1cc6";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"733c";s:22:"mod1/extModInclude.php";s:4:"e3bf";s:14:"mod1/index.php";s:4:"05f6";s:18:"mod1/locallang.php";s:4:"24be";s:22:"mod1/locallang_mod.php";s:4:"81cc";s:19:"mod1/moduleicon.gif";s:4:"e7d0";s:29:"mod1/mantis-0.19.3/.cvsignore";s:4:"4894";s:37:"mod1/mantis-0.19.3/account_delete.php";s:4:"c9e8";s:35:"mod1/mantis-0.19.3/account_page.php";s:4:"93cb";s:40:"mod1/mantis-0.19.3/account_prefs_inc.php";s:4:"34a2";s:41:"mod1/mantis-0.19.3/account_prefs_page.php";s:4:"8776";s:42:"mod1/mantis-0.19.3/account_prefs_reset.php";s:4:"d63c";s:43:"mod1/mantis-0.19.3/account_prefs_update.php";s:4:"cf57";s:39:"mod1/mantis-0.19.3/account_prof_add.php";s:4:"5875";s:42:"mod1/mantis-0.19.3/account_prof_delete.php";s:4:"f9f5";s:45:"mod1/mantis-0.19.3/account_prof_edit_page.php";s:4:"97fa";s:48:"mod1/mantis-0.19.3/account_prof_make_default.php";s:4:"bf72";s:45:"mod1/mantis-0.19.3/account_prof_menu_page.php";s:4:"9bde";s:42:"mod1/mantis-0.19.3/account_prof_update.php";s:4:"f212";s:37:"mod1/mantis-0.19.3/account_update.php";s:4:"ce1c";s:45:"mod1/mantis-0.19.3/adm_permissions_report.php";s:4:"9f77";s:38:"mod1/mantis-0.19.3/bug_actiongroup.php";s:4:"cf8f";s:43:"mod1/mantis-0.19.3/bug_actiongroup_page.php";s:4:"fb46";s:33:"mod1/mantis-0.19.3/bug_assign.php";s:4:"a986";s:42:"mod1/mantis-0.19.3/bug_assign_reporter.php";s:4:"9382";s:45:"mod1/mantis-0.19.3/bug_change_status_page.php";s:4:"5f8f";s:33:"mod1/mantis-0.19.3/bug_delete.php";s:4:"7eec";s:35:"mod1/mantis-0.19.3/bug_file_add.php";s:4:"cee7";s:38:"mod1/mantis-0.19.3/bug_file_delete.php";s:4:"03ed";s:42:"mod1/mantis-0.19.3/bug_file_upload_inc.php";s:4:"d604";s:34:"mod1/mantis-0.19.3/bug_monitor.php";s:4:"b698";s:48:"mod1/mantis-0.19.3/bug_monitor_list_view_inc.php";s:4:"a88a";s:43:"mod1/mantis-0.19.3/bug_relationship_add.php";s:4:"1309";s:46:"mod1/mantis-0.19.3/bug_relationship_delete.php";s:4:"dea5";s:45:"mod1/mantis-0.19.3/bug_relationship_graph.php";s:4:"ee57";s:49:"mod1/mantis-0.19.3/bug_relationship_graph_img.php";s:4:"eff4";s:35:"mod1/mantis-0.19.3/bug_reminder.php";s:4:"eaa2";s:40:"mod1/mantis-0.19.3/bug_reminder_page.php";s:4:"a229";s:33:"mod1/mantis-0.19.3/bug_report.php";s:4:"5cb6";s:47:"mod1/mantis-0.19.3/bug_report_advanced_page.php";s:4:"42d6";s:38:"mod1/mantis-0.19.3/bug_report_page.php";s:4:"9db1";s:42:"mod1/mantis-0.19.3/bug_set_sponsorship.php";s:4:"c015";s:52:"mod1/mantis-0.19.3/bug_sponsorship_list_view_inc.php";s:4:"d5b0";s:33:"mod1/mantis-0.19.3/bug_update.php";s:4:"7be0";s:47:"mod1/mantis-0.19.3/bug_update_advanced_page.php";s:4:"c796";s:38:"mod1/mantis-0.19.3/bug_update_page.php";s:4:"3161";s:45:"mod1/mantis-0.19.3/bug_view_advanced_page.php";s:4:"e26d";s:35:"mod1/mantis-0.19.3/bug_view_inc.php";s:4:"d54c";s:36:"mod1/mantis-0.19.3/bug_view_page.php";s:4:"3a28";s:34:"mod1/mantis-0.19.3/bugnote_add.php";s:4:"25f8";s:38:"mod1/mantis-0.19.3/bugnote_add_inc.php";s:4:"9119";s:37:"mod1/mantis-0.19.3/bugnote_delete.php";s:4:"504a";s:40:"mod1/mantis-0.19.3/bugnote_edit_page.php";s:4:"9898";s:45:"mod1/mantis-0.19.3/bugnote_set_view_state.php";s:4:"1c75";s:37:"mod1/mantis-0.19.3/bugnote_update.php";s:4:"5eb5";s:39:"mod1/mantis-0.19.3/bugnote_view_inc.php";s:4:"6685";s:37:"mod1/mantis-0.19.3/changelog_page.php";s:4:"c5bc";s:42:"mod1/mantis-0.19.3/config_defaults_inc.php";s:4:"1c3a";s:33:"mod1/mantis-0.19.3/config_inc.php";s:4:"02be";s:40:"mod1/mantis-0.19.3/config_inc.php.sample";s:4:"9637";s:27:"mod1/mantis-0.19.3/core.php";s:4:"04dd";s:33:"mod1/mantis-0.19.3/csv_export.php";s:4:"3188";s:36:"mod1/mantis-0.19.3/file_download.php";s:4:"bd1a";s:34:"mod1/mantis-0.19.3/history_inc.php";s:4:"e5c6";s:28:"mod1/mantis-0.19.3/index.php";s:4:"f67f";s:34:"mod1/mantis-0.19.3/jump_to_bug.php";s:4:"506e";s:28:"mod1/mantis-0.19.3/login.php";s:4:"7589";s:33:"mod1/mantis-0.19.3/login_anon.php";s:4:"f428";s:40:"mod1/mantis-0.19.3/login_cookie_test.php";s:4:"53d1";s:33:"mod1/mantis-0.19.3/login_page.php";s:4:"1712";s:45:"mod1/mantis-0.19.3/login_select_proj_page.php";s:4:"a31d";s:34:"mod1/mantis-0.19.3/logout_page.php";s:4:"f0e1";s:31:"mod1/mantis-0.19.3/lost_pwd.php";s:4:"ad9a";s:36:"mod1/mantis-0.19.3/lost_pwd_page.php";s:4:"b8af";s:32:"mod1/mantis-0.19.3/main_page.php";s:4:"e385";s:39:"mod1/mantis-0.19.3/make_captcha_img.php";s:4:"c487";s:49:"mod1/mantis-0.19.3/manage_custom_field_create.php";s:4:"9dc0";s:49:"mod1/mantis-0.19.3/manage_custom_field_delete.php";s:4:"08d7";s:52:"mod1/mantis-0.19.3/manage_custom_field_edit_page.php";s:4:"5fa5";s:47:"mod1/mantis-0.19.3/manage_custom_field_page.php";s:4:"d7ab";s:49:"mod1/mantis-0.19.3/manage_custom_field_update.php";s:4:"3a56";s:42:"mod1/mantis-0.19.3/manage_proj_cat_add.php";s:4:"22e9";s:43:"mod1/mantis-0.19.3/manage_proj_cat_copy.php";s:4:"97c0";s:45:"mod1/mantis-0.19.3/manage_proj_cat_delete.php";s:4:"a451";s:48:"mod1/mantis-0.19.3/manage_proj_cat_edit_page.php";s:4:"ff9a";s:45:"mod1/mantis-0.19.3/manage_proj_cat_update.php";s:4:"99eb";s:41:"mod1/mantis-0.19.3/manage_proj_create.php";s:4:"87c9";s:46:"mod1/mantis-0.19.3/manage_proj_create_page.php";s:4:"20a3";s:60:"mod1/mantis-0.19.3/manage_proj_custom_field_add_existing.php";s:4:"531d";s:54:"mod1/mantis-0.19.3/manage_proj_custom_field_remove.php";s:4:"db5d";s:54:"mod1/mantis-0.19.3/manage_proj_custom_field_update.php";s:4:"a00e";s:41:"mod1/mantis-0.19.3/manage_proj_delete.php";s:4:"e401";s:44:"mod1/mantis-0.19.3/manage_proj_edit_page.php";s:4:"1e08";s:39:"mod1/mantis-0.19.3/manage_proj_page.php";s:4:"e3b6";s:41:"mod1/mantis-0.19.3/manage_proj_update.php";s:4:"1b18";s:43:"mod1/mantis-0.19.3/manage_proj_user_add.php";s:4:"b3c8";s:44:"mod1/mantis-0.19.3/manage_proj_user_copy.php";s:4:"0f64";s:46:"mod1/mantis-0.19.3/manage_proj_user_remove.php";s:4:"81a3";s:42:"mod1/mantis-0.19.3/manage_proj_ver_add.php";s:4:"2abd";s:45:"mod1/mantis-0.19.3/manage_proj_ver_delete.php";s:4:"07d2";s:48:"mod1/mantis-0.19.3/manage_proj_ver_edit_page.php";s:4:"424d";s:45:"mod1/mantis-0.19.3/manage_proj_ver_update.php";s:4:"c524";s:41:"mod1/mantis-0.19.3/manage_user_create.php";s:4:"1fa2";s:46:"mod1/mantis-0.19.3/manage_user_create_page.php";s:4:"008b";s:41:"mod1/mantis-0.19.3/manage_user_delete.php";s:4:"26ba";s:44:"mod1/mantis-0.19.3/manage_user_edit_page.php";s:4:"e3e5";s:39:"mod1/mantis-0.19.3/manage_user_page.php";s:4:"eebf";s:43:"mod1/mantis-0.19.3/manage_user_proj_add.php";s:4:"5a98";s:46:"mod1/mantis-0.19.3/manage_user_proj_delete.php";s:4:"7e87";s:40:"mod1/mantis-0.19.3/manage_user_prune.php";s:4:"e214";s:40:"mod1/mantis-0.19.3/manage_user_reset.php";s:4:"0462";s:41:"mod1/mantis-0.19.3/manage_user_update.php";s:4:"969c";s:44:"mod1/mantis-0.19.3/mantis_offline.php.sample";s:4:"dae7";s:31:"mod1/mantis-0.19.3/meta_inc.php";s:4:"5b2a";s:35:"mod1/mantis-0.19.3/my_view_page.php";s:4:"c398";s:31:"mod1/mantis-0.19.3/news_add.php";s:4:"dd6b";s:34:"mod1/mantis-0.19.3/news_delete.php";s:4:"bc01";s:37:"mod1/mantis-0.19.3/news_edit_page.php";s:4:"c549";s:31:"mod1/mantis-0.19.3/news_inc.php";s:4:"9f73";s:37:"mod1/mantis-0.19.3/news_list_page.php";s:4:"7465";s:37:"mod1/mantis-0.19.3/news_menu_page.php";s:4:"ab35";s:31:"mod1/mantis-0.19.3/news_rss.php";s:4:"4ace";s:34:"mod1/mantis-0.19.3/news_update.php";s:4:"f5b6";s:37:"mod1/mantis-0.19.3/news_view_page.php";s:4:"bf71";s:48:"mod1/mantis-0.19.3/print_all_bug_options_inc.php";s:4:"a280";s:49:"mod1/mantis-0.19.3/print_all_bug_options_page.php";s:4:"3666";s:50:"mod1/mantis-0.19.3/print_all_bug_options_reset.php";s:4:"7022";s:51:"mod1/mantis-0.19.3/print_all_bug_options_update.php";s:4:"059d";s:41:"mod1/mantis-0.19.3/print_all_bug_page.php";s:4:"219e";s:47:"mod1/mantis-0.19.3/print_all_bug_page_excel.php";s:4:"3ba7";s:46:"mod1/mantis-0.19.3/print_all_bug_page_word.php";s:4:"0f7d";s:37:"mod1/mantis-0.19.3/print_bug_page.php";s:4:"637d";s:40:"mod1/mantis-0.19.3/print_bugnote_inc.php";s:4:"3180";s:35:"mod1/mantis-0.19.3/proj_doc_add.php";s:4:"8a53";s:40:"mod1/mantis-0.19.3/proj_doc_add_page.php";s:4:"cae2";s:38:"mod1/mantis-0.19.3/proj_doc_delete.php";s:4:"cd21";s:41:"mod1/mantis-0.19.3/proj_doc_edit_page.php";s:4:"ae02";s:36:"mod1/mantis-0.19.3/proj_doc_page.php";s:4:"9e7b";s:38:"mod1/mantis-0.19.3/proj_doc_update.php";s:4:"c917";s:35:"mod1/mantis-0.19.3/query_delete.php";s:4:"88b2";s:40:"mod1/mantis-0.19.3/query_delete_page.php";s:4:"372c";s:34:"mod1/mantis-0.19.3/query_store.php";s:4:"8336";s:39:"mod1/mantis-0.19.3/query_store_page.php";s:4:"ba3d";s:38:"mod1/mantis-0.19.3/query_view_page.php";s:4:"6174";s:34:"mod1/mantis-0.19.3/set_project.php";s:4:"24bf";s:29:"mod1/mantis-0.19.3/signup.php";s:4:"8b40";s:34:"mod1/mantis-0.19.3/signup_page.php";s:4:"7423";s:47:"mod1/mantis-0.19.3/summary_graph_bycategory.php";s:4:"ae34";s:51:"mod1/mantis-0.19.3/summary_graph_bycategory_pct.php";s:4:"f11a";s:48:"mod1/mantis-0.19.3/summary_graph_bydeveloper.php";s:4:"5680";s:47:"mod1/mantis-0.19.3/summary_graph_bypriority.php";s:4:"05b8";s:51:"mod1/mantis-0.19.3/summary_graph_bypriority_mix.php";s:4:"2aeb";s:51:"mod1/mantis-0.19.3/summary_graph_bypriority_pct.php";s:4:"cb15";s:47:"mod1/mantis-0.19.3/summary_graph_byreporter.php";s:4:"c32f";s:49:"mod1/mantis-0.19.3/summary_graph_byresolution.php";s:4:"cedf";s:53:"mod1/mantis-0.19.3/summary_graph_byresolution_mix.php";s:4:"1095";s:53:"mod1/mantis-0.19.3/summary_graph_byresolution_pct.php";s:4:"43af";s:47:"mod1/mantis-0.19.3/summary_graph_byseverity.php";s:4:"7c6c";s:51:"mod1/mantis-0.19.3/summary_graph_byseverity_mix.php";s:4:"70e6";s:51:"mod1/mantis-0.19.3/summary_graph_byseverity_pct.php";s:4:"7a04";s:45:"mod1/mantis-0.19.3/summary_graph_bystatus.php";s:4:"2e4f";s:49:"mod1/mantis-0.19.3/summary_graph_bystatus_pct.php";s:4:"fdb7";s:54:"mod1/mantis-0.19.3/summary_graph_cumulative_bydate.php";s:4:"269d";s:49:"mod1/mantis-0.19.3/summary_graph_imp_category.php";s:4:"b735";s:49:"mod1/mantis-0.19.3/summary_graph_imp_priority.php";s:4:"bf57";s:51:"mod1/mantis-0.19.3/summary_graph_imp_resolution.php";s:4:"f71b";s:49:"mod1/mantis-0.19.3/summary_graph_imp_severity.php";s:4:"c374";s:47:"mod1/mantis-0.19.3/summary_graph_imp_status.php";s:4:"11cc";s:43:"mod1/mantis-0.19.3/summary_jpgraph_page.php";s:4:"deb6";s:35:"mod1/mantis-0.19.3/summary_page.php";s:4:"9a0a";s:29:"mod1/mantis-0.19.3/verify.php";s:4:"fa4e";s:27:"mod1/mantis-0.19.3/view.php";s:4:"356e";s:40:"mod1/mantis-0.19.3/view_all_bug_page.php";s:4:"0a5c";s:35:"mod1/mantis-0.19.3/view_all_inc.php";s:4:"cc19";s:35:"mod1/mantis-0.19.3/view_all_set.php";s:4:"d265";s:40:"mod1/mantis-0.19.3/view_filters_page.php";s:4:"cd95";s:34:"mod1/mantis-0.19.3/admin/admin.css";s:4:"977f";s:34:"mod1/mantis-0.19.3/admin/check.php";s:4:"dbd5";s:39:"mod1/mantis-0.19.3/admin/copy_field.php";s:4:"a137";s:47:"mod1/mantis-0.19.3/admin/db_table_names_inc.php";s:4:"e54a";s:34:"mod1/mantis-0.19.3/admin/index.php";s:4:"f8a3";s:41:"mod1/mantis-0.19.3/admin/move_db2disk.php";s:4:"0209";s:41:"mod1/mantis-0.19.3/admin/system_utils.php";s:4:"2966";s:36:"mod1/mantis-0.19.3/admin/upgrade.php";s:4:"5234";s:45:"mod1/mantis-0.19.3/admin/upgrade_advanced.php";s:4:"af48";s:45:"mod1/mantis-0.19.3/admin/upgrade_escaping.php";s:4:"2d36";s:40:"mod1/mantis-0.19.3/admin/upgrade_inc.php";s:4:"9698";s:41:"mod1/mantis-0.19.3/admin/upgrade_list.php";s:4:"50c6";s:44:"mod1/mantis-0.19.3/admin/upgrade_warning.php";s:4:"5498";s:37:"mod1/mantis-0.19.3/admin/workflow.php";s:4:"c9c1";s:37:"mod1/mantis-0.19.3/admin/css/core.php";s:4:"aa24";s:45:"mod1/mantis-0.19.3/admin/css/css_download.php";s:4:"0a70";s:40:"mod1/mantis-0.19.3/admin/css/css_inc.php";s:4:"8e9f";s:41:"mod1/mantis-0.19.3/admin/css/css_view.php";s:4:"98b2";s:38:"mod1/mantis-0.19.3/admin/css/index.php";s:4:"b9f9";s:41:"mod1/mantis-0.19.3/admin/css/view_inc.php";s:4:"4512";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_13_inc.php";s:4:"f61e";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_14_inc.php";s:4:"c364";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_15_inc.php";s:4:"65ea";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_16_inc.php";s:4:"3852";s:61:"mod1/mantis-0.19.3/admin/upgrades/0_17_escaping_fixes_inc.php";s:4:"cf52";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_17_inc.php";s:4:"0519";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_18_inc.php";s:4:"37c9";s:46:"mod1/mantis-0.19.3/admin/upgrades/0_19_inc.php";s:4:"ba45";s:34:"mod1/mantis-0.19.3/core/.cvsignore";s:4:"77c6";s:38:"mod1/mantis-0.19.3/core/access_api.php";s:4:"4c6b";s:46:"mod1/mantis-0.19.3/core/authentication_api.php";s:4:"e54c";s:35:"mod1/mantis-0.19.3/core/bug_api.php";s:4:"31eb";s:39:"mod1/mantis-0.19.3/core/bugnote_api.php";s:4:"5544";s:40:"mod1/mantis-0.19.3/core/category_api.php";s:4:"2570";s:35:"mod1/mantis-0.19.3/core/checkin.php";s:4:"6238";s:48:"mod1/mantis-0.19.3/core/class.RSSBuilder.inc.php";s:4:"7266";s:42:"mod1/mantis-0.19.3/core/class.urlmatch.php";s:4:"f9fe";s:40:"mod1/mantis-0.19.3/core/collapse_api.php";s:4:"dcdb";s:40:"mod1/mantis-0.19.3/core/compress_api.php";s:4:"4de8";s:38:"mod1/mantis-0.19.3/core/config_api.php";s:4:"8530";s:40:"mod1/mantis-0.19.3/core/constant_inc.php";s:4:"245a";s:35:"mod1/mantis-0.19.3/core/csv_api.php";s:4:"f9fe";s:44:"mod1/mantis-0.19.3/core/current_user_api.php";s:4:"c4d5";s:44:"mod1/mantis-0.19.3/core/custom_field_api.php";s:4:"3dcc";s:47:"mod1/mantis-0.19.3/core/custom_function_api.php";s:4:"c250";s:40:"mod1/mantis-0.19.3/core/database_api.php";s:4:"5553";s:36:"mod1/mantis-0.19.3/core/date_api.php";s:4:"7273";s:37:"mod1/mantis-0.19.3/core/email_api.php";s:4:"6109";s:37:"mod1/mantis-0.19.3/core/error_api.php";s:4:"37b4";s:36:"mod1/mantis-0.19.3/core/file_api.php";s:4:"8878";s:38:"mod1/mantis-0.19.3/core/filter_api.php";s:4:"f63d";s:35:"mod1/mantis-0.19.3/core/gpc_api.php";s:4:"6569";s:37:"mod1/mantis-0.19.3/core/graph_api.php";s:4:"123b";s:40:"mod1/mantis-0.19.3/core/graphviz_api.php";s:4:"1a7a";s:38:"mod1/mantis-0.19.3/core/helper_api.php";s:4:"9ce4";s:39:"mod1/mantis-0.19.3/core/history_api.php";s:4:"6f67";s:36:"mod1/mantis-0.19.3/core/html_api.php";s:4:"dee0";s:36:"mod1/mantis-0.19.3/core/icon_api.php";s:4:"cad4";s:36:"mod1/mantis-0.19.3/core/lang_api.php";s:4:"3c00";s:36:"mod1/mantis-0.19.3/core/ldap_api.php";s:4:"3eb8";s:39:"mod1/mantis-0.19.3/core/my_view_inc.php";s:4:"7a38";s:36:"mod1/mantis-0.19.3/core/news_api.php";s:4:"77b0";s:36:"mod1/mantis-0.19.3/core/obsolete.php";s:4:"14d6";s:35:"mod1/mantis-0.19.3/core/php_api.php";s:4:"4388";s:39:"mod1/mantis-0.19.3/core/prepare_api.php";s:4:"76a6";s:37:"mod1/mantis-0.19.3/core/print_api.php";s:4:"9f88";s:39:"mod1/mantis-0.19.3/core/profile_api.php";s:4:"45d6";s:39:"mod1/mantis-0.19.3/core/project_api.php";s:4:"7826";s:44:"mod1/mantis-0.19.3/core/relationship_api.php";s:4:"c8cb";s:50:"mod1/mantis-0.19.3/core/relationship_graph_api.php";s:4:"e19e";s:43:"mod1/mantis-0.19.3/core/sponsorship_api.php";s:4:"eca3";s:38:"mod1/mantis-0.19.3/core/string_api.php";s:4:"b36e";s:39:"mod1/mantis-0.19.3/core/summary_api.php";s:4:"7fb1";s:37:"mod1/mantis-0.19.3/core/timer_api.php";s:4:"0e93";s:36:"mod1/mantis-0.19.3/core/user_api.php";s:4:"db0d";s:41:"mod1/mantis-0.19.3/core/user_pref_api.php";s:4:"12af";s:39:"mod1/mantis-0.19.3/core/utility_api.php";s:4:"9a93";s:39:"mod1/mantis-0.19.3/core/version_api.php";s:4:"7083";s:50:"mod1/mantis-0.19.3/core/adodb/adodb-csvlib.inc.php";s:4:"63fb";s:52:"mod1/mantis-0.19.3/core/adodb/adodb-datadict.inc.php";s:4:"6724";s:49:"mod1/mantis-0.19.3/core/adodb/adodb-error.inc.php";s:4:"1087";s:56:"mod1/mantis-0.19.3/core/adodb/adodb-errorhandler.inc.php";s:4:"9da6";s:53:"mod1/mantis-0.19.3/core/adodb/adodb-errorpear.inc.php";s:4:"7865";s:54:"mod1/mantis-0.19.3/core/adodb/adodb-exceptions.inc.php";s:4:"3e8e";s:52:"mod1/mantis-0.19.3/core/adodb/adodb-iterator.inc.php";s:4:"86e7";s:47:"mod1/mantis-0.19.3/core/adodb/adodb-lib.inc.php";s:4:"240a";s:49:"mod1/mantis-0.19.3/core/adodb/adodb-pager.inc.php";s:4:"3889";s:48:"mod1/mantis-0.19.3/core/adodb/adodb-pear.inc.php";s:4:"37ad";s:48:"mod1/mantis-0.19.3/core/adodb/adodb-perf.inc.php";s:4:"1f14";s:64:"mod1/mantis-0.19.3/core/adodb/adodb-perf.inc.php.#.LENS-03-09-05";s:4:"9df6";s:48:"mod1/mantis-0.19.3/core/adodb/adodb-php4.inc.php";s:4:"e5ba";s:48:"mod1/mantis-0.19.3/core/adodb/adodb-time.inc.php";s:4:"6bd2";s:44:"mod1/mantis-0.19.3/core/adodb/adodb-time.zip";s:4:"dcf9";s:53:"mod1/mantis-0.19.3/core/adodb/adodb-xmlschema.inc.php";s:4:"6ba0";s:43:"mod1/mantis-0.19.3/core/adodb/adodb.inc.php";s:4:"2059";s:41:"mod1/mantis-0.19.3/core/adodb/license.txt";s:4:"8bd7";s:48:"mod1/mantis-0.19.3/core/adodb/pivottable.inc.php";s:4:"9d51";s:40:"mod1/mantis-0.19.3/core/adodb/readme.txt";s:4:"856d";s:46:"mod1/mantis-0.19.3/core/adodb/rsfilter.inc.php";s:4:"84ba";s:40:"mod1/mantis-0.19.3/core/adodb/server.php";s:4:"241f";s:46:"mod1/mantis-0.19.3/core/adodb/toexport.inc.php";s:4:"7358";s:44:"mod1/mantis-0.19.3/core/adodb/tohtml.inc.php";s:4:"b2c9";s:43:"mod1/mantis-0.19.3/core/adodb/xmlschema.dtd";s:4:"61b0";s:54:"mod1/mantis-0.19.3/core/adodb/contrib/toxmlrpc.inc.php";s:4:"3d6d";s:59:"mod1/mantis-0.19.3/core/adodb/cute_icons_for_site/adodb.gif";s:4:"9430";s:60:"mod1/mantis-0.19.3/core/adodb/cute_icons_for_site/adodb2.gif";s:4:"f540";s:62:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-access.inc.php";s:4:"8884";s:59:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-db2.inc.php";s:4:"8026";s:64:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-firebird.inc.php";s:4:"689f";s:63:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-generic.inc.php";s:4:"11b7";s:61:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-ibase.inc.php";s:4:"93c5";s:64:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-informix.inc.php";s:4:"0164";s:61:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-mssql.inc.php";s:4:"0baa";s:61:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-mysql.inc.php";s:4:"6344";s:60:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-oci8.inc.php";s:4:"9b3b";s:64:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-postgres.inc.php";s:4:"221a";s:61:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-sapdb.inc.php";s:4:"afbf";s:62:"mod1/mantis-0.19.3/core/adodb/datadict/datadict-sybase.inc.php";s:4:"6d08";s:49:"mod1/mantis-0.19.3/core/adodb/docs/docs-adodb.htm";s:4:"d1b9";s:52:"mod1/mantis-0.19.3/core/adodb/docs/docs-datadict.htm";s:4:"d59c";s:50:"mod1/mantis-0.19.3/core/adodb/docs/docs-oracle.htm";s:4:"33df";s:48:"mod1/mantis-0.19.3/core/adodb/docs/docs-perf.htm";s:4:"3320";s:51:"mod1/mantis-0.19.3/core/adodb/docs/docs-session.htm";s:4:"c22a";s:52:"mod1/mantis-0.19.3/core/adodb/docs/old-changelog.htm";s:4:"c1ca";s:45:"mod1/mantis-0.19.3/core/adodb/docs/readme.htm";s:4:"9a0e";s:56:"mod1/mantis-0.19.3/core/adodb/docs/tips_portable_sql.htm";s:4:"4027";s:43:"mod1/mantis-0.19.3/core/adodb/docs/tute.htm";s:4:"691e";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-access.inc.php";s:4:"ae9f";s:55:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ado.inc.php";s:4:"d03c";s:56:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ado5.inc.php";s:4:"33ac";s:62:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ado_access.inc.php";s:4:"008b";s:61:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ado_mssql.inc.php";s:4:"9415";s:65:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-borland_ibase.inc.php";s:4:"2881";s:55:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-csv.inc.php";s:4:"9075";s:55:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-db2.inc.php";s:4:"0feb";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-fbsql.inc.php";s:4:"67bb";s:60:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-firebird.inc.php";s:4:"5eff";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ibase.inc.php";s:4:"8a4e";s:60:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-informix.inc.php";s:4:"460d";s:62:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-informix72.inc.php";s:4:"b869";s:56:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-ldap.inc.php";s:4:"4c40";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-mssql.inc.php";s:4:"a867";s:59:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-mssqlpo.inc.php";s:4:"490c";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-mysql.inc.php";s:4:"afca";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-mysqli.inc.php";s:4:"3d1f";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-mysqlt.inc.php";s:4:"253d";s:59:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-netezza.inc.php";s:4:"2ae6";s:56:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-oci8.inc.php";s:4:"f037";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-oci805.inc.php";s:4:"21fc";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-oci8po.inc.php";s:4:"8e63";s:56:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-odbc.inc.php";s:4:"a84b";s:62:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-odbc_mssql.inc.php";s:4:"347d";s:63:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-odbc_oracle.inc.php";s:4:"4da7";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-odbtp.inc.php";s:4:"e093";s:65:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-odbtp_unicode.inc.php";s:4:"4d8c";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-oracle.inc.php";s:4:"fbc4";s:55:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-pdo.inc.php";s:4:"5097";s:60:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-postgres.inc.php";s:4:"f918";s:62:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-postgres64.inc.php";s:4:"be14";s:61:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-postgres7.inc.php";s:4:"7487";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-proxy.inc.php";s:4:"af9b";s:57:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sapdb.inc.php";s:4:"29a3";s:63:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sqlanywhere.inc.php";s:4:"f1a6";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sqlite.inc.php";s:4:"eb5b";s:62:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sqlite.inc.php.bak";s:4:"0918";s:60:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sqlitepo.inc.php";s:4:"aff9";s:58:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-sybase.inc.php";s:4:"1ce5";s:55:"mod1/mantis-0.19.3/core/adodb/drivers/adodb-vfp.inc.php";s:4:"5b92";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-ar.inc.php";s:4:"5e28";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-bg.inc.php";s:4:"da59";s:55:"mod1/mantis-0.19.3/core/adodb/lang/adodb-bgutf8.inc.php";s:4:"f32d";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-ca.inc.php";s:4:"96da";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-cn.inc.php";s:4:"155e";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-cz.inc.php";s:4:"6964";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-de.inc.php";s:4:"26c5";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-en.inc.php";s:4:"02b0";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-es.inc.php";s:4:"dae3";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-fr.inc.php";s:4:"dd47";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-hu.inc.php";s:4:"9fde";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-it.inc.php";s:4:"15e2";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-nl.inc.php";s:4:"f1ef";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-pl.inc.php";s:4:"2a6c";s:54:"mod1/mantis-0.19.3/core/adodb/lang/adodb-pt-br.inc.php";s:4:"e973";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-ro.inc.php";s:4:"fcbc";s:55:"mod1/mantis-0.19.3/core/adodb/lang/adodb-ru1251.inc.php";s:4:"e828";s:51:"mod1/mantis-0.19.3/core/adodb/lang/adodb-sv.inc.php";s:4:"81fa";s:50:"mod1/mantis-0.19.3/core/adodb/pear/readme.Auth.txt";s:4:"4970";s:59:"mod1/mantis-0.19.3/core/adodb/pear/Auth/Container/ADOdb.php";s:4:"f606";s:51:"mod1/mantis-0.19.3/core/adodb/perf/perf-db2.inc.php";s:4:"6cea";s:56:"mod1/mantis-0.19.3/core/adodb/perf/perf-informix.inc.php";s:4:"a872";s:53:"mod1/mantis-0.19.3/core/adodb/perf/perf-mssql.inc.php";s:4:"126b";s:53:"mod1/mantis-0.19.3/core/adodb/perf/perf-mysql.inc.php";s:4:"cb17";s:52:"mod1/mantis-0.19.3/core/adodb/perf/perf-oci8.inc.php";s:4:"0731";s:56:"mod1/mantis-0.19.3/core/adodb/perf/perf-postgres.inc.php";s:4:"beef";s:62:"mod1/mantis-0.19.3/core/adodb/session/adodb-compress-bzip2.php";s:4:"122a";s:61:"mod1/mantis-0.19.3/core/adodb/session/adodb-compress-gzip.php";s:4:"2130";s:60:"mod1/mantis-0.19.3/core/adodb/session/adodb-cryptsession.php";s:4:"086a";s:62:"mod1/mantis-0.19.3/core/adodb/session/adodb-encrypt-mcrypt.php";s:4:"e870";s:59:"mod1/mantis-0.19.3/core/adodb/session/adodb-encrypt-md5.php";s:4:"74d2";s:62:"mod1/mantis-0.19.3/core/adodb/session/adodb-encrypt-secret.php";s:4:"67df";s:52:"mod1/mantis-0.19.3/core/adodb/session/adodb-sess.txt";s:4:"260b";s:60:"mod1/mantis-0.19.3/core/adodb/session/adodb-session-clob.php";s:4:"07cd";s:55:"mod1/mantis-0.19.3/core/adodb/session/adodb-session.php";s:4:"dd37";s:62:"mod1/mantis-0.19.3/core/adodb/session/adodb-sessions.mysql.sql";s:4:"42fe";s:68:"mod1/mantis-0.19.3/core/adodb/session/adodb-sessions.oracle.clob.sql";s:4:"3c64";s:63:"mod1/mantis-0.19.3/core/adodb/session/adodb-sessions.oracle.sql";s:4:"08d0";s:51:"mod1/mantis-0.19.3/core/adodb/session/crypt.inc.php";s:4:"2d25";s:64:"mod1/mantis-0.19.3/core/adodb/session/old/adodb-cryptsession.php";s:4:"9389";s:64:"mod1/mantis-0.19.3/core/adodb/session/old/adodb-session-clob.php";s:4:"7362";s:59:"mod1/mantis-0.19.3/core/adodb/session/old/adodb-session.php";s:4:"f03d";s:55:"mod1/mantis-0.19.3/core/adodb/session/old/crypt.inc.php";s:4:"2d25";s:49:"mod1/mantis-0.19.3/core/adodb/tests/benchmark.php";s:4:"4529";s:46:"mod1/mantis-0.19.3/core/adodb/tests/client.php";s:4:"f9c8";s:43:"mod1/mantis-0.19.3/core/adodb/tests/pdo.php";s:4:"9e4c";s:53:"mod1/mantis-0.19.3/core/adodb/tests/test-datadict.php";s:4:"43d9";s:49:"mod1/mantis-0.19.3/core/adodb/tests/test-perf.php";s:4:"b95e";s:51:"mod1/mantis-0.19.3/core/adodb/tests/test-pgblob.php";s:4:"f160";s:49:"mod1/mantis-0.19.3/core/adodb/tests/test-php5.php";s:4:"527d";s:54:"mod1/mantis-0.19.3/core/adodb/tests/test-xmlschema.php";s:4:"afe8";s:44:"mod1/mantis-0.19.3/core/adodb/tests/test.php";s:4:"12a7";s:45:"mod1/mantis-0.19.3/core/adodb/tests/test2.php";s:4:"384b";s:45:"mod1/mantis-0.19.3/core/adodb/tests/test3.php";s:4:"2524";s:45:"mod1/mantis-0.19.3/core/adodb/tests/test4.php";s:4:"a428";s:45:"mod1/mantis-0.19.3/core/adodb/tests/test5.php";s:4:"34ce";s:53:"mod1/mantis-0.19.3/core/adodb/tests/test_rs_array.php";s:4:"08e2";s:49:"mod1/mantis-0.19.3/core/adodb/tests/testcache.php";s:4:"24b7";s:57:"mod1/mantis-0.19.3/core/adodb/tests/testdatabases.inc.php";s:4:"36bd";s:49:"mod1/mantis-0.19.3/core/adodb/tests/testgenid.php";s:4:"1576";s:49:"mod1/mantis-0.19.3/core/adodb/tests/testmssql.php";s:4:"7084";s:48:"mod1/mantis-0.19.3/core/adodb/tests/testoci8.php";s:4:"2a19";s:54:"mod1/mantis-0.19.3/core/adodb/tests/testoci8cursor.php";s:4:"0c65";s:50:"mod1/mantis-0.19.3/core/adodb/tests/testpaging.php";s:4:"58e4";s:48:"mod1/mantis-0.19.3/core/adodb/tests/testpear.php";s:4:"9fab";s:52:"mod1/mantis-0.19.3/core/adodb/tests/testsessions.php";s:4:"4d85";s:44:"mod1/mantis-0.19.3/core/adodb/tests/time.php";s:4:"b158";s:46:"mod1/mantis-0.19.3/core/adodb/tests/tmssql.php";s:4:"2933";s:49:"mod1/mantis-0.19.3/core/adodb/tests/xmlschema.xml";s:4:"a692";s:53:"mod1/mantis-0.19.3/core/adodb/xsl/convert-0.1-0.2.xsl";s:4:"f2a0";s:53:"mod1/mantis-0.19.3/core/adodb/xsl/convert-0.2-0.1.xsl";s:4:"5d27";s:48:"mod1/mantis-0.19.3/core/adodb/xsl/remove-0.2.xsl";s:4:"9679";s:47:"mod1/mantis-0.19.3/core/phpmailer/ChangeLog.txt";s:4:"9e3a";s:41:"mod1/mantis-0.19.3/core/phpmailer/LICENSE";s:4:"278f";s:40:"mod1/mantis-0.19.3/core/phpmailer/README";s:4:"20d2";s:53:"mod1/mantis-0.19.3/core/phpmailer/class.phpmailer.php";s:4:"b1ec";s:48:"mod1/mantis-0.19.3/core/phpmailer/class.smtp.php";s:4:"0453";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-br.php";s:4:"01cc";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-cz.php";s:4:"b01a";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-de.php";s:4:"3e94";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-en.php";s:4:"b7f2";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-es.php";s:4:"a27e";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-fr.php";s:4:"2a14";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-it.php";s:4:"6c28";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-nl.php";s:4:"af99";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-no.php";s:4:"87a2";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-se.php";s:4:"a04e";s:64:"mod1/mantis-0.19.3/core/phpmailer/language/phpmailer.lang-tr.php";s:4:"0265";s:34:"mod1/mantis-0.19.3/css/default.css";s:4:"0b52";s:30:"mod1/mantis-0.19.3/doc/CREDITS";s:4:"ac9f";s:36:"mod1/mantis-0.19.3/doc/CUSTOMIZATION";s:4:"d6d0";s:32:"mod1/mantis-0.19.3/doc/ChangeLog";s:4:"4f30";s:30:"mod1/mantis-0.19.3/doc/INSTALL";s:4:"d0f6";s:30:"mod1/mantis-0.19.3/doc/LICENSE";s:4:"393a";s:29:"mod1/mantis-0.19.3/doc/README";s:4:"f74b";s:32:"mod1/mantis-0.19.3/doc/UPGRADING";s:4:"160a";s:50:"mod1/mantis-0.19.3/graphs/graph_assigned_to_me.php";s:4:"64fc";s:47:"mod1/mantis-0.19.3/graphs/graph_by_category.php";s:4:"f1a2";s:49:"mod1/mantis-0.19.3/graphs/graph_by_cumulative.php";s:4:"03b7";s:50:"mod1/mantis-0.19.3/graphs/graph_by_daily_delta.php";s:4:"d40d";s:52:"mod1/mantis-0.19.3/graphs/graph_by_release_delta.php";s:4:"b3c7";s:47:"mod1/mantis-0.19.3/graphs/graph_by_severity.php";s:4:"3673";s:54:"mod1/mantis-0.19.3/graphs/graph_by_severity_status.php";s:4:"435a";s:50:"mod1/mantis-0.19.3/graphs/graph_reported_by_me.php";s:4:"c649";s:40:"mod1/mantis-0.19.3/images/attachment.png";s:4:"16f8";s:35:"mod1/mantis-0.19.3/images/blank.gif";s:4:"a533";s:37:"mod1/mantis-0.19.3/images/dollars.gif";s:4:"6fc7";s:34:"mod1/mantis-0.19.3/images/down.gif";s:4:"ea51";s:40:"mod1/mantis-0.19.3/images/excel2icon.gif";s:4:"36d7";s:39:"mod1/mantis-0.19.3/images/excelicon.gif";s:4:"d8b1";s:38:"mod1/mantis-0.19.3/images/fileicon.gif";s:4:"a8cd";s:37:"mod1/mantis-0.19.3/images/gificon.gif";s:4:"b67b";s:38:"mod1/mantis-0.19.3/images/htmlicon.gif";s:4:"338c";s:36:"mod1/mantis-0.19.3/images/ieicon.gif";s:4:"800d";s:37:"mod1/mantis-0.19.3/images/jpgicon.gif";s:4:"3305";s:38:"mod1/mantis-0.19.3/images/mailicon.gif";s:4:"098b";s:42:"mod1/mantis-0.19.3/images/mantis_space.gif";s:4:"fc94";s:35:"mod1/mantis-0.19.3/images/minus.png";s:4:"b323";s:36:"mod1/mantis-0.19.3/images/notice.gif";s:4:"dbea";s:32:"mod1/mantis-0.19.3/images/ok.gif";s:4:"3988";s:38:"mod1/mantis-0.19.3/images/pdf2icon.gif";s:4:"1a56";s:37:"mod1/mantis-0.19.3/images/pdficon.gif";s:4:"9cb8";s:34:"mod1/mantis-0.19.3/images/plus.png";s:4:"3c48";s:37:"mod1/mantis-0.19.3/images/pngicon.gif";s:4:"4c2b";s:37:"mod1/mantis-0.19.3/images/ppticon.gif";s:4:"dbd0";s:40:"mod1/mantis-0.19.3/images/priority_1.gif";s:4:"34eb";s:40:"mod1/mantis-0.19.3/images/priority_2.gif";s:4:"5ad1";s:40:"mod1/mantis-0.19.3/images/priority_3.gif";s:4:"a488";s:44:"mod1/mantis-0.19.3/images/priority_low_1.gif";s:4:"d48d";s:44:"mod1/mantis-0.19.3/images/priority_low_2.gif";s:4:"5116";s:44:"mod1/mantis-0.19.3/images/priority_low_3.gif";s:4:"0f43";s:39:"mod1/mantis-0.19.3/images/protected.gif";s:4:"8e5a";s:43:"mod1/mantis-0.19.3/images/rel_dependant.png";s:4:"2ffe";s:43:"mod1/mantis-0.19.3/images/rel_duplicate.png";s:4:"a457";s:41:"mod1/mantis-0.19.3/images/rel_related.png";s:4:"9e63";s:38:"mod1/mantis-0.19.3/images/synthese.gif";s:4:"d8e2";s:40:"mod1/mantis-0.19.3/images/synthgraph.gif";s:4:"6d51";s:38:"mod1/mantis-0.19.3/images/texticon.gif";s:4:"f3fa";s:41:"mod1/mantis-0.19.3/images/unknownicon.gif";s:4:"b7ea";s:36:"mod1/mantis-0.19.3/images/unread.gif";s:4:"8440";s:32:"mod1/mantis-0.19.3/images/up.gif";s:4:"c4b1";s:36:"mod1/mantis-0.19.3/images/update.png";s:4:"b468";s:39:"mod1/mantis-0.19.3/images/word2icon.gif";s:4:"0c2a";s:38:"mod1/mantis-0.19.3/images/wordicon.gif";s:4:"31f4";s:37:"mod1/mantis-0.19.3/images/zipicon.gif";s:4:"e47b";s:39:"mod1/mantis-0.19.3/javascript/common.js";s:4:"6a17";s:54:"mod1/mantis-0.19.3/lang/strings_chinese_simplified.txt";s:4:"428c";s:55:"mod1/mantis-0.19.3/lang/strings_chinese_traditional.txt";s:4:"826c";s:44:"mod1/mantis-0.19.3/lang/strings_croatian.txt";s:4:"8a53";s:41:"mod1/mantis-0.19.3/lang/strings_czech.txt";s:4:"1db5";s:42:"mod1/mantis-0.19.3/lang/strings_danish.txt";s:4:"9415";s:41:"mod1/mantis-0.19.3/lang/strings_dutch.txt";s:4:"e909";s:43:"mod1/mantis-0.19.3/lang/strings_english.txt";s:4:"90cf";s:44:"mod1/mantis-0.19.3/lang/strings_estonian.txt";s:4:"d9f5";s:43:"mod1/mantis-0.19.3/lang/strings_finnish.txt";s:4:"9ca1";s:42:"mod1/mantis-0.19.3/lang/strings_french.txt";s:4:"003a";s:42:"mod1/mantis-0.19.3/lang/strings_german.txt";s:4:"a443";s:45:"mod1/mantis-0.19.3/lang/strings_hungarian.txt";s:4:"6f98";s:43:"mod1/mantis-0.19.3/lang/strings_italian.txt";s:4:"1b87";s:48:"mod1/mantis-0.19.3/lang/strings_japanese_euc.txt";s:4:"e547";s:49:"mod1/mantis-0.19.3/lang/strings_japanese_sjis.txt";s:4:"9ad0";s:49:"mod1/mantis-0.19.3/lang/strings_japanese_utf8.txt";s:4:"747d";s:42:"mod1/mantis-0.19.3/lang/strings_korean.txt";s:4:"e8c2";s:47:"mod1/mantis-0.19.3/lang/strings_korean_utf8.txt";s:4:"717d";s:43:"mod1/mantis-0.19.3/lang/strings_latvian.txt";s:4:"db27";s:46:"mod1/mantis-0.19.3/lang/strings_lithuanian.txt";s:4:"6c64";s:45:"mod1/mantis-0.19.3/lang/strings_norwegian.txt";s:4:"e97c";s:42:"mod1/mantis-0.19.3/lang/strings_polish.txt";s:4:"9a1c";s:53:"mod1/mantis-0.19.3/lang/strings_portuguese_brazil.txt";s:4:"2b44";s:55:"mod1/mantis-0.19.3/lang/strings_portuguese_standard.txt";s:4:"cb57";s:44:"mod1/mantis-0.19.3/lang/strings_romanian.txt";s:4:"bda4";s:43:"mod1/mantis-0.19.3/lang/strings_russian.txt";s:4:"6e3b";s:48:"mod1/mantis-0.19.3/lang/strings_russian_koi8.txt";s:4:"ac19";s:43:"mod1/mantis-0.19.3/lang/strings_serbian.txt";s:4:"e3e7";s:42:"mod1/mantis-0.19.3/lang/strings_slovak.txt";s:4:"d91f";s:43:"mod1/mantis-0.19.3/lang/strings_slovene.txt";s:4:"8a1d";s:43:"mod1/mantis-0.19.3/lang/strings_spanish.txt";s:4:"3886";s:43:"mod1/mantis-0.19.3/lang/strings_swedish.txt";s:4:"f1fc";s:43:"mod1/mantis-0.19.3/lang/strings_turkish.txt";s:4:"01ed";s:45:"mod1/mantis-0.19.3/lang/strings_ukrainian.txt";s:4:"2e7b";s:39:"mod1/mantis-0.19.3/packages/mantis.spec";s:4:"a3fc";s:38:"mod1/mantis-0.19.3/sql/db_generate.sql";s:4:"b242";s:32:"mod1/mantis-0.19.3/sql/mssql.sql";s:4:"f095";s:32:"mod1/mantis-0.19.3/sql/pgsql.sql";s:4:"d4e8";}',
);

?>