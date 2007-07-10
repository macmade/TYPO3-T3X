<?php

########################################################################
# Extension Manager/Repository config file for ext: "rtehtmlarea_openoffice"
#
# Auto generated 08-05-2006 15:04
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'HTMLArea RTE / OpenOffice Skin',
	'description' => 'An skin based on OpenOffice icons  by Novell for HTMLArea RTE 1.1.4+',
	'category' => 'be',
	'shy' => 0,
	'dependencies' => 'cms,lang,rtehtmlarea',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
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
	'version' => '0.1.0',
	'_md5_values_when_last_written' => 'a:79:{s:21:"ext_conf_template.txt";s:4:"0665";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"534a";s:14:"doc/manual.sxw";s:4:"488f";s:31:"ooo/htmlarea-edited-content.css";s:4:"8571";s:16:"ooo/htmlarea.css";s:4:"4798";s:17:"ooo/separator.gif";s:4:"23ed";s:23:"ooo/images/ed_about.gif";s:4:"8a93";s:30:"ooo/images/ed_align_center.gif";s:4:"b1a8";s:31:"ooo/images/ed_align_justify.gif";s:4:"d021";s:28:"ooo/images/ed_align_left.gif";s:4:"8f78";s:29:"ooo/images/ed_align_right.gif";s:4:"2872";s:21:"ooo/images/ed_all.gif";s:4:"42e8";s:23:"ooo/images/ed_blank.gif";s:4:"10b6";s:25:"ooo/images/ed_charmap.gif";s:4:"a9ba";s:26:"ooo/images/ed_color_bg.gif";s:4:"d3e6";s:26:"ooo/images/ed_color_fg.gif";s:4:"5783";s:22:"ooo/images/ed_copy.gif";s:4:"2322";s:24:"ooo/images/ed_custom.gif";s:4:"e7b2";s:21:"ooo/images/ed_cut.gif";s:4:"61c6";s:24:"ooo/images/ed_delete.gif";s:4:"926b";s:29:"ooo/images/ed_format_bold.gif";s:4:"4d3e";s:31:"ooo/images/ed_format_italic.gif";s:4:"7854";s:31:"ooo/images/ed_format_strike.gif";s:4:"2903";s:28:"ooo/images/ed_format_sub.gif";s:4:"9a0e";s:28:"ooo/images/ed_format_sup.gif";s:4:"de5e";s:34:"ooo/images/ed_format_underline.gif";s:4:"cc90";s:22:"ooo/images/ed_help.gif";s:4:"08f0";s:20:"ooo/images/ed_hr.gif";s:4:"ab06";s:22:"ooo/images/ed_html.gif";s:4:"300f";s:23:"ooo/images/ed_image.gif";s:4:"f214";s:29:"ooo/images/ed_indent_less.gif";s:4:"754e";s:29:"ooo/images/ed_indent_more.gif";s:4:"d058";s:31:"ooo/images/ed_left_to_right.gif";s:4:"bc93";s:22:"ooo/images/ed_link.gif";s:4:"3edc";s:29:"ooo/images/ed_list_bullet.gif";s:4:"9a9e";s:26:"ooo/images/ed_list_num.gif";s:4:"3a11";s:23:"ooo/images/ed_paste.gif";s:4:"d612";s:22:"ooo/images/ed_redo.gif";s:4:"659b";s:31:"ooo/images/ed_right_to_left.gif";s:4:"0987";s:28:"ooo/images/ed_splitblock.gif";s:4:"503e";s:26:"ooo/images/ed_splitcel.gif";s:4:"2c04";s:22:"ooo/images/ed_undo.gif";s:4:"40d7";s:24:"ooo/images/ed_unlink.gif";s:4:"bf27";s:27:"ooo/images/insert_table.gif";s:4:"f2d3";s:33:"ooo/images/Acronym/ed_acronym.gif";s:4:"7435";s:38:"ooo/images/CharacterMap/ed_charmap.gif";s:4:"fe54";s:34:"ooo/images/FindReplace/ed_find.gif";s:4:"4c9c";s:37:"ooo/images/InsertSmiley/ed_smiley.gif";s:4:"2093";s:35:"ooo/images/QuickTag/ed_quicktag.gif";s:4:"577b";s:36:"ooo/images/RemoveFormat/ed_clean.gif";s:4:"26b5";s:39:"ooo/images/SelectColor/CO-forecolor.gif";s:4:"5783";s:41:"ooo/images/SelectColor/CO-hilitecolor.gif";s:4:"d3e6";s:39:"ooo/images/SpellChecker/spell-check.gif";s:4:"4384";s:42:"ooo/images/TableOperations/cell-delete.gif";s:4:"db47";s:48:"ooo/images/TableOperations/cell-insert-after.gif";s:4:"4163";s:49:"ooo/images/TableOperations/cell-insert-before.gif";s:4:"9ed2";s:41:"ooo/images/TableOperations/cell-merge.gif";s:4:"feef";s:40:"ooo/images/TableOperations/cell-prop.gif";s:4:"0622";s:41:"ooo/images/TableOperations/cell-split.gif";s:4:"587f";s:41:"ooo/images/TableOperations/col-delete.gif";s:4:"d9bc";s:47:"ooo/images/TableOperations/col-insert-after.gif";s:4:"6a76";s:48:"ooo/images/TableOperations/col-insert-before.gif";s:4:"8845";s:40:"ooo/images/TableOperations/col-split.gif";s:4:"e986";s:41:"ooo/images/TableOperations/row-delete.gif";s:4:"812b";s:47:"ooo/images/TableOperations/row-insert-above.gif";s:4:"a4cc";s:47:"ooo/images/TableOperations/row-insert-under.gif";s:4:"2d19";s:39:"ooo/images/TableOperations/row-prop.gif";s:4:"6e36";s:40:"ooo/images/TableOperations/row-split.gif";s:4:"4f6f";s:41:"ooo/images/TableOperations/table-prop.gif";s:4:"5be3";s:48:"ooo/images/TableOperations/toggle-borders-16.gif";s:4:"b425";s:45:"ooo/images/TableOperations/toggle-borders.gif";s:4:"46df";s:40:"ooo/images/TYPO3Browsers/ed_image-16.gif";s:4:"8f9c";s:37:"ooo/images/TYPO3Browsers/ed_image.gif";s:4:"f214";s:39:"ooo/images/TYPO3Browsers/ed_link-16.gif";s:4:"d8af";s:36:"ooo/images/TYPO3Browsers/ed_link.gif";s:4:"3edc";s:41:"ooo/images/TYPO3Browsers/ed_unlink-16.gif";s:4:"31a2";s:38:"ooo/images/TYPO3Browsers/ed_unlink.gif";s:4:"bf27";s:35:"ooo/images/UserElements/ed_user.gif";s:4:"7db7";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'rtehtmlarea' => '',
			'php' => '3.0.0-',
			'typo3' => '3.5.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>