<?php
	$MCONF["name"]="tools_txftpbrowserM1";
	$MCONF["access"]="admin";
	$MCONF["script"]="index.php";
	$MCONF['ftpBrowserSubDir'] = 'ftpbrowser-2.0/';
	
	$MLANG["default"]["tabs_images"]["tab"] = "moduleicon.gif";
	$MLANG["default"]["ll_ref"]="LLL:EXT:ftpbrowser/mod1/locallang_mod.php";

	define('TYPO3_MOD_PATH', '../typo3conf/ext/ftpbrowser/mod1/' . ($MCONF['extModInclude'] ? $MCONF['ftpBrowserSubDir'] : ''));
	$BACK_PATH = ($MCONF['extModInclude'] ? '../' : '') . '../../../../typo3/';
?>
